<?php

namespace App\Http\Controllers;

use App\ServiceTransactionItem;
use App\Transaction;
use Illuminate\Http\Request;

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
        $transaction = Transaction::findOrfail($transactionId);

        if($transaction) {
            $transaction['posServiceItems'] = $transaction->posServiceItems();
            $transaction['posServiceSummary'] = $transaction->posServiceSummary();
        }


        return response()->json([
            'transaction' => $transaction
        ]);
    }
}
