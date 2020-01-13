<?php

namespace App\Http\Controllers;

use App\ServiceTransactionItem;
use App\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionsController extends Controller
{
    public function viewServiceItems($transactionId, Request $request) {
        $result = ServiceTransactionItem::with('customerWash')->where([
            'transaction_id' => $transactionId,
            'name' => $request->serviceName,
        ])->orderBy('created_at')->get();

        $result = $result->transform(function($item) {
            return [
                'service_transaction_item_id' => $item->id,
                'name' => $item->name,
                'saved' => $item->saved,
                'added' => $item->created_at,
                'machine_name' => $item->machineName(),
                'used' => $item->used(),
                'created_at' => $item->created_at,
            ];
        });

        return response()->json([
            'result' => $result,
        ]);
    }

    public function show($transactionId) {
        $transaction = Transaction::with('customer')->findOrfail($transactionId);
        $transaction->refreshAll();

        return response()->json([
            'transaction' => $transaction
        ]);
    }

    public function unpaidTransactions(Request $request) {
        // $result = DB::table('transactions')
        //     ->where('customers.name', 'like', "%$request->keyword%")
        //     ->whereNotNull('saved')
        //     ->select('transactions.id','job_order', 'saved', 'total_price', 'customer_id', 'customers.id', 'customers.name')
        //     ->join('customers', 'customers.id', '=', 'customer_id')
        //     ->orderBy('name');

        $result = Transaction::where('customer_name', 'like', "%$request->keyword%")
            ->whereNotNull('saved')
            ->whereDoesntHave('payment')
            ->orderBy('customer_name');

        if($request->date) {
            $result = $result->whereDate('saved', $request->date);
        }

        return response()->json([
            'result' => $result->paginate(),
        ]);
    }
}
