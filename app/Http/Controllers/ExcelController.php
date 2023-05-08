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
use App\Transaction;
use App\OtherService;
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

        $others = OtherService::orderBy('name')
            ->selectRaw('id, "other" as category, name, price')->get();

        $services = $scarpa->merge($lagoon)->merge($lagoonPerKilo)->merge($others);

        return Excel::download(new ReportTemplate($services, [
            'ID', 'CATEGORY', 'NAME', 'PRICE'
        ]), 'keme.csv');
    }

    public function jobOrders(Request $request) {
        // if($request->date && $request->until) {
        //     return Carbon::createFromDate($request->date)->diffInDays($request->until);
        // }

        $order = $request->orderBy ? $request->orderBy : 'desc';
        $sortBy = Transaction::filterKeys($request->sortBy);

        $result = Transaction::with(['serviceTransactionItems', 'payment' => function($query) {
            // $query->select('id', 'date', 'cash','points', 'points_in_peso', 'cash_less_provider', 'discount_name', 'discount', 'total_amount', 'change', 'total_cash');
        }, 'partialPayment' => function($query) {
            $query->select('id', 'transaction_id', 'date', 'total_amount', 'cash', 'balance');
        }])->where(function($query) use ($request) {
            $query->where('customer_name', 'like', "%$request->keyword%")
                ->orWhere('job_order', 'like', "%$request->keyword%");
        })->where('saved', true);

        $result = $result->orderBy($sortBy, $order);

        if($request->date && $request->until) {
            $result = $result->whereDate('date', '>=', $request->date)
                ->whereDate('date', '<=', $request->until);
        } else if($request->date) {
            $result = $result->whereDate('date', $request->date);
        }

        if($request->datePaid && $request->paidUntil) {
            $result = $result->whereDate('date_paid', '>=', $request->datePaid)
                ->whereDate('date_paid', '<=', $request->paidUntil);
        } else if($request->datePaid) {
            $result = $result->whereDate('date_paid', $request->datePaid);
        }

        if($result->count() >= 3000) {
            return response()->json([
                'errors' => [
                    'message' => ['Result too big. Only 3000 result is allowed to export.']
                ]
            ], 422);
        }
        $result = $result->get();


        $cols = [];

        foreach($result as $item) {
            $cols[] = [
                'date_created' => $item->date,
                'job_order' => $item->job_order,
                'customer' => $item->customer_name,
                'service_quantity' => $request->option == 'simplified' ? '' : 'QUANTITY',
                'service_name' =>  $request->option == 'simplified' ? '' : 'SERVICE NAME',
                'service_amount' => $request->option == 'simplified' ? '' : 'AMOUNT',
                'amount' => $item->total_price,
                'discount_name' => $item->payment && $item->payment->discount ? $item->payment->discount_name . "({$item->payment->discount}%)" : '',
                'discount' => $item->payment && $item->payment->discount ? $item->payment->discount_in_peso : '0',
                'discounted_amount' => $item->payment && $item->payment->discount ? $item->total_price - $item->payment->discount_in_peso : $item->total_price,
                'points_used' => $item->payment && $item->payment->points ? "({$item->payment->points}pts.)" . " PHP {$item->payment->points_in_peso}" : '',
                'cash_received' => $item->payment ? $item->payment->collected : ($item->partialPayment ? $item->partialPayment->cash : '0'),
                'date_paid' => $item->date_paid ? $item->date_paid : ($item->partialPayment ? '[PARTIAL]' : '[UNPAID]'),
                'payment_method' => $item->payment ? $item->payment->payment_method : '-',
                'partial_payment' => $item->partialPayment && !$item->payment ? $item->partialPayment->cash : '-',
                'balance' => $item->payment ? '0' : ($item->partialPayment ? $item->partialPayment->balance : $item->total_price),
            ];
            if($request->option == 'simplified') continue;
            foreach($item->posServiceItems() as $serviceItem) {
                $cols[] = [
                    'date_created' => '',
                    'job_order' => '',
                    'customer' => '',
                    'service_quantity' => $serviceItem['quantity'],
                    'service_name' => $serviceItem['name'],
                    'service_amount' => $serviceItem['total_price'],
                    'amount' => '',
                    'discount_name' => '',
                    'discount' => '',
                    'discounted_amount' => '',
                    'cash' => '',
                    'date_paid' => '',
                    'payment_method' => '',
                    'partial_payment' => '',
                    'balance' => '',
                ];
            }
            foreach($item->posProductItems() as $productItem) {
                $cols[] = [
                    'date_created' => '',
                    'job_order' => '',
                    'customer' => '',
                    'service_quantity' => $productItem['quantity'],
                    'service_name' => $productItem['name'],
                    'service_amount' => $productItem['total_price'],
                    'amount' => '',
                    'discount_name' => '',
                    'discount' => '',
                    'discounted_amount' => '',
                    'cash' => '',
                    'date_paid' => '',
                    'payment_method' => '',
                    'partial_payment' => '',
                    'balance' => '',
                ];
            }
            foreach($item->posLagoonItems() as $productItem) {
                $cols[] = [
                    'date_created' => '',
                    'job_order' => '',
                    'customer' => '',
                    'service_quantity' => $productItem['quantity'],
                    'service_name' => $productItem['name'],
                    'service_amount' => $productItem['total_price'],
                    'amount' => '',
                    'discount_name' => '',
                    'discount' => '',
                    'discounted_amount' => '',
                    'cash' => '',
                    'date_paid' => '',
                    'payment_method' => '',
                    'partial_payment' => '',
                    'balance' => '',
                ];
            }
            foreach($item->posLagoonPerKiloItems() as $productItem) {
                $cols[] = [
                    'date_created' => '',
                    'job_order' => '',
                    'customer' => '',
                    'service_quantity' => $productItem['kilos'],
                    'service_name' => $productItem['name'],
                    'service_amount' => $productItem['total_price'],
                    'amount' => '',
                    'discount_name' => '',
                    'discount' => '',
                    'discounted_amount' => '',
                    'cash' => '',
                    'date_paid' => '',
                    'payment_method' => '',
                    'partial_payment' => '',
                    'balance' => '',
                ];
            }
            foreach($item->posScarpaCleaningItems() as $productItem) {
                $cols[] = [
                    'date_created' => '',
                    'job_order' => '',
                    'customer' => '',
                    'service_quantity' => $productItem['quantity'],
                    'service_name' => $productItem['name'],
                    'service_amount' => $productItem['total_price'],
                    'amount' => '',
                    'discount_name' => '',
                    'discount' => '',
                    'discounted_amount' => '',
                    'cash' => '',
                    'date_paid' => '',
                    'payment_method' => '',
                    'partial_payment' => '',
                    'balance' => '',
                ];
            }
        }

        return Excel::download(new ReportTemplate(collect($cols), [
            'DATE CREATED', 'JO#', 'CUSTOMER', $request->option == 'simplified' ? '' : 'SERVICES', '', '', 'TOTAL AMOUNT', 'DISCOUNT NAME', 'DISCOUNT', 'DISCOUNTED AMOUNT','POINTS USED', 'COLLECTION', 'DATE PAID', 'PAYMENT METHOD', 'PARTIAL PAYMENT', 'BALANCE',
        ]), 'job-orders.csv');
    }
}
