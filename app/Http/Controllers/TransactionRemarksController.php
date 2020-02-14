<?php

namespace App\Http\Controllers;

use App\Transaction;
use App\TransactionRemarks;
use Illuminate\Http\Request;

class TransactionRemarksController extends Controller
{
    public function index($transactionId) {
        $transaction = Transaction::with(['remarks' => function($query) {
            $query->orderBy('created_at');
        }])
            ->findOrFail($transactionId);

        return response()->json([
            'transaction' => $transaction,
        ]);
    }

    public function store($transactionId, Request $request) {
        $rules = [
            'remarks' => 'required',
        ];

        if($request->validate($rules)) {
            $remarks = TransactionRemarks::create([
                'transaction_id' => $transactionId,
                'remarks' => $request->remarks,
                'added_by' => auth('api')->user()->name,
            ]);

            return response()->json([
                'remarks' => $remarks,
            ]);
        }
    }

    public function deleteRemarks($remarksId) {
        $remarks = TransactionRemarks::findOrFail($remarksId);
        if($remarks->delete()) {
            return response()->json([
                'remarks' => $remarks
            ]);
        }
    }
}
