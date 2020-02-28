<?php

namespace App\Http\Controllers;

use App\RfidCardTransaction;
use App\RfidLoadTransaction;
use App\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    public function pos(Request $request) {
        $serviceTransactions = DB::table('transactions')
            ->whereDate('transactions.date', $request->date)
            ->whereNull('transactions.deleted_at')
            ->whereNull('service_transaction_items.deleted_at')
            ->join('service_transaction_items', 'transactions.id', '=', 'service_transaction_items.transaction_id')
            ->selectRaw('transactions.id as transaction_id, job_order, service_transaction_items.name as item_name, service_transaction_items.price as item_price, transactions.date as _date');

        $productTransactions = DB::table('transactions')
            ->whereDate('transactions.date', $request->date)
            ->whereNull('transactions.deleted_at')
            ->whereNull('product_transaction_items.deleted_at')
            ->join('product_transaction_items', 'transactions.id', '=', 'product_transaction_items.transaction_id')
            ->selectRaw('transactions.id as transactions_id, job_order, product_transaction_items.name as item_name, product_transaction_items.price as item_price, transactions.date as _date')
            ->unionAll($serviceTransactions)
            ->orderBy('job_order')
            ->get();

        return response()->json([
            'result' => $productTransactions->groupBy('job_order'),
        ]);
    }

    // public function posCollections(Request $request) {
    //     $serviceTransactions = DB::table('transactions')
    //         ->whereDate('transactions.date_paid', $request->date)
    //         ->whereNull('transactions.deleted_at')
    //         ->whereNull('service_transaction_items.deleted_at')
    //         ->join('service_transaction_items', 'transactions.id', '=', 'service_transaction_items.transaction_id')
    //         ->selectRaw('transactions.id as transaction_id, job_order, date, customer_name, service_transaction_items.name as item_name, service_transaction_items.price as item_price, transactions.date_paid as _date');

    //     $result = DB::table('transactions')
    //         ->whereDate('transactions.date_paid', $request->date)
    //         ->whereNull('transactions.deleted_at')
    //         ->whereNull('product_transaction_items.deleted_at')
    //         ->join('product_transaction_items', 'transactions.id', '=', 'product_transaction_items.transaction_id')
    //         ->selectRaw('transactions.id as transactions_id, job_order, date, customer_name, product_transaction_items.name as item_name, product_transaction_items.price as item_price, transactions.date_paid as _date')
    //         ->unionAll($serviceTransactions)
    //         ->orderBy(DB::raw('job_order, item_name'))
    //         ->get();
    //         // ->groupBy('job_order');


    //     return view('printer.pos-collections', [
    //         'result' => $result,
    //     ]);
    // }

    public function printPosCollections(Request $request) {
        $result = Transaction::with('serviceTransactionItems', 'productTransactionItems', 'payment')
            ->whereDate('date_paid', $request->date)
            ->orderBy('job_order')
            ->get();

        $summary = [
            'totalCount' => $result->count(),
            'totalCollections' => $result->sum('total_price'),
        ];

        return view('printer.pos-collections', [
            'result' => $result,
            'summary' => $summary,
        ]);
    }

    public function printPosTransactions(Request $request) {
        $result = Transaction::with('serviceTransactionItems', 'productTransactionItems', 'payment')
            ->whereDate('date', $request->date)
            ->orderBy('job_order')
            ->get();

        $unpaid = $result->filter(function($item) {
            return $item->date_paid == null;
        });

        $paid = $result->filter(function($item) {
            return $item->date_paid != null;
        });

        $summary = [
            'totalCount' => $result->count(),
            'totalSales' => $result->sum('total_price'),
            'paidCount' => $paid->count(),
            'totalCollections' => $paid->sum('total_price'),
            'unpaidCount' => $unpaid->count(),
            'totalUnpaid' => $unpaid->sum('total_price'),
        ];

        return view('printer.pos-transactions', [
            'result' => $result,
            'summary' => $summary,
        ]);
    }

    public function printRfidTransactions(Request $request) {
        $result = RfidCardTransaction::whereDate('created_at', $request->date);

        if($request->cardType == 'c') {
            $result = $result->where('card_type', 'c');
        } else if($request->cardType == 'u') {
            $result = $result->where('card_type', 'u');
        }

        $result = $result->orderByDesc('created_at')->get();

        $customerCards = $result->filter(function($item) {
            return $item->card_type == 'c';
        });
        $userCards = $result->filter(function($item) {
            return $item->card_type == 'u';
        });

        $summary = [
            'customerCount' => $customerCards->count(),
            'customerCollections' => $customerCards->sum('price'),
            'userCount' => $userCards->count(),
            'userCollections' => $userCards->sum('price'),
            'totalCount' => $result->count(),
            'totalSales' => $result->sum('price'),
        ];

        return view('printer.rfid-transactions', [
            'result' => $result,
            'summary' => $summary,
        ]);
    }

    public function printRfidLoadTransactions(Request $request) {
        $result = RfidLoadTransaction::whereDate('created_at', $request->date)
            ->orderByDesc('created_at')->get();

        $summary = [
            'totalCount' => $result->count(),
            'totalPrice' => $result->sum('amount'),
        ];

        return view('printer.rfid-load-transactions', [
            'result' => $result,
            'summary' => $summary,
        ]);
    }
}
