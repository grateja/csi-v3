<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ProductPurchase;
use Illuminate\Support\Facades\DB;
use App\Expense;
use Illuminate\Support\Carbon;
use App\CompletedServiceTransaction;
use App\CompletedProductTransaction;
use App\RfidLoadTransaction;
use App\RfidTransaction;
use App\Customer;

class DashboardController extends Controller
{
    public function index($branchId, Request $request) {
        $branchId = $branchId == 'self' ? auth('api')->user()->active_branch_id : $branchId;

        $dateFrom = $request->dateFrom ? $request->dateFrom : Carbon::create(Carbon::today()->year . '-01-01');
        $dateTo = $request->dateTo ? $request->dateTo : Carbon::now();

        $purchases = ProductPurchase::whereBetween('date', [$dateFrom, $dateTo])
            ->whereHas('branchProduct', function($query) use ($branchId) {
                $query->where('branch_id', $branchId);
            })->with(['branchProduct' => function($query) {
                $query->with(['product' => function($query) {
                    $query->select('name', 'id');
                }])->select('id', 'product_id');
            }])
            ->selectRaw('branch_product_id, DATE_FORMAT(date, "%M %Y") as monthName, MONTH(date) as monthIndex, SUM(unit_cost * quantity) as totalCost')
            ->groupBy(DB::raw('monthName, monthIndex, branch_product_id'))
            ->orderBy('monthIndex')
            ->get();


        $expenses = Expense::whereBetween('date', [$dateFrom, $dateTo])
            ->where('branch_id', $branchId)
            ->selectRaw('DATE_FORMAT(date, "%M %Y") as monthName, MONTH(date) as monthIndex, SUM(amount) as totalCost')
            ->groupBy(DB::raw('monthName, monthIndex'))
            ->orderBy('monthIndex')
            ->get();

        $services = CompletedServiceTransaction::whereBetween('date_paid', [$dateFrom, $dateTo])
            ->where('branch_id', $branchId)
            ->whereNotNull('date_paid')
            ->selectRaw('DATE_FORMAT(date_paid, "%M %Y") as monthName, MONTH(date_paid) as monthIndex, SUM(price_sum * quantity) as totalAmount')
            ->groupBy(DB::raw('monthName, monthIndex'))
            ->orderBy('monthIndex')
            ->get();

        $products = CompletedProductTransaction::whereBetween('date_paid', [$dateFrom, $dateTo])
            ->where('branch_id', $branchId)
            ->whereNotNull('date_paid')
            ->selectRaw('DATE_FORMAT(date_paid, "%M %Y") as monthName, MONTH(date_paid) as monthIndex, SUM(price_sum * quantity) as totalAmount')
            ->groupBy(DB::raw('monthName, monthIndex'))
            ->orderBy('monthIndex')
            ->get();

        $rfidTopUps = RfidLoadTransaction::whereBetween('created_at', [$dateFrom, $dateTo])
            ->whereHas('rfidCard', function($query) use ($branchId) {
                $query->where('branch_id', $branchId);
            })
            ->selectRaw('DATE_FORMAT(created_at, "%M %Y") as monthName, MONTH(created_at) as monthIndex, SUM(amount) as totalAmount')
            ->groupBy(DB::raw('monthName, monthIndex'))
            ->orderBy('monthIndex')
            ->get();

        // $rfidTransactions = RfidTransaction::whereBetween('created_at', [$dateFrom, $dateTo])
        //     ->where('branch_id', $branchId)
        //     ->selectRaw('DATE_FORMAT(created_at, "%M %Y") as monthName, MONTH(created_at) as monthIndex, SUM(price) as totalAmount')
        //     ->groupBy(DB::raw('monthName, monthIndex'))
        //     ->orderBy('monthIndex')
        //     ->get();

        $customers = Customer::whereBetween('created_at', [$dateFrom, $dateTo])
            ->where('branch_id', $branchId)
            ->selectRaw('DATE_FORMAT(created_at, "%M %Y") as monthName, MONTH(created_at) as monthIndex, COUNT(id) as totalNewCustomers')
            ->groupBy(DB::raw('monthName, monthIndex'))
            ->orderBy('monthIndex')
            ->get();

        $months = [];
        for ($i=1; $i <= 12; $i++) {

            // expenses
            $purchaseExpense = $purchases->where('monthIndex', $i)->sum('totalCost');
            $expenseExpenses = $expenses->where('monthIndex', $i)->sum('totalCost');

            // income
            $servicesIncome = $services->where('monthIndex', $i)->sum('totalAmount');
            $productsIncome = $products->where('monthIndex', $i)->sum('totalAmount');
            $rfidTopUpsIncome = $rfidTopUps->where('monthIndex', $i)->sum('totalAmount');
            // $rfidTransactionsIncome = $rfidTransactions->where('monthIndex', $i)->sum('totalAmount');
            $newCustomers = $customers->where('monthIndex', $i)->sum('totalNewCustomers');

            $months[] = [
                'index' => $i,
                'monthName' => date('F' , mktime(0,0,0,$i, 1)),
                'totalExpenses' => array_sum([$purchaseExpense, $expenseExpenses]),
                'totalIncome' => array_sum([$servicesIncome, $productsIncome, $rfidTopUpsIncome/*, $rfidTransactionsIncome */]),
                'newCustomers' => $newCustomers,
            ];
        }

        return response()->json([
            'result' => [
                // 'purchases' => $purchases->groupBy('monthIndex'),
                // 'expenses' => $expenses->groupBy('monthIndex'),
                // 'services' => $services->groupBy('monthIndex'),
                // 'products' => $products->groupBy('monthIndex'),
                // 'rfidTopUps' => $rfidTopUps->groupBy('monthIndex'),
                // 'rfidTransaction' => $rfidTransactions->groupBy('monthIndex'),
                'customers' => $customers,
                'months' => $months,
                'maxExpense' => round(collect($months)->max('totalExpenses') / 1000),
                'maxIncome' => round(collect($months)->max('totalIncome') / 1000),
            ],
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo
        ], 200);
    }

    public function expenses($branchId, Request $request) {

        $branchId = $branchId == 'self' ? auth('api')->user()->active_branch_id : $branchId;
        $year = $request->year ? $request->year : Carbon::now()->year;

        $purchases = ProductPurchase::whereMonth('date', $request->month)->whereYear('date', $year)
            ->whereHas('branchProduct', function($query) use ($branchId) {
                $query->where('branch_id', $branchId);
            })
            ->sum(DB::raw('unit_cost * quantity'));


        $expenses = Expense::whereMonth('date', $request->month)->whereYear('date', $year)
            ->where('branch_id', $branchId)->sum('amount');

        $result = [
            [
                'id' => 'expenses',
                'data' => $expenses,
                'label' => 'Expenses',
            ],
            [
                'id' => 'purchases',
                'data' => $purchases,
                'label' => 'Purchases',
            ]
        ];

        return response()->json([
            'result' => $result,
        ], 200);
    }

    public function income($branchId, Request $request) {

        $branchId = $branchId == 'self' ? auth('api')->user()->active_branch_id : $branchId;
        $year = $request->year ? $request->year : Carbon::now()->year;

        $services = CompletedServiceTransaction::whereMonth('date_paid', $request->month)->whereYear('date_paid', $year)
            ->whereNotNull('date_paid')
            ->where('branch_id', $branchId)->sum(DB::raw('price_sum * quantity'));
            // ->selectRaw('DATE_FORMAT(date_paid, "%M %Y") as monthName, MONTH(date_paid) as monthIndex, SUM(price_sum * quantity) as totalAmount')
            // ->groupBy(DB::raw('monthName, monthIndex'))
            // ->orderBy('monthIndex')
            // ->get();

        $products = CompletedProductTransaction::whereMonth('date_paid', $request->month)->whereYear('date_paid', $year)
            ->where('branch_id', $branchId)
            ->whereNotNull('date_paid')
            ->sum(DB::raw('price_sum * quantity'));
            // ->selectRaw('DATE_FORMAT(date_paid, "%M %Y") as monthName, MONTH(date_paid) as monthIndex, SUM(price_sum * quantity) as totalAmount')
            // ->groupBy(DB::raw('monthName, monthIndex'))
            // ->orderBy('monthIndex')
            // ->get();

        $rfidTopUps = RfidLoadTransaction::whereMonth('created_at', $request->month)->whereYear('created_at', $year)
            ->whereHas('rfidCard', function($query) use ($branchId) {
                $query->where('branch_id', $branchId);
            })->sum(DB::raw('amount'));
            // ->selectRaw('DATE_FORMAT(created_at, "%M %Y") as monthName, MONTH(created_at) as monthIndex, SUM(amount) as totalAmount')
            // ->groupBy(DB::raw('monthName, monthIndex'))
            // ->orderBy('monthIndex')
            // ->get();

        // $rfidTransactions = RfidTransaction::whereMonth('created_at', $request->month)->whereYear('created_at', $year)
        //     ->where('branch_id', $branchId)
        //     ->sum(DB::raw('price'));
            // ->selectRaw('DATE_FORMAT(created_at, "%M %Y") as monthName, MONTH(created_at) as monthIndex, SUM(price) as totalAmount')
            // ->groupBy(DB::raw('monthName, monthIndex'))
            // ->orderBy('monthIndex')
            // ->get();

        $result = [
            [
                'id' => 'services',
                'data' => $services,
                'label' => 'Services',
            ],
            [
                'id' => 'products',
                'data' => $products,
                'label' => 'Products',
            ],
            [
                'id' => 'rfidTopUps',
                'data' => $rfidTopUps,
                'label' => 'RFID Top ups',
            ],
            // [
            //     'id' => 'rfidTransactions',
            //     'data' => $rfidTransactions,
            //     'label' => 'RFID Transactions',
            // ],
        ];

        return response()->json([
            'result' => $result,
        ], 200);

    }

    public function liquidateExpenses($branchId, Request $request) {
        $branchId = $branchId == 'self' ? auth('api')->user()->active_branch_id : $branchId;
        $year = $request->year ? $request->year : Carbon::now()->year;

        $expenses = Expense::whereMonth('date', $request->month)->whereYear('date', $year)
            ->where('branch_id', $branchId)
            ->selectRaw('DATE_FORMAT(date, "%M %Y") as monthName, MONTH(date) as monthIndex, SUM(amount) as totalCost, expense_type')
            ->groupBy(DB::raw('monthName, monthIndex, expense_type'))
            ->orderBy('monthIndex')
            ->get();

        $expenses = collect($expenses)->transform(function($item) {
            return [
                'id' => $item->expense_type,
                'label' => $item->expense_type,
                'totalCost' => $item->totalCost,
            ];
        });


        return response()->json([
            'result' => $expenses,
            'totalCost' => $expenses->sum('totalCost')
        ], 200);
    }

    public function liquidatePurchases($branchId, Request $request) {
        $branchId = $branchId == 'self' ? auth('api')->user()->active_branch_id : $branchId;
        $year = $request->year ? $request->year : Carbon::now()->year;

        $purchases = ProductPurchase::whereMonth('date', $request->month)->whereYear('date', $year)
            ->whereHas('branchProduct', function($query) use ($branchId) {
                $query->where('branch_id', $branchId);
            })->with(['branchProduct' => function($query) {
                $query->with(['product' => function($query) {
                    $query->select('name', 'id');
                }])->select('id', 'product_id');
            }])
            ->selectRaw('branch_product_id, DATE_FORMAT(date, "%M %Y") as monthName, MONTH(date) as monthIndex, SUM(unit_cost * quantity) as totalCost')
            ->groupBy(DB::raw('monthName, monthIndex, branch_product_id'))
            ->orderBy('monthIndex')
            ->get();

        $purchases = collect($purchases)->transform(function($item) {
            return [
                'id' => $item->branch_product_id,
                'label' => $item->branchProduct->product['name'],
                'totalCost' => $item->totalCost,
            ];
        });

        return response()->json([
            'result' => $purchases,
            'totalCost' => $purchases->sum('totalCost'),
        ], 200);
    }

    public function liquidateServices($branchId, Request $request) {
        $branchId = $branchId == 'self' ? auth('api')->user()->active_branch_id : $branchId;
        $year = $request->year ? $request->year : Carbon::now()->year;

        $services = CompletedServiceTransaction::whereMonth('date_paid', $request->month)->whereYear('date_paid', $year)
            ->with('branchService')
            ->where('branch_id', $branchId)
            ->whereNotNull('date_paid')
            ->selectRaw('branch_service_id, DATE_FORMAT(date_paid, "%M %Y") as monthName, MONTH(date_paid) as monthIndex, SUM(price_sum * quantity) as totalAmount')
            ->groupBy(DB::raw('monthName, monthIndex, branch_service_id'))
            ->orderBy('monthIndex')
            ->get();

        $services = collect($services)->transform(function($item) {
            return [
                'id' => $item->branch_service_id,
                'label' => $item->branchService->service['name'],
                'totalAmount' => $item->totalAmount,
            ];
        });

        return response()->json([
            'result' => $services,
            'totalAmount' => $services->sum('totalAmount'),
        ], 200);
    }

    public function liquidateProducts($branchId, Request $request) {
        $branchId = $branchId == 'self' ? auth('api')->user()->active_branch_id : $branchId;
        $year = $request->year ? $request->year : Carbon::now()->year;

        $products = CompletedProductTransaction::whereMonth('date_paid', $request->month)->whereYear('date_paid', $year)
            ->with('branchProduct')
            ->where('branch_id', $branchId)
            ->whereNotNull('date_paid')
            ->selectRaw('branch_product_id, DATE_FORMAT(date_paid, "%M %Y") as monthName, MONTH(date_paid) as monthIndex, SUM(price_sum * quantity) as totalAmount')
            ->groupBy(DB::raw('monthName, monthIndex, branch_product_id'))
            ->orderBy('monthIndex')
            ->get();

        $products = collect($products)->transform(function($item) {
            return [
                'id' => $item->branch_product_id,
                'label' => $item->branchProduct->product['name'],
                'totalAmount' => $item->totalAmount,
            ];
        });

        return response()->json([
            'result' => $products,
            'totalAmount' => $products->sum('totalAmount'),
        ], 200);
    }

    public function liquidateRfidTopups($branchId, Request $request) {
        $branchId = $branchId == 'self' ? auth('api')->user()->active_branch_id : $branchId;
        $year = $request->year ? $request->year : Carbon::now()->year;

        $rfidTopUps = RfidLoadTransaction::whereMonth('created_at', $request->month)->whereYear('created_at', $year)
            ->whereHas('rfidCard', function($query) use ($branchId) {
                $query->where('branch_id', $branchId);
            })->with('rfidCard.customer')
            ->selectRaw('rfid_card_id, DATE_FORMAT(created_at, "%M %Y") as monthName, MONTH(created_at) as monthIndex, SUM(amount) as totalAmount')
            ->groupBy(DB::raw('monthName, monthIndex, rfid_card_id'))
            ->orderByDesc('totalAmount')
            ->limit(10)
            ->get();

        $rfidTopUps = collect($rfidTopUps)->transform(function($item) {
            return [
                'id' => $item->rfid_card_id,
                'label' => $item->rfidCard->customer['name'],
                'totalAmount' => $item->totalAmount,
            ];
        });

        return response()->json([
            'result' => $rfidTopUps,
            'totalAmount' => $rfidTopUps->sum('totalAmount'),
        ], 200);
    }

    public function liquidateRfidTransactions($branchId, Request $request) {
        $branchId = $branchId == 'self' ? auth('api')->user()->active_branch_id : $branchId;
        $year = $request->year ? $request->year : Carbon::now()->year;

        $rfidTransactions = RfidTransaction::whereMonth('created_at', $request->month)->whereYear('created_at', $year)
            ->where('branch_id', $branchId)
            ->with('rfidServicePrice')
            ->selectRaw('rfid_service_price_id, SUM(price) as totalAmount')
            ->groupBy(DB::raw('rfid_service_price_id'))
            ->get();

        $rfidTransactions = collect($rfidTransactions)->transform(function($item) {
            return [
                'id' => $item->rfid_card_id,
                'label' => $item->rfidServicePrice['name'],
                'totalAmount' => $item->totalAmount,
            ];
        });

        return response()->json([
            'result' => $rfidTransactions,
            'totalAmount' => $rfidTransactions->sum('totalAmount'),
        ], 200);
    }


}
