<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CompletedServiceTransaction;
use App\BranchAuth;
use App\ServiceItemRemarks;
use Illuminate\Support\Facades\DB;
use App\ServiceTransaction;
use App\CompletedProductTransaction;
use App\ProductTransaction;
use App\ProductItemRemarks;
use App\Transaction;
use App\TransactionRemarks;

class VoidTransactionsController extends Controller
{
    public function voidService($completedServiceTransactionId, Request $request) {
        $completedServiceTransaction = CompletedServiceTransaction::with('service')->findOrFail($completedServiceTransactionId);

        BranchAuth::check($completedServiceTransaction->branch_id);

        if($completedServiceTransaction->date_paid) {
            return response()->json([
                'errors' => [
                    'remarks' => ['Cannot void paid services']
                ]
            ], 422);
        }

        $rules = [
            'remarks' => 'required',
        ];

        if($request->validate($rules)) {
            return DB::transaction(function () use ($completedServiceTransaction, $request) {
                ServiceItemRemarks::create([
                    'completed_service_transaction_id' => $completedServiceTransaction->id,
                    'item_name' => $completedServiceTransaction->service['name'],
                    'remarks' => $request->remarks,
                    'user_id' => auth('api')->id(),
                    'branch_id' => auth('api')->user()->active_branch_id,
                    'job_order' => $completedServiceTransaction->job_order,
                    'transaction_id' => $completedServiceTransaction->transaction_id,
                    'customer_name' => $completedServiceTransaction->customer['name'],
                ]);
                $serviceTransaction = ServiceTransaction::findOrFail($completedServiceTransaction->service_transaction_id);
                if($serviceTransaction->quantity == 1) {
                    $serviceTransaction->delete();
                } else {
                    $serviceTransaction->decrement('quantity');
                }
                $completedServiceTransaction->delete();

                return response()->json([
                    'id' => $completedServiceTransaction->id,
                ], 200);
            });
        }
    }

    public function voidProduct($completedProductTransactionId, Request $request) {
        $completedProductTransaction = CompletedProductTransaction::with('product', 'customer')->findOrFail($completedProductTransactionId);

        BranchAuth::check($completedProductTransaction->branch_id);

        if($completedProductTransaction->date_paid) {
            return response()->json([
                'errors' => [
                    'remarks' => ['Cannot void paid products']
                ]
            ], 422);
        }

        $rules = [
            'remarks' => 'required',
        ];

        if($request->validate($rules)) {
            return DB::transaction(function () use ($completedProductTransaction, $request) {
                ProductItemRemarks::create([
                    'completed_product_transaction_id' => $completedProductTransaction->id,
                    'item_name' => $completedProductTransaction->product['name'],
                    'remarks' => $request->remarks,
                    'user_id' => auth('api')->id(),
                    'branch_id' => auth('api')->user()->active_branch_id,
                    'job_order' => $completedProductTransaction->job_order,
                    'transaction_id' => $completedProductTransaction->transaction_id,
                    'customer_name' => $completedProductTransaction->customer['name'],
                ]);
                $productTransaction = ProductTransaction::findOrFail($completedProductTransaction->product_transaction_id);
                if($productTransaction->quantity == 1) {
                    $productTransaction->delete();
                } else {
                    $productTransaction->decrement('quantity');
                }
                $completedProductTransaction->delete();

                return response()->json([
                    'id' => $completedProductTransaction->id,
                ], 200);
            });
        }
    }

    public function voidTransaction($transactionId, Request $request) {
        $transaction = Transaction::with('customer')->findOrFail($transactionId);

        BranchAuth::check($transaction->branch_id);


        if($transaction->date_paid) {
            return response()->json([
                'errors' => [
                    'remarks' => ['Cannot void paid transaction']
                ]
            ], 422);
        }

        $rules = [
            'remarks' => 'required',
        ];

        if($request->validate($rules)) {
            return DB::transaction(function () use ($transaction, $request) {
                $transactionRemarks = TransactionRemarks::create([
                    'transaction_id' => $transaction->id,
                    'user_id' => auth('api')->id(),
                    'branch_id' => $transaction->branch_id,
                    'remarks' => $request->remarks,
                    'job_order' => $transaction->job_order,
                    'remarks_type' => 'v',
                    'customer_name' => $transaction->customer['name'],
                ]);

                if(!$transaction->delete()) {
                    DB::rollback();
                    return response()->json([
                        'errors' => [
                            'remarks' => ['Cannot void transaction. One or more services are already used.']
                        ]
                    ], 422);
                }

                return response()->json([
                    'id' => $transaction->id,
                ], 200);
            });
        }
    }

    public function getTransaction($transactionId) {
        $transaction = Transaction::findOrFail($transactionId);

        BranchAuth::check($transaction->branch_id);

        return response()->json([
            'transaction' => [
                'id' => $transaction->id,
                'date' => $transaction->date,
                'is_saved' => $transaction->isSaved(),
                'someSaved' => $transaction->someSaved(),
                'job_order' => $transaction->job_order,
                'customerName' => $transaction->customer['name'],
            ]
        ]);
    }
}
