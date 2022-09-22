<?php

namespace App\Http\Controllers;

use App\Customer;
use App\Expense;
use App\Exports\ReportTemplate;
use App\Lagoon;
use App\LagoonPerKilo;
use App\PartialPayment;
use App\ProductPurchase;
use App\ProductTransactionItem;
use App\ServiceTransactionItem;
use App\TransactionPayment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ExcelController extends Controller
{
    public function customRange(Request $request, $download = false) {
        $result = [];
        $posTransactions = DB::table('transactions')
            ->where('saved', true)
            ->whereNull('deleted_at')
            ->whereBetween(DB::raw('DATE(date)'), [$request['dateFrom'], $request['dateTo']])
            ->groupBy(DB::raw('day'))
            ->selectRaw('DATE(' . 'date' . ') as day, SUM(total_price) as total_price, SUM(1) as total_jo, SUM(IF(date_paid IS NULL, 0, 1)) as paid_jo')
            ->get();

        $rfidCardTransactions = DB::table('rfid_card_transactions')
            ->where('card_type', 'u')
            ->whereNull('deleted_at')
            ->whereBetween(DB::raw('DATE(created_at)'), [$request['dateFrom'], $request['dateTo']])
            ->groupBy(DB::raw('day'))
            ->selectRaw('DATE(created_at) as day, SUM(price) as total_price, SUM(IF(card_type = "u", price, 0)) as collection')
            ->get();

        $rfidLoadTransactions = DB::table('rfid_load_transactions')
            ->whereBetween(DB::raw('DATE(created_at)'), [$request['dateFrom'], $request['dateTo']])
            ->whereNull('deleted_at')
            ->groupBy(DB::raw('day'))
            ->selectRaw('DATE(created_at) as day, SUM(amount) as total_price, SUM(amount) as collection')
            ->get();

        $newCustomers = Customer::whereBetween(DB::raw('DATE(first_visit)'), [$request['dateFrom'], $request['dateTo']])
            ->groupBy(DB::raw('day'))
            ->selectRaw('DATE(first_visit) as day, COUNT(id) as total_count')
            ->get();

        $expenses = Expense::whereBetween(DB::raw('DATE(date)'), [$request['dateFrom'], $request['dateTo']])
            ->groupBy(DB::raw('day'))
            ->selectRaw('DATE(date) as day, SUM(amount) as expense')
            ->get();

        $productPurchases = ProductPurchase::where('unit_cost', '>', '0')
            ->whereBetween(DB::raw('DATE(date)'), [$request['dateFrom'], $request['dateTo']])
            ->groupBy(DB::raw('day'))
            ->selectRaw('DATE(date) as day, SUM(unit_cost * quantity) as product_purchase')
            ->get();

        $partialPayments = PartialPayment::whereBetween(DB::raw('DATE(created_at)'), [$request['dateFrom'], $request['dateTo']])
            ->groupBy(DB::raw('day'))
            ->select(DB::raw('DATE(created_at) as day, SUM(`cash` - `change`) as collection'))
            ->get();

        $transactionPayment = TransactionPayment::whereBetween(DB::raw('DATE(created_at)'), [$request['dateFrom'], $request['dateTo']])
            ->groupBy(DB::raw('day'))
            // ->selectRaw('DATE(created_at) as day, SUM((`total_amount` - (`total_amount` /  100 * `discount`)) - `points_in_peso` - `card_load_used` - COALESCE((SELECT cash FROM partial_payments WHERE partial_payments.transaction_id = transaction_payments.id), 0) ) as collection')
            ->select(DB::raw('DATE(created_at) as day, SUM(`cash` - `change`) as collection'))
            ->get();

        $result = array_merge(
            $posTransactions->toArray(),
            $rfidCardTransactions->toArray(),
            $rfidLoadTransactions->toArray(),
            $expenses->toArray(),
            $productPurchases->toArray(),
            $newCustomers->toArray(),
            $transactionPayment->toArray(),
            $partialPayments->toArray()
        );
        $result = collect($result)->groupBy('day');

        $productNames = ProductTransactionItem::whereBetween(DB::raw('DATE(created_at)'), [$request['dateFrom'], $request['dateTo']])
            ->distinct('name')
            ->select('name')
            ->orderBy('name')
            ->pluck('name')
            ->toArray();

        $products = ProductTransactionItem::whereBetween(DB::raw('DATE(created_at)'), [$request['dateFrom'], $request['dateTo']])
            ->groupBy('name', DB::raw('day'))
            ->select(DB::raw('DATE(created_at) as day, name, COUNT(name) as quantity'))
            ->orderBy('day')
            ->get();

        $serviceNames = ServiceTransactionItem::whereBetween(DB::raw('DATE(created_at)'), [$request['dateFrom'], $request['dateTo']])
            ->distinct('name')
            ->select('name')
            ->orderBy('name')
            ->pluck('name')
            ->toArray();

        $services = ServiceTransactionItem::whereBetween(DB::raw('DATE(created_at)'), [$request['dateFrom'], $request['dateTo']])
            ->groupBy('name', DB::raw('day'))
            ->select(DB::raw('DATE(created_at) as day, name, COUNT(name) as quantity'))
            ->orderBy('day')
            ->get();


        // foreach($dates as $date) {
        //     $productItem = [];
        //     $productItem['day'] = $date;
        //     foreach($productNames as $productName) {
        //         $productItem[$productName] = $products->filter(function($item) use ($date, $productName) {
        //             return $item->day == $date && $item->name == $productName;
        //         })->sum('quantity');
        //     }
        //     $productItems[] = $productItem;
        // }

        $result = $result->transform(function($item, $key) use ($products, $productNames, $services, $serviceNames) {
            $totalCollections = $item->sum('collection');
            $totalExpenses =  $item->sum('expense') + $item->sum('product_purchase');

            $productItem = [];
            $serviceItem = [];

            foreach($productNames as $productName) {
                $productItem[$productName] = $products->filter(function($item) use ($key, $productName) {
                    return $item->day == $key && $item->name == $productName;
                })->sum('quantity');
            }
            foreach($serviceNames as $serviceName) {
                $serviceItem[$serviceName] = $services->filter(function($item) use ($key, $serviceName) {
                    return $item->day == $key && $item->name == $serviceName;
                })->sum('quantity');
            }

            return collect(
            [
                'date' => $key,
                'newCustomers' => $item->sum('total_count'),
                'total_jo' => $item->sum('total_jo'),
                'amount' => $item->sum('total_price'),
                'collection' => $totalCollections,
                // 'paid_jo' => $item->sum('paid_jo'),
                'expenses' => $totalExpenses,
                'totalDeposit' => $totalCollections - $totalExpenses,
            ])->merge($serviceItem)->merge($productItem);
        })->sortBy('date');

        if(count($result) == 0) {
            return response()->json([
                'errors' => [
                    'message' => ['No data available']
                ]
            ], 422);
        }

        // return response()->json($productItems);

        if($download) {
            return Excel::download(new ReportTemplate($result, array_merge([
                'DATE',
                'NEW CUSTOMER',
                'JOB ORDERS',
                'SALES',
                'COLLECTIONS',
                'EXPENSES',
                'DEPOSIT',
            ], $serviceNames, $productNames)), 'parts.xls');
        }

        return response()->json([
            'result' => array_values($result->toArray()),
        ]);
    }

    public function exportServices() {
        $lagoonPerKilo = LagoonPerKilo::orderBy('name')
            ->selectRaw('id, "lagoon /kg" as category, name, price_per_kilo')->get();

        $lagoon = Lagoon::orderBy(DB::raw('category, name'))
            ->selectRaw('id, category, name, price')->get();


        $scarpa = DB::table('scarpa_categories')
            ->join('scarpa_variations', 'scarpa_category_id', '=', 'scarpa_categories.id')
            ->selectRaw('scarpa_variations.id as id, name as category, action as name, selling_price as price')
            ->get();

        $services = $scarpa->merge($lagoon)->merge($lagoonPerKilo);
        
        return Excel::download(new ReportTemplate($services, [
            'ID', 'CATEGORY', 'NAME', 'PRICE'
        ]), 'keme.csv');
    }
}
