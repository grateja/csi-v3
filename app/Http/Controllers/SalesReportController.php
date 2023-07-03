<?php

namespace App\Http\Controllers;

use App\Customer;
use App\Expense;
use App\LagoonPerKiloTransactionItem;
use App\LagoonTransactionItem;
use App\Machine;
use App\MonthlyTarget;
use App\PartialPayment;
use App\ProductPurchase;
use App\ProductTransactionItem;
use App\RfidCardTransaction;
use App\RfidLoadTransaction;
use App\ServiceTransactionItem;
use App\ScarpaCleaningTransactionItem;
use App\ThermalPrinter;
use App\Transaction;
use App\TransactionPayment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesReportController extends Controller
{
    public function index($monthIndex, $year, Request $request) {
        $result = [];
        $posTransactions = DB::table('transactions')
            ->where('saved', true)
            ->whereNull('cancelation_remarks')
            ->whereNull('deleted_at')
            ->whereMonth('date', $monthIndex)->whereYear('date', $year)
            ->groupBy(DB::raw('day'))
            ->selectRaw('DATE(' . 'date' . ') as day, SUM(total_price) as total_price, SUM(1) as total_jo, SUM(IF(date_paid IS NULL, 0, 1)) as paid_jo')
            //->selectRaw('DATE(' . 'date' . ') as day, SUM(total_price) total_price, SUM(IF(date_paid IS NULL, 0,(select (`total_amount` - (`total_amount` /  100 * `discount`)) from `transaction_payments` where `transaction_payments`.`id` = `transactions`.`id`))) as collection')
            ->get();

        $rfidCardTransactions = DB::table('rfid_card_transactions')
            ->where('card_type', 'u')
            ->whereNull('deleted_at')
            ->whereMonth('created_at', $monthIndex)->whereYear('created_at', $year)
            ->groupBy(DB::raw('day'))
            ->selectRaw('DATE(created_at) as day, SUM(price) as total_price, SUM(IF(card_type = "u", price, 0)) as collection')
            ->get();

        $rfidLoadTransactions = DB::table('rfid_load_transactions')
            ->whereMonth('created_at', $monthIndex)->whereYear('created_at', $year)
            ->whereNull('deleted_at')
            ->groupBy(DB::raw('day'))
            ->selectRaw('DATE(created_at) as day, SUM(amount) as total_price, SUM(amount) as collection')
            ->get();

        $newCustomers = Customer::whereMonth('created_at', $monthIndex)
            ->whereYear('created_at', $year)
            ->groupBy(DB::raw('day'))
            ->selectRaw('DATE(created_at) as day, COUNT(id) as total_count')
            ->get();

        $expenses = Expense::whereMonth('date', $monthIndex)
            ->whereYear('date', $year)
            ->groupBy(DB::raw('day'))
            ->selectRaw('DATE(date) as day, SUM(amount) as expense')
            ->get();

        $productPurchases = ProductPurchase::where('unit_cost', '>', '0')->whereMonth('date', $monthIndex)
            ->whereYear('date', $year)
            ->groupBy(DB::raw('day'))
            ->selectRaw('DATE(date) as day, SUM(unit_cost * quantity) as product_purchase')
            ->get();

        $partialPayments = PartialPayment::whereMonth('date', $monthIndex)
            ->whereYear('date', $year)
            ->groupBy(DB::raw('day'))
            ->select(DB::raw('DATE(date) as day, SUM(`cash` - `change`) as collection'))
            ->get();

        $transactionPayment = TransactionPayment::whereMonth('date', $monthIndex)
            ->whereYear('date', $year)
            ->groupBy(DB::raw('day'))
            // ->selectRaw('DATE(date) as day, SUM((`total_amount` - (`total_amount` /  100 * `discount`)) - `points_in_peso` - `card_load_used` - COALESCE((SELECT cash FROM partial_payments WHERE partial_payments.transaction_id = transaction_payments.id), 0) ) as collection')
            ->select(DB::raw('DATE(date) as day, SUM(`cash` - `change`) as collection'))
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
        $result = $result->map(function($item, $key) {
            $totalCollections = $item->sum('collection');
            $totalExpenses =  $item->sum('expense') + $item->sum('product_purchase');
            return
            [
                'date' => $key,
                'amount' => $item->sum('total_price'),
                'collection' => $totalCollections,
                'total_jo' => $item->sum('total_jo'),
                'paid_jo' => $item->sum('paid_jo'),
                'expenses' => $totalExpenses,
                'newCustomers' => $item->sum('total_count'),
                'totalDeposit' => $totalCollections - $totalExpenses,
            ];
        });

        // $summary = [
        //     'total_jo' => $posTransactions->sum('total_jo'),
        //     'paid_jo' => $posTransactions->sum('paid_jo'),
        //     'expenses' => $result->sum('expenses'),// + $productPurchases->sum('product_purchase'),
        //     'totalSales' => $result->sum('amount'),
        //     'totalCollections' => $result->sum('collection'),
        //     'totalNewCustomers' => $result->sum('newCustomers'),
        //     'totalDeposit' => $result->sum('totalDeposit'),
        // ];

        return response()->json([
            'result' => array_values($result->toArray()),
            // 'summary' => $summary
        ]);
    }

    public function summary(Request $request, $print = false) {
        $newCustomers = Customer::whereDate('created_at', '>=', $request->date)
            ->whereDate('created_at', '<=', $request->until)
            ->count();

        $posTransactions = Transaction::where('saved', true)
            ->whereNull('cancelation_remarks')
            ->whereDate('date', '>=', $request->date)
            ->whereDate('date', '<=', $request->until)
            ->selectRaw('COUNT(id) as total_jo, SUM(total_price) as total_sales')
            ->first();

        $partialPayments = PartialPayment::whereHas('transaction', function($query) use ($request) {
            $query->whereDate('date', '>=', $request->date)
                ->whereDate('date', '<=', $request->until)
                ->whereNull('date_paid')
                ->whereNull('cancelation_remarks');
        })->selectRaw('COUNT(id) as total_jo, SUM(cash) as total_sales, SUM(balance) as total_balance')
            ->first();

        $fullyPaid = TransactionPayment::whereHas('transaction', function($query) use ($request) {
            $query->whereDate('date','>=', $request->date)
                ->whereDate('date', '<=', $request->until)
                ->whereNull('cancelation_remarks');
        })->selectRaw('COUNT(id) as total_jo, SUM(total_amount) as total_sales, SUM(balance) as total_balance')
            ->first();

        $unpaid = Transaction::where(function($query) use ($request) {
            $query->whereDate('date', '>=', $request->date)
                ->whereDate('date', '<=', $request->until);
        })->whereDoesntHave('partialPayment')
            ->whereNull('cancelation_remarks')
            ->whereNull('date_paid')
            ->selectRaw('COUNT(id) as total_jo, SUM(total_price) as total_sales')
            ->first();

        $posSummary = [
            'pos_transactions' => $posTransactions,
            'partial_payments' => $partialPayments,
            'fully_paid' => $fullyPaid,
            'unpaid' => $unpaid,
        ];


        $rfidCardTransactionSummary = RfidCardTransaction::whereDate('created_at','>=', $request->date)
            ->whereDate('created_at', '<=', $request->until)
            ->selectRaw('SUM(IF(card_type = "u", price, 0)) as users_card, SUM(IF(card_type = "c", price, 0)) as customers_card, SUM(price) as total_price, COUNT(id) AS cycle_count')
            ->first();

        $rfidLoadTransactionSummary = RfidLoadTransaction::whereDate('created_at', '>=', $request->date)
            ->whereDate('created_at', '<=', $request->until)
            ->selectRaw('SUM(amount) as total_price, COUNT(id) as total_count')
            ->first();

        $collPartialPayments = PartialPayment::where(function($query) use ($request) {
            $query->whereDate('date','>=', $request->date)
                ->whereDate('date', '<=', $request->until);
        })->whereHas('transaction', function($query) {
                $query->whereDoesntHave('payment')
                    ->whereNull('cancelation_remarks');
            })
            ->sum('cash');

        $colFullPartialPayment = PartialPayment::where(function($query) use ($request) {
            $query->whereDate('date','>=', $request->date)
                ->whereDate('date', '<=', $request->until);
        })->whereHas('transaction', function($query) {
                $query->whereHas('payment')
                    ->whereNull('cancelation_remarks');
            })->sum('cash');

        $collFullyPaid = TransactionPayment::whereDate('date', '>=', $request->date)
            ->whereDate('date', '<=', $request->until)
            ->select(DB::raw('SUM(`cash` - `change`) as collection'))->first();

        $totalSales = $posTransactions->total_sales + $rfidLoadTransactionSummary->total_price + $rfidCardTransactionSummary->users_card;

        // total sales
        //   job_orders
        //   rfid load
        //   rfid transactions

        $collections = [
            'partiallyPaid' => $collPartialPayments,
            'fullyPaid' => $collFullyPaid->collection + $colFullPartialPayment,
            'rfidLoad' => $rfidLoadTransactionSummary->total_price,
            'rfidTap' => $rfidCardTransactionSummary->users_card,
            'total' => $collPartialPayments + $collFullyPaid->collection + $rfidLoadTransactionSummary->total_price + $rfidCardTransactionSummary->users_card + $colFullPartialPayment,
        ];

        $cashless = TransactionPayment::where(function($query) use ($request) {
            $query->whereDate('date', '>=',  $request->date)
                ->whereDate('date', '<=', $request->until);
        })->where("cash_less_amount", ">", 0)
            ->select(DB::raw("SUM(cash_less_amount) as amount, COUNT(*) as quantity, cash_less_provider"))
            ->groupBy('cash_less_provider')
            ->get();

        $discounts = TransactionPayment::where(function($query) use ($request) {
            $query->whereDate('date', '>=',  $request->date)
                ->whereDate('date', '<=', $request->until);
        })->where('discount', '>', 0)
            ->select(DB::raw("SUM(total_amount) as total_amount, discount, COUNT(*) as quantity, CONCAT(discount_name, ' (-', discount ,'%)') as discount_name"))
            ->groupBy('discount_name', 'discount')
            ->get();

        $productPurchases = ProductPurchase::where('unit_cost', '>', '0')
            ->where(function($query) use ($request) {
                $query->whereDate('date', '>=',  $request->date)
                    ->whereDate('date', '<=', $request->until);
            })->selectRaw('SUM(quantity * unit_cost) as total_cost, COUNT(id) as total_count')
            ->first();
        $otherExpenses = Expense::whereDate('date', '>=', $request->date)
            ->whereDate('date', '<=', $request->until)
            ->selectRaw('SUM(amount) as total_expense, COUNT(id) as total_count')
            ->first();

        $expenses = [
            'productPurchases' => $productPurchases,
            'otherExpenses' => $otherExpenses,
            'total' => $otherExpenses->total_expense + $productPurchases->total_cost,
        ];

        $usedProducts = ProductTransactionItem::whereHas('transaction', function($query) use ($request) {
            $query->whereDate('date', '>=',  $request->date)
                ->whereNull('cancelation_remarks')
                ->whereDate('date', '<=', $request->until)
                ->where('saved', true);
        })->where('saved', true)->groupBy('name')->selectRaw('count(*) as quantity, name, sum(price) as total_price')->get();

        $usedServices = ServiceTransactionItem::whereHas('transaction', function($query) use ($request) {
            $query->whereDate('date', '>=', $request->date)
                ->whereNull('cancelation_remarks')
                ->whereDate('date', '<=', $request->until)
                ->where('saved', true);
        })->where('saved', true)->groupBy('name')->selectRaw('COUNT(*) as quantity, name, SUM(price) as total_price')->get();

        $usedScarpa = ScarpaCleaningTransactionItem::whereHas('transaction', function($query) use ($request) {
            $query->whereDate('date', '>=', $request->date)
                ->whereNull('cancelation_remarks')
                ->whereDate('date', '<=', $request->until)
                ->where('saved', true);
        })->join('scarpa_categories', 'scarpa_categories.id', 'scarpa_category_id')
            ->where('saved', true)->groupBy('name')->selectRaw('COUNT(*) as quantity, scarpa_categories.name as name, SUM(price) as total_price')->get();

        $usedLagoon = LagoonTransactionItem::whereHas('transaction', function($query) use ($request) {
            $query->whereDate('date', '>=', $request->date)
                ->whereNull('cancelation_remarks')
                ->whereDate('date', '<=', $request->until)
                ->where('saved', true);
        })->join('lagoons', 'lagoons.id', 'lagoon_transaction_items.lagoon_id')
            ->where('saved', true)->groupBy('category')->selectRaw('COUNT(*) as quantity, category as name, SUM(lagoon_transaction_items.price) as total_price')->get();

        $usedLagoonPerKilo = LagoonPerKiloTransactionItem::whereHas('transaction', function($query) use ($request) {
            $query->whereDate('date', '>=', $request->date)
                ->whereNull('cancelation_remarks')
                ->whereDate('date', '<=', $request->until)
                ->where('saved', true);
        })->where('saved', true)->groupBy('name')->selectRaw('SUM(kilos) as kg, name, SUM(total_price) as total_price')->get();

        $data = [
            'newCustomers' => $newCustomers,
            'posSummary' => $posSummary,
            'rfidCard' => $rfidCardTransactionSummary,
            'rfidLoad' => $rfidLoadTransactionSummary,
            'collections' => $collections,
            'expenses' => $expenses,
            'usedProducts' => $usedProducts,
            'usedServices' => $usedServices,
            'usedScarpa' => $usedScarpa,
            'usedLagoon' => $usedLagoon,
            'usedLagoonPerKilo' => $usedLagoonPerKilo,
            'totalSales' => $totalSales,
            'totalDeposit' => $collections['total'] - $expenses['total'],
            'cashless' => $cashless,
            'discounts' => $discounts,
        ];

        if($print) {
            $data['date'] = Carbon::createFromDate($request->date)->format("D m/d/Y");
            $data['quote'] = '** Daily Sales **';
            // return view('printer.daily', $data);
            $thermalPrinter = new ThermalPrinter;
            if($printerError = $thermalPrinter->hasError()) {
                return response()->json([
                    'errors' => $printerError
                ], 422);
            } else {
                $thermalPrinter->dailySummary($data);
                return response()->json([
                    'success' => 'Daily sales Printed successfully'
                ]);
            }
        } else {
            return response()->json($data);
        }
    }

    // public function posTransactions($monthIndex, $year, Request $request) {
    //     $groupBy = $request->groupBy == 'payment' ? 'transaction_payments.date' : 'transactions.date';

    //     $posTransactions = Transaction::whereHas('payment')
    //         ->join('transaction_payments', 'transaction_payments.transaction_id', '=', 'transactions.id')
    //         ->whereMonth($groupBy, $monthIndex)->whereYear($groupBy, $year)
    //         ->groupBy(DB::raw('day'))
    //         ->selectRaw('DATE('.$groupBy.') as day, SUM(total_price) as total')
    //         ->get();

    //     return response()->json([
    //         'result' => $posTransactions,
    //     ]);
    // }

    public function weekly($monthIndex, $year, Request $request) {
        $posTransactions = Transaction::whereMonth('date', $monthIndex)
            ->whereYear('date', $year)
            ->where('saved', true)
            ->selectRaw('SUM(IF(date_paid IS NULL, 0, total_price)) as paid_total,
                SUM(IF(date_paid IS NOT NULL, 0, total_price)) as unpaid_total,
                SUM(total_price) as total_price,
                COUNT(IF(date_paid IS NULL, NULL, 1)) as paid_jo,
                COUNT(IF(date_paid IS NULL, 1, NULL)) as unpaid_jo,
                COUNT(job_order) AS total_jo,
                CONCAT(
                    IF(DATE_FORMAT((date - INTERVAL (WEEKDAY(date)) DAY), "%m") <> '.$monthIndex.',
                        DATE_FORMAT(DATE_SUB(date, INTERVAL DAYOFMONTH(date)-1 DAY), "%d"),
                        DATE_FORMAT((date - INTERVAL (WEEKDAY(date)) DAY), "%d")), "-",
                    IF(DATE_FORMAT((date - INTERVAL (WEEKDAY(date)-6) DAY), "%m") <> '.$monthIndex.',
                        DATE_FORMAT(LAST_DAY(date), "%d"),
                        DATE_FORMAT((date - INTERVAL (WEEKDAY(date)-6) DAY), "%d"))
                ) as label')
            ->groupBy(DB::raw('label'))->get();

            $rfidCardTransactions = RfidCardTransaction::where('card_type', 'u')
                ->whereMonth('created_at', $monthIndex)->whereYear('created_at', $year)
                ->selectRaw('SUM(price) as total_price, SUM(IF(card_type = "u", price, 0)) as collection,
                CONCAT(
                    IF(DATE_FORMAT((created_at - INTERVAL (WEEKDAY(created_at)) DAY), "%m") <> '.$monthIndex.',
                        DATE_FORMAT(DATE_SUB(created_at, INTERVAL DAYOFMONTH(created_at)-1 DAY), "%d"),
                        DATE_FORMAT((created_at - INTERVAL (WEEKDAY(created_at)) DAY), "%d")), "-",
                    IF(DATE_FORMAT((created_at - INTERVAL (WEEKDAY(created_at)-6) DAY), "%m") <> '.$monthIndex.',
                        DATE_FORMAT(LAST_DAY(created_at), "%d"),
                        DATE_FORMAT((created_at - INTERVAL (WEEKDAY(created_at)-6) DAY), "%d"))
                ) as label')
                ->groupBy(DB::raw('label'))
                ->get();

            $rfidLoadTransactions = RfidLoadTransaction::whereMonth('created_at', $monthIndex)
                ->whereYear('created_at', $year)
                ->selectRaw('SUM(amount) as total_price, SUM(amount) as collection,
                CONCAT(
                    IF(DATE_FORMAT((created_at - INTERVAL (WEEKDAY(created_at)) DAY), "%m") <> '.$monthIndex.',
                        DATE_FORMAT(DATE_SUB(created_at, INTERVAL DAYOFMONTH(created_at)-1 DAY), "%d"),
                        DATE_FORMAT((created_at - INTERVAL (WEEKDAY(created_at)) DAY), "%d")), "-",
                    IF(DATE_FORMAT((created_at - INTERVAL (WEEKDAY(created_at)-6) DAY), "%m") <> '.$monthIndex.',
                        DATE_FORMAT(LAST_DAY(created_at), "%d"),
                        DATE_FORMAT((created_at - INTERVAL (WEEKDAY(created_at)-6) DAY), "%d"))
                ) as label')
                ->groupBy(DB::raw('label'))
                ->get();

            $newCustomers = Customer::whereMonth('created_at', $monthIndex)
                ->whereYear('created_at', $year)
                ->selectRaw('COUNT(id) as total_count,
                CONCAT(
                    IF(DATE_FORMAT((created_at - INTERVAL (WEEKDAY(created_at)) DAY), "%m") <> '.$monthIndex.',
                        DATE_FORMAT(DATE_SUB(created_at, INTERVAL DAYOFMONTH(created_at)-1 DAY), "%d"),
                        DATE_FORMAT((created_at - INTERVAL (WEEKDAY(created_at)) DAY), "%d")), "-",
                    IF(DATE_FORMAT((created_at - INTERVAL (WEEKDAY(created_at)-6) DAY), "%m") <> '.$monthIndex.',
                        DATE_FORMAT(LAST_DAY(created_at), "%d"),
                        DATE_FORMAT((created_at - INTERVAL (WEEKDAY(created_at)-6) DAY), "%d"))
                ) as label')
                ->groupBy(DB::raw('label'))
                ->get();

            $expenses = Expense::whereMonth('date', $monthIndex)
                ->whereYear('date', $year)
                ->selectRaw('SUM(amount) as expense, CONCAT(
                    IF(DATE_FORMAT((date - INTERVAL (WEEKDAY(date)) DAY), "%m") <> '.$monthIndex.',
                        DATE_FORMAT(DATE_SUB(date, INTERVAL DAYOFMONTH(date)-1 DAY), "%d"),
                        DATE_FORMAT((date - INTERVAL (WEEKDAY(date)) DAY), "%d")), "-",
                    IF(DATE_FORMAT((date - INTERVAL (WEEKDAY(date)-6) DAY), "%m") <> '.$monthIndex.',
                        DATE_FORMAT(LAST_DAY(date), "%d"),
                        DATE_FORMAT((date - INTERVAL (WEEKDAY(date)-6) DAY), "%d"))
                ) as label')
                ->groupBy(DB::raw('label'))
                ->get();

            $productPurchases = ProductPurchase::whereMonth('date', $monthIndex)
                ->whereYear('date', $year)
                ->selectRaw('SUM(unit_cost * quantity) as product_purchase,
                CONCAT(
                    IF(DATE_FORMAT((date - INTERVAL (WEEKDAY(date)) DAY), "%m") <> '.$monthIndex.',
                        DATE_FORMAT(DATE_SUB(date, INTERVAL DAYOFMONTH(date)-1 DAY), "%d"),
                        DATE_FORMAT((date - INTERVAL (WEEKDAY(date)) DAY), "%d")), "-",
                    IF(DATE_FORMAT((date - INTERVAL (WEEKDAY(date)-6) DAY), "%m") <> '.$monthIndex.',
                        DATE_FORMAT(LAST_DAY(date), "%d"),
                        DATE_FORMAT((date - INTERVAL (WEEKDAY(date)-6) DAY), "%d"))
                ) as label')
                ->groupBy(DB::raw('label'))
                ->get();

            $transactionPayment = TransactionPayment::whereMonth('date', $monthIndex)
                ->whereYear('date', $year)
                ->selectRaw('SUM(`total_amount` - (`total_amount` /  100 * `discount`) - `points_in_peso` - `card_load_used`) as collection,
                CONCAT(
                    IF(DATE_FORMAT((date - INTERVAL (WEEKDAY(date)) DAY), "%m") <> '.$monthIndex.',
                        DATE_FORMAT(DATE_SUB(date, INTERVAL DAYOFMONTH(date)-1 DAY), "%d"),
                        DATE_FORMAT((date - INTERVAL (WEEKDAY(date)) DAY), "%d")), "-",
                    IF(DATE_FORMAT((date - INTERVAL (WEEKDAY(date)-6) DAY), "%m") <> '.$monthIndex.',
                        DATE_FORMAT(LAST_DAY(date), "%d"),
                        DATE_FORMAT((date - INTERVAL (WEEKDAY(date)-6) DAY), "%d"))
                ) as label')
                ->groupBy(DB::raw('label'))
                ->get();

        $result = array_merge(
            $posTransactions->toArray(),
            $rfidCardTransactions->toArray(),
            $rfidLoadTransactions->toArray(),
            $expenses->toArray(),
            $productPurchases->toArray(),
            $newCustomers->toArray(),
            $transactionPayment->toArray()
        );

        $result = collect($result)->groupBy('label');
        $result = $result->map(function($item, $key) {

            $totalExpenses = $item->sum('expense') + $item->sum('product_purchase');
            $totalCollections = $item->sum('collection');

            return
            [
                'label' => $key,
                'amount' => $item->sum('total_price'),
                'total_jo' => $item->sum('total_jo'),
                'paid_jo' => $item->sum('paid_jo'),
                'expenses' => $totalExpenses,
                'newCustomers' => $item->sum('total_count'),
                'collections' => $totalCollections,
                'totalDeposit' => $totalCollections - $totalExpenses,
            ];
        });

        $summary = [
            'total_jo' => $posTransactions->sum('total_jo'),
            'paid_jo' => $posTransactions->sum('paid_jo'),
            'expenses' => $expenses->sum('expense') + $productPurchases->sum('product_purchase'),
            'totalSales' => $result->sum('amount'),
            'totalCollections' => $result->sum('collections'),
            'totalNewCustomers' => $result->sum('newCustomers'),
            'totalDeposit' => $result->sum('totalDeposit'),
        ];

        return response()->json([
            'result' => array_values($result->toArray()),
            'summary' => $summary,
        ]);
    }

    public function monthy($year) {
        $posTransactions = DB::table('transactions')
            ->whereNull('cancelation_remarks')
            ->where('saved', true)
            ->whereNull('deleted_at')
            ->whereYear('date', $year)
            ->groupBy(DB::raw('month, year'))
            ->selectRaw('YEAR(date) as year, MONTH(date) as month, SUM(total_price) as total_price, SUM(1) as total_jo, SUM(IF(date_paid IS NULL, 0, 1)) as paid_jo')
            ->get();

        $rfidCardTransactions = DB::table('rfid_card_transactions')
            ->where('card_type', 'u')
            ->whereNull('deleted_at')
            ->whereYear('created_at', $year)
            ->groupBy(DB::raw('month, year'))
            ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(price) as total_price, SUM(IF(card_type = "u", price, 0)) as collection')
            ->get();

        $rfidLoadTransactions = DB::table('rfid_load_transactions')
            ->whereYear('created_at', $year)
            ->whereNull('deleted_at')
            ->groupBy(DB::raw('month, year'))
            ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(amount) as total_price, SUM(amount) as collection')
            ->get();

        $newCustomers = Customer::whereYear('created_at', $year)
            ->groupBy(DB::raw('month, year'))
            ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(id) as total_count')
            ->get();

        $expenses = Expense::whereYear('date', $year)
            ->groupBy(DB::raw('month, year'))
            ->selectRaw('YEAR(date) as year, MONTH(date) as month, SUM(amount) as expense')
            ->get();

        $productPurchases = ProductPurchase::where('unit_cost', '>', '0')->whereYear('date', $year)
            ->groupBy(DB::raw('month, year'))
            ->selectRaw('YEAR(date) as year, MONTH(date) as month, SUM(unit_cost * quantity) as product_purchase')
            ->get();

        $partialPayments = PartialPayment::whereYear('date', $year)
            ->groupBy(DB::raw('month, year'))
            ->select(DB::raw('YEAR(date) as year, MONTH(date) as month, SUM(`cash` - `change`) as collection'))
            ->get();

        $transactionPayment = TransactionPayment::whereYear('date', $year)
            ->groupBy(DB::raw('month, year'))
            ->select(DB::raw('YEAR(date) as year, MONTH(date) as month, SUM(`cash` - `change`) as collection'))
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
        $result = collect($result)->groupBy('month');
        $result = $result->map(function($item, $key) {
            $totalCollections = $item->sum('collection');
            $totalExpenses =  $item->sum('expense') + $item->sum('product_purchase');
            return
            [
                // 'year' => $item->first()->year,
                'monthIndex' => $key,
                'amount' => $item->sum('total_price'),
                'collection' => $totalCollections,
                'total_jo' => $item->sum('total_jo'),
                'paid_jo' => $item->sum('paid_jo'),
                'expenses' => $totalExpenses,
                'newCustomers' => $item->sum('total_count'),
                'totalDeposit' => $totalCollections - $totalExpenses,
            ];
        });

        return response()->json([
            'result' => array_values($result->toArray()),
        ]);
    }

    public function yearly($yearFrom, $yearUntil) {
        $posTransactions = DB::table('transactions')
            ->whereNull('cancelation_remarks')
            ->where('saved', true)
            ->whereNull('deleted_at')
            ->whereBetween(DB::raw('YEAR(date)'), [$yearFrom, $yearUntil])
            ->groupBy(DB::raw('year'))
            ->selectRaw('YEAR(date) as year, SUM(total_price) as total_price, SUM(1) as total_jo, SUM(IF(date_paid IS NULL, 0, 1)) as paid_jo')
            ->get();

        $rfidCardTransactions = DB::table('rfid_card_transactions')
            ->where('card_type', 'u')
            ->whereNull('deleted_at')
            ->whereBetween(DB::raw('YEAR(created_at)'), [$yearFrom, $yearUntil])
            ->groupBy(DB::raw('year'))
            ->selectRaw('YEAR(created_at) as year, SUM(price) as total_price, SUM(IF(card_type = "u", price, 0)) as collection')
            ->get();

        $rfidLoadTransactions = DB::table('rfid_load_transactions')
            ->whereBetween(DB::raw('YEAR(created_at)'), [$yearFrom, $yearUntil])
            ->whereNull('deleted_at')
            ->groupBy(DB::raw('year'))
            ->selectRaw('YEAR(created_at) as year, SUM(amount) as total_price, SUM(amount) as collection')
            ->get();

        $newCustomers = Customer::whereBetween(DB::raw('YEAR(created_at)'), [$yearFrom, $yearUntil])
            ->groupBy(DB::raw('year'))
            ->selectRaw('YEAR(created_at) as year, COUNT(id) as total_count')
            ->get();

        $expenses = Expense::whereBetween(DB::raw('YEAR(created_at)'), [$yearFrom, $yearUntil])
            ->groupBy(DB::raw('year'))
            ->selectRaw('YEAR(date) as year, SUM(amount) as expense')
            ->get();

        $productPurchases = ProductPurchase::whereBetween(DB::raw('YEAR(created_at)'), [$yearFrom, $yearUntil])
            ->groupBy(DB::raw('year'))
            ->selectRaw('YEAR(date) as year, SUM(unit_cost * quantity) as product_purchase')
            ->get();

        $partialPayments = PartialPayment::whereBetween(DB::raw('YEAR(created_at)'), [$yearFrom, $yearUntil])
            ->groupBy(DB::raw('year'))
            ->select(DB::raw('YEAR(date) as year, SUM(`cash` - `change`) as collection'))
            ->get();

        $transactionPayment = TransactionPayment::whereBetween(DB::raw('YEAR(created_at)'), [$yearFrom, $yearUntil])
            ->groupBy(DB::raw('year'))
            ->select(DB::raw('YEAR(date) as year, SUM(`cash` - `change`) as collection'))
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
        $result = collect($result)->groupBy('year');
        $result = $result->map(function($item, $key) {
            $totalCollections = $item->sum('collection');
            $totalExpenses =  $item->sum('expense') + $item->sum('product_purchase');
            return
            [
                'year' => $key,
                'amount' => $item->sum('total_price'),
                'collection' => $totalCollections,
                'total_jo' => $item->sum('total_jo'),
                'paid_jo' => $item->sum('paid_jo'),
                'expenses' => $totalExpenses,
                'newCustomers' => $item->sum('total_count'),
                'totalDeposit' => $totalCollections - $totalExpenses,
            ];
        });


        return response()->json([
            'result' => array_values($result->toArray()),
        ]);
    }

    public function monthlySummary($monthIndex, $year) {

    }

    public function yearlySummary($year) {

    }

    // $request->dateFrom
    // $request->dateTo
    public function customRange(Request $request, $print = false) {
        return null;
        $rules = [
            'dateFrom' => 'required|date',
            'dateTo' => 'required|date',
        ];

        if($request->validate($rules)) {

            $newCustomers = Customer::whereBetween(DB::raw('DATE(created_at)'), [$request->dateFrom, $request->dateTo])->count();

            $posTransactions = Transaction::where('saved', true)
                ->whereBetween(DB::raw('DATE(date)'), [$request->dateFrom, $request->dateTo])
                ->selectRaw('COUNT(id) as total_jo, SUM(total_price) as total_sales')
                ->first();

            $partialPayments = PartialPayment::whereHas('transaction', function($query) use ($request) {
                $query->whereBetween(DB::raw('DATE(date)'), [$request->dateFrom, $request->dateTo])
                    ->whereNull('date_paid');
            })->selectRaw('COUNT(id) as total_jo, SUM(cash) as total_sales, SUM(balance) as total_balance')
                ->first();

            $fullyPaid = TransactionPayment::whereHas('transaction', function($query) use ($request) {
                $query->whereBetween(DB::raw('DATE(date)'), [$request->dateFrom, $request->dateTo]);
            })->selectRaw('COUNT(id) as total_jo, SUM(total_amount) as total_sales, SUM(balance) as total_balance')
                ->first();

            $unpaid = Transaction::whereBetween(DB::raw('DATE(date)'), [$request->dateFrom, $request->dateTo])
                ->whereDoesntHave('partialPayment')
                ->whereNull('date_paid')
                ->selectRaw('COUNT(id) as total_jo, SUM(total_price) as total_sales')
                ->first();

            $posSummary = [
                'pos_transactions' => $posTransactions,
                'partial_payments' => $partialPayments,
                'fully_paid' => $fullyPaid,
                'unpaid' => $unpaid,
            ];


            $rfidCardTransactionSummary = RfidCardTransaction::whereBetween(DB::raw('DATE(created_at)'), [$request->dateFrom, $request->dateTo])
                ->selectRaw('SUM(IF(card_type = "u", price, 0)) as users_card, SUM(IF(card_type = "c", price, 0)) as customers_card, SUM(price) as total_price, COUNT(id) AS cycle_count')
                ->first();

            $rfidLoadTransactionSummary = RfidLoadTransaction::whereBetween(DB::raw('DATE(created_at)'), [$request->dateFrom, $request->dateTo])
                ->selectRaw('SUM(amount) as total_price, COUNT(id) as total_count')
                ->first();

            $collPartialPayments = PartialPayment::whereBetween(DB::raw('DATE(date)'), [$request->dateFrom, $request->dateTo])
                ->whereHas('transaction', function($query) {
                    $query->whereDoesntHave('payment');
                })
                ->sum('cash');

            $colFullPartialPayment = PartialPayment::whereBetween(DB::raw('DATE(date)'), [$request->dateFrom, $request->dateTo])
                ->whereHas('transaction', function($query) {
                    $query->whereHas('payment');
                })->sum('cash');

            $collFullyPaid = TransactionPayment::whereBetween(DB::raw('DATE(date)'), [$request->dateFrom, $request->dateTo])
                ->select(DB::raw('SUM(`cash` - `change`) as collection'))->first();

            $totalSales = $posTransactions->total_sales + $rfidLoadTransactionSummary->total_price + $rfidCardTransactionSummary->users_card;

            // total sales
            //   job_orders
            //   rfid load
            //   rfid transactions

            $collections = [
                'partiallyPaid' => $collPartialPayments,
                'fullyPaid' => $collFullyPaid->collection + $colFullPartialPayment,
                'rfidLoad' => $rfidLoadTransactionSummary->total_price,
                'rfidTap' => $rfidCardTransactionSummary->users_card,
                'total' => $collPartialPayments + $collFullyPaid->collection + $rfidLoadTransactionSummary->total_price + $rfidCardTransactionSummary->users_card + $colFullPartialPayment,
            ];

            $productPurchases = ProductPurchase::where('unit_cost', '>', '0')->whereBetween(DB::raw('DATE(date)'), [$request->dateFrom, $request->dateTo])
                ->selectRaw('SUM(quantity * unit_cost) as total_cost, COUNT(id) as total_count')
                ->first();
            $otherExpenses = Expense::whereBetween(DB::raw('DATE(date)'), [$request->dateFrom, $request->dateTo])
                ->selectRaw('SUM(amount) as total_expense, COUNT(id) as total_count')
                ->first();

            $expenses = [
                'productPurchases' => $productPurchases,
                'otherExpenses' => $otherExpenses,
                'total' => $otherExpenses->total_expense + $productPurchases->total_cost,
            ];

            $usedProducts = ProductTransactionItem::whereHas('transaction', function($query) use ($request) {
                $query->whereBetween(DB::raw('DATE(date)'), [$request->dateFrom, $request->dateTo])
                    ->where('saved', true);
            })->where('saved', true)->groupBy('name')->selectRaw('count(*) as quantity, name, sum(price) as total_price')->get();

            $usedServices = ServiceTransactionItem::whereHas('transaction', function($query) use ($request) {
                $query->whereBetween(DB::raw('DATE(date)'), [$request->dateFrom, $request->dateTo])
                    ->where('saved', true);
            })->where('saved', true)->groupBy('name')->selectRaw('COUNT(*) as quantity, name, SUM(price) as total_price')->get();

            $data = [
                'newCustomers' => $newCustomers,
                'posSummary' => $posSummary,
                'rfidCard' => $rfidCardTransactionSummary,
                'rfidLoad' => $rfidLoadTransactionSummary,
                'collections' => $collections,
                'expenses' => $expenses,
                'usedProducts' => $usedProducts,
                'usedServices' => $usedServices,
                'totalSales' => $totalSales,
                'totalDeposit' => $collections['total'] - $expenses['total'],
            ];

            if($print) {
                $data['dateFrom'] = Carbon::createFromDate($request->dateFrom)->format("D m/d/Y");
                $data['dateTo'] = Carbon::createFromDate($request->dateTo)->format("D m/d/Y");
                $data['origin'] = $request->origin;
                // return view('printer.daily', $data);
                $thermalPrinter = new ThermalPrinter;
                if($printerError = $thermalPrinter->hasError()) {
                    return response()->json([
                        'errors' => $printerError
                    ], 422);
                } else {
                    $thermalPrinter->dailySummary($data);
                    return response()->json([
                        'success' => 'Daily sales Printed successfully'
                    ]);
                }
            } else {
                return response()->json($data);
            }

        }
    }
}
