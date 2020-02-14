<?php

namespace App\Http\Controllers;

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

    public function posCollections(Request $request) {
        $serviceTransactions = DB::table('transactions')
            ->whereDate('transactions.date_paid', $request->date)
            ->whereNull('transactions.deleted_at')
            ->whereNull('service_transaction_items.deleted_at')
            ->join('service_transaction_items', 'transactions.id', '=', 'service_transaction_items.transaction_id')
            ->selectRaw('transactions.id as transaction_id, job_order, service_transaction_items.name as item_name, service_transaction_items.price as item_price, transactions.date_paid as _date');

        $productTransactions = DB::table('transactions')
            ->whereDate('transactions.date_paid', $request->date)
            ->whereNull('transactions.deleted_at')
            ->whereNull('product_transaction_items.deleted_at')
            ->join('product_transaction_items', 'transactions.id', '=', 'product_transaction_items.transaction_id')
            ->selectRaw('transactions.id as transactions_id, job_order, product_transaction_items.name as item_name, product_transaction_items.price as item_price, transactions.date_paid as _date')
            ->unionAll($serviceTransactions)
            ->orderBy('job_order')
            ->get();

        return response()->json([
            'result' => $productTransactions->groupBy('job_order'),
        ]);
    }
}
