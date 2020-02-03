<?php

namespace App\Http\Controllers;

use App\Customer;
use App\Machine;
use App\RfidCardTransaction;
use App\RfidLoadTransaction;
use App\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesReportController extends Controller
{
    public function index($monthIndex, $year, Request $request) {
        $result = [];
        $groupBy = $request->groupBy == 'payment' ? 'transaction_payments.date' : 'transactions.date';

        $posTransactions = DB::table('transactions')
            ->whereNotNull('date_paid')
            ->whereMonth($groupBy, $monthIndex)->whereYear($groupBy, $year)
            ->groupBy(DB::raw('day'))
            ->selectRaw('DATE(' . $groupBy . ') as day, SUM(total_price) total_price')
            ->get();

        $rfidCardTransactions = DB::table('rfid_card_transactions')
            ->where('card_type', 'u')
            ->whereMonth('created_at', $monthIndex)->whereYear('created_at', $year)
            ->groupBy(DB::raw('day'))
            ->selectRaw('DATE(created_at) as day, SUM(price) as total_price')
            ->get();

        $rfidLoadTransactions = DB::table('rfid_load_transactions')
            ->whereMonth('created_at', $monthIndex)->whereYear('created_at', $year)
            ->groupBy(DB::raw('day'))
            ->selectRaw('DATE(created_at) as day, SUM(amount) as total_price')
            ->get();

        $newCustomers = Customer::whereMonth('first_visit', $monthIndex)
            ->whereYear('first_visit', $year)
            ->groupBy(DB::raw('day'))
            ->selectRaw('DATE(first_visit) as day, COUNT(id) as total_count')
            ->get();

        $result = array_merge($posTransactions->toArray(), $rfidCardTransactions->toArray(), $rfidLoadTransactions->toArray());
        $result = collect($result)->groupBy('day');
        $result = $result->map(function($item, $key) {
            return
            [
                'date' => $key,
                'amount' => $item->sum('total_price')
            ];
        });

        return response()->json([
            'result' => array_values($result->toArray()),
            'newCustomers' => $newCustomers,
        ]);
    }

    public function summary($date) {
        $posTransactionSummary = Transaction::whereDate('date', $date)
            ->selectRaw('SUM(IF(date_paid IS NULL, 0, total_price)) as paid_total, SUM(IF(date_paid IS NOT NULL, 0, total_price)) as unpaid_total, SUM(total_price) as total_price, COUNT(IF(date_paid IS NULL, NULL, 1)) as paid_count, COUNT(IF(date_paid IS NULL, 1, NULL)) as unpaid_count, COUNT(job_order) AS total_count')
            ->get();

        $rfidCardTransactionSummary = RfidCardTransaction::whereDate('created_at', $date)
            ->selectRaw('SUM(IF(card_type = "u", price, 0)) as users_card, SUM(IF(card_type = "c", price, 0)) as customer_card, SUM(price) as total_price, COUNT(id) AS cycle_count')
            ->get();

        $rfidLoadTransactionSummary = RfidLoadTransaction::whereDate('created_at', $date)
            ->selectRaw('SUM(amount) as total_price, COUNT(id) as total_count')
            ->get();

        $newCustomers = Customer::whereDate('first_visit', $date)
            ->selectRaw('COUNT(id) as total_count')
            ->get();

        return response()->json([
            'result' => [
                'posTransactionSummary' => $posTransactionSummary,
                'rfidCardTransactionSummary' => $rfidCardTransactionSummary,
                'rfidLoadTransactionSummary' => $rfidLoadTransactionSummary,
                'newCustomers' => $newCustomers,
            ]
        ]);
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
}
