<?php

namespace App\Http\Controllers;

use App\Customer;
use App\Expense;
use App\Machine;
use App\MonthlyTarget;
use App\PartialPayment;
use App\ProductPurchase;
use App\ProductTransactionItem;
use App\RfidCardTransaction;
use App\RfidLoadTransaction;
use App\ServiceTransactionItem;
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

        $newCustomers = Customer::whereMonth('first_visit', $monthIndex)
            ->whereYear('first_visit', $year)
            ->groupBy(DB::raw('day'))
            ->selectRaw('DATE(first_visit) as day, COUNT(id) as total_count')
            ->get();

        $expenses = Expense::whereMonth('date', $monthIndex)
            ->whereYear('date', $year)
            ->groupBy(DB::raw('day'))
            ->selectRaw('DATE(date) as day, SUM(amount) as expense')
            ->get();

        $productPurchases = ProductPurchase::whereMonth('date', $monthIndex)
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

        $summary = [
            'total_jo' => $posTransactions->sum('total_jo'),
            'paid_jo' => $posTransactions->sum('paid_jo'),
            'expenses' => $result->sum('expenses'),// + $productPurchases->sum('product_purchase'),
            'totalSales' => $result->sum('amount'),
            'totalCollections' => $result->sum('collection'),
            'totalNewCustomers' => $result->sum('newCustomers'),
            'totalDeposit' => $result->sum('totalDeposit'),
        ];

        return response()->json([
            'result' => array_values($result->toArray()),
            'summary' => $summary
        ]);
    }

    public function summary($date, $print = false) {
        $newCustomers = Customer::whereDate('first_visit', $date)->count();

        $posTransactions = Transaction::whereDate('date', $date)
            ->selectRaw('COUNT(id) as total_jo, SUM(total_price) as total_sales')
            ->first();

        $partialPayments = PartialPayment::whereHas('transaction', function($query) use ($date) {
            $query->whereDate('date', $date)
                ->whereNull('date_paid');
        })->selectRaw('COUNT(id) as total_jo, SUM(cash) as total_sales, SUM(balance) as total_balance')
            ->first();

        $fullyPaid = TransactionPayment::whereHas('transaction', function($query) use ($date) {
            $query->whereDate('date', $date);
        })->selectRaw('COUNT(id) as total_jo, SUM(total_amount) as total_sales, SUM(balance) as total_balance')
            ->first();

        $unpaid = Transaction::whereDate('date', $date)
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


        $rfidCardTransactionSummary = RfidCardTransaction::whereDate('created_at', $date)
            ->selectRaw('SUM(IF(card_type = "u", price, 0)) as users_card, SUM(IF(card_type = "c", price, 0)) as customers_card, SUM(price) as total_price, COUNT(id) AS cycle_count')
            ->first();

        $rfidLoadTransactionSummary = RfidLoadTransaction::whereDate('created_at', $date)
            ->selectRaw('SUM(amount) as total_price, COUNT(id) as total_count')
            ->first();

        $collPartialPayments = PartialPayment::whereDate('date', $date)
            ->sum('cash');
        $collFullyPaid = TransactionPayment::whereDate('date', $date)
            ->select(DB::raw('SUM(`cash` - `change`) as collection'))->first();

        $totalSales = $posTransactions->total_sales + $rfidLoadTransactionSummary->total_price + $rfidCardTransactionSummary->users_card;

        // total sales
        //   job_orders
        //   rfid load
        //   rfid transactions

        $collections = [
            'partiallyPaid' => $collPartialPayments,
            'fullyPaid' => $collFullyPaid->collection,
            'rfidLoad' => $rfidLoadTransactionSummary->total_price,
            'rfidTap' => $rfidCardTransactionSummary->users_card,
            'total' => $collPartialPayments + $collFullyPaid->collection + $rfidLoadTransactionSummary->total_price + $rfidCardTransactionSummary->users_card,
        ];

        $productPurchases = ProductPurchase::whereDate('date', $date)
            ->selectRaw('SUM(quantity * unit_cost) as total_cost, COUNT(id) as total_count')
            ->first();
        $otherExpenses = Expense::whereDate('date', $date)
            ->selectRaw('SUM(amount) as total_expense, COUNT(id) as total_count')
            ->first();

        $expenses = [
            'productPurchases' => $productPurchases,
            'otherExpenses' => $otherExpenses,
            'total' => $otherExpenses->total_expense, + $productPurchases->total_cost,
        ];

        $usedProducts = ProductTransactionItem::whereHas('transaction', function($query) use ($date) {
            $query->whereDate('date', $date);
        })->where('saved', true)->groupBy('name')->selectRaw('count(*) as quantity, name, sum(price) as total_price')->get();

        $usedServices = ServiceTransactionItem::whereHas('transaction', function($query) use ($date) {
            $query->whereDate('date', $date);
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
            $data['date'] = Carbon::createFromDate($date)->format("l jS F Y");
            return view('printer.daily', $data);
        } else {
            return response()->json($data);
        }

    }

    public function posTransactions($monthIndex, $year, Request $request) {
        $groupBy = $request->groupBy == 'payment' ? 'transaction_payments.date' : 'transactions.date';

        $posTransactions = Transaction::whereHas('payment')
            ->join('transaction_payments', 'transaction_payments.transaction_id', '=', 'transactions.id')
            ->whereMonth($groupBy, $monthIndex)->whereYear($groupBy, $year)
            ->groupBy(DB::raw('day'))
            ->selectRaw('DATE('.$groupBy.') as day, SUM(total_price) as total')
            ->get();

        return response()->json([
            'result' => $posTransactions,
        ]);
    }

    public function rangeSummary($dateFrom, $dateTo) {

    }

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

            $newCustomers = Customer::whereMonth('first_visit', $monthIndex)
                ->whereYear('first_visit', $year)
                ->selectRaw('COUNT(id) as total_count,
                CONCAT(
                    IF(DATE_FORMAT((first_visit - INTERVAL (WEEKDAY(first_visit)) DAY), "%m") <> '.$monthIndex.',
                        DATE_FORMAT(DATE_SUB(first_visit, INTERVAL DAYOFMONTH(first_visit)-1 DAY), "%d"),
                        DATE_FORMAT((first_visit - INTERVAL (WEEKDAY(first_visit)) DAY), "%d")), "-",
                    IF(DATE_FORMAT((first_visit - INTERVAL (WEEKDAY(first_visit)-6) DAY), "%m") <> '.$monthIndex.',
                        DATE_FORMAT(LAST_DAY(first_visit), "%d"),
                        DATE_FORMAT((first_visit - INTERVAL (WEEKDAY(first_visit)-6) DAY), "%d"))
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

    public function yearlyCumulative($year) {
        $posTransactions = Transaction::whereYear('date', $year)
            ->where('saved', 1)
            ->selectRaw('MONTH(date) as month, SUM(total_price) as jo_sales, COUNT(*) as jo_count')
            ->orderBy('month')
            ->groupBy('month')
            ->get();

        $total = 0;

        $monthlyTargets = MonthlyTarget::all();


        $posTransactions->transform(function($item) use (&$total, $monthlyTargets) {
            $total = $item->jo_sales + $total;
            $item['month_to_date'] = $total;

            $target = $monthlyTargets->find('index', $item->month);
            $item['target'] = $target->target;

            // if($before > 0 && $item->jo_sales > $before) {
            //     // win
            //     $item['diff_percentage'] = ($item->jo_sales - $before) / $item->jo_sales * 100;
            // } else if($before > 0 && $item->jo_sales < $before) {
            //     // loss
            //     $item['diff_percentage'] = (($before - $item->jo_sales) / $before * 100) - 100;
            // }
            // $before = $item->jo_sales;
            return $item;
        });

        return response()->json($posTransactions);
    }
}
