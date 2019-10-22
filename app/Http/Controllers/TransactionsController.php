<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Transaction;
use App\ClientAuth;
use App\BranchProduct;
use App\ProductTransaction;
use App\ServiceTransaction;
use App\BranchService;
use Carbon\Carbon;
use App\Customer;
use App\BranchAuth;
use App\Token;
use App\CompletedServiceTransaction;
use App\CompletedProductTransaction;

class TransactionsController extends Controller
{
    public function customerTransaction($customerId, Request $request) {
        $transaction = Transaction::with('productTransactions.branchProduct.product', 'serviceTransactions.branchService.service', 'customer')
            ->where('branch_id', $request->branchId)
            ->where('customer_id', $customerId)
            ->whereNull('date_paid')->first();

        if($transaction == null) {
            return response()->json([
                'message' => 'Customer does not have any unpaid transactions.'
            ], 404);
        }

        $productItems = collect($transaction->productTransactions)->map(function($item) {
            return [
                'productTransactionId' => $item->id,
                'name' => $item->branchProduct->product->name,
                'quantity' => $item->quantity,
                'price' => $item->branchProduct->price * $item->quantity,
                'productId' => $item->branch_product_id,
                'saved' => $item->saved,
            ];
        });

        $serviceItems = collect($transaction->serviceTransactions)->map(function($item) {
            return [
                'serviceTransactionId' => $item->id,
                'name' => $item->branchService->service->name,
                'quantity' => $item->quantity,
                'price' => $item->branchService[$item->service_group . '_service_price'] * $item->quantity, //$item->price,
                'serviceId' => $item->branch_service_id,
                'saved' => $item->saved,
                'serviceGroup' => $item->service_group,
            ];
        });

        return response()->json([
            'services' => $serviceItems,
            'products' => $productItems,
            'transaction' => [
                'id' => $transaction->id,
                'date' => $transaction->date,
                'is_saved' => $transaction->isSaved(),
                'someSaved' => $transaction->someSaved(),
                'job_order' => $transaction->job_order,
                'hasItems' => count($serviceItems) || count($productItems),
                'customerName' => $transaction->customer['name'],
            ]
        ]);
    }

    public function createTransaction($customerId, Request $request) {

        return response()->json('This resource is commented. Litteraly');
        // $rules = [
        //     'quantity' => 'required',
        //     'productId' => 'required',
        // ];

        // if($request->validate($rules)) {
        //     return DB::transaction(function () use ($customerId, $request) {
        //         $branchProduct = BranchProduct::findOrFail($request->productId);
        //         $branchProduct = BranchProduct::with('product')->findOrFail($request->productId);
        //         $customer = Customer::findOrFail($customerId);

        //         if($branchProduct->available <= 0) {
        //             $err = 'No available stock.';
        //         } else if($branchProduct->available - $request->quantity < 0) {
        //             $err = 'Not enough stock.';
        //         }

        //         if(isset($err)){
        //             return response()->json([
        //                 'errors' => [
        //                     'quantity' => [$err]
        //                 ]
        //             ], 422);
        //         }

        //         $transaction = Transaction::create([
        //             'date' => Carbon::now(),
        //             'customer_id' => $customer->id,
        //             'branch_id' => $branchProduct->branch_id,
        //         ]);

        //         $transactionItem = TransactionItem::create([
        //             'transaction_id' => $transaction->id,
        //             'added_by' => auth('api')->id(),
        //             'branch_product_id' => $branchProduct->id,
        //             'quantity' => $request->quantity
        //         ]);

        //         return response()->json([
        //             'transactionItem' => [
        //                 'transactionItemId' => $transactionItem->id,
        //                 'name' => $branchProduct->product->name,
        //                 'quantity' => $request->quantity,
        //                 'price' => $branchProduct->price * $request->quantity,
        //                 'productId' => $branchProduct->product_id,
        //                 'saved' => false,
        //             ]
        //         ], 200);
        //     });
        // }
    }

    public function addService($id, Request $request) {
        $transaction = Transaction::with('branch', 'customer')->find($id);

        if($transaction && !$transaction->created_at->isToday()) {
            return response()->json([
                'errors' => [
                    'message' => ['Cannot add item from other`s day transaction.'],
                ]
            ], 422);
        }

        $rules = [
            'quantity' => 'required',
            'serviceId' => 'required',
            'serviceGroup' => 'required',        // whether full service or self service
        ];

        if($request->validate($rules)) {
            return DB::transaction(function () use ($transaction, $request) {
                $branchService = BranchService::findOrFail($request->serviceId);

                if($transaction != null) {
                    if($transaction->branch_id != $branchService->branch_id) {
                        return response()->json([
                            'errors' => [
                                'quantity' => ['Cannot add item from different branch.']
                            ]
                        ], 422);
                    }
                }

                if($transaction == null) {
                    if(!$request->customerId) {
                        return response()->json([
                            'errors' => [
                                'customerId' => ['Customer is required.']
                            ]
                        ], 422);
                    }

                    $transaction = Transaction::create([
                        'date' => Carbon::now(),
                        'customer_id' => $request->customerId,
                        'branch_id' => $branchService->branch_id,
                    ]);
                }

                $price = $branchService["{$request->serviceGroup}_service_price"] * $request->quantity; // == 'fullservice' ? $branchService->full_service_price : $branchService->self_service_price

                // check if service transaction is already in the list
                $serviceTransaction = ServiceTransaction::where([
                    'transaction_id' => $transaction->id,
                    'branch_service_id' => $branchService->id,
                    'saved' => false
                ])->first();

                if($serviceTransaction == null) {
                    // create new service transaction item
                    $serviceTransaction = ServiceTransaction::create([
                        'transaction_id' => $transaction->id,
                        'added_by' => auth('api')->id(),
                        'branch_service_id' => $branchService->id,
                        'quantity' => $request->quantity,
                        'service_group' => $request->serviceGroup,
                    ]);
                } else {
                    $serviceTransaction->update([
                        'quantity' => DB::raw('quantity+' . $request->quantity),
                        'saved' => false,
                    ]);
                }

                $transaction->update([
                    'date_saved' => null,
                ]);


                return response()->json([
                    'serviceTransaction' => [
                        'serviceTransactionId' => $serviceTransaction->id,
                        'name' => $branchService->service->name,
                        'quantity' => $request->quantity,
                        'price' => $price,
                        'serviceId' => $branchService->service_id,
                        'saved' => false,
                        'serviceGroup' => $serviceTransaction->service_group,
                    ],
                    'transaction' => [
                        'date' => $transaction->date,
                        'id' => $transaction->id,
                        'is_saved' => false,
                        'someSaved' => $transaction->someSaved(),
                        'job_order' => $transaction->job_order,
                        'hasItems' => true,
                        'customerName' => $transaction->customer['name'],
                    ]
                ], 200);
            });
        }
    }

    public function addOrder($id, Request $request) {
        $transaction = Transaction::with('branch', 'customer')->find($id);


        if($transaction && !$transaction->created_at->isToday()) {
            return response()->json([
                'errors' => [
                    'message' => ['Cannot add item from other`s day transaction.'],
                ]
            ], 422);
        }

        $rules = [
            'quantity' => 'required',
            'productId' => 'required',
        ];

        if($request->validate($rules)) {
            return DB::transaction(function () use ($transaction, $request) {
                $branchProduct = BranchProduct::findOrFail($request->productId);

                if($transaction != null) {
                    if($transaction->branch_id != $branchProduct->branch_id) {
                        return response()->json([
                            'errors' => [
                                'quantity' => ['Cannot add item from different branch.']
                            ]
                        ], 422);
                    }
                }

                if($transaction == null) {
                    if(!$request->customerId) {
                        return response()->json([
                            'errors' => [
                                'customerId' => ['Customer is required.']
                            ]
                        ], 422);
                    }

                    $transaction = Transaction::create([
                        'date' => Carbon::now(),
                        'customer_id' => $request->customerId,
                        'branch_id' => $branchProduct->branch_id,
                    ]);
                }

                $branchProduct = BranchProduct::with('product')->findOrFail($request->productId);

                if($branchProduct->available <= 0) {
                    $err = 'No available stock.';
                } else if($branchProduct->available - $request->quantity < 0) {
                    $err = 'Not enough stock.';
                }

                if(isset($err)){
                    return response()->json([
                        'errors' => [
                            'quantity' => [$err]
                        ]
                    ], 422);
                }

                $transactionItem = ProductTransaction::where([
                    'transaction_id' => $transaction->id,
                    'branch_product_id' => $branchProduct->id,
                    'saved' => false,
                ])->first();

                if($transactionItem == null) {
                    $transactionItem = ProductTransaction::create([
                        'transaction_id' => $transaction->id,
                        'added_by' => auth('api')->id(),
                        'branch_product_id' => $branchProduct->id,
                        'quantity' => $request->quantity
                    ]);
                } else {
                    $transactionItem->update([
                        'quantity' => DB::raw('quantity+' . $request->quantity),
                        'saved' => false,
                    ]);
                }

                $transaction->update([
                    'date_saved' => null,
                ]);

                return response()->json([
                    'productTransaction' => [
                        'productTransactionId' => $transactionItem->id,
                        'name' => $branchProduct->product->name,
                        'quantity' => $request->quantity,
                        'price' => $branchProduct->price * $request->quantity,
                        'productId' => $branchProduct->product_id,
                        'saved' => false,
                    ],
                    'transaction' => [
                        'date' => $transaction->date,
                        'id' => $transaction->id,
                        'is_saved' => false,
                        'someSaved' => $transaction->someSaved(),
                        'job_order' => $transaction->job_order,
                        'hasItems' => true,
                        'customerName' => $transaction->customer['name'],
                    ]
                ], 200);
            });
        }
    }

    public function saveCurrentTransaction($transactionId) {
        $transaction = Transaction::with(['customer','serviceTransactions.branchService.service', 'serviceTransactions' => function($query) {
            $query->where('saved', false);
        }, 'productTransactions' => function($query) {
            $query->where('saved', false);
        }])->findOrFail($transactionId);

        ClientAuth::check($transaction->customer->client_id);
        BranchAuth::check($transaction->branch_id);

        return DB::transaction(function () use ($transaction) {
            $customer = $transaction->customer;
            foreach($transaction->serviceTransactions as $serviceTransaction) {

                // for ($i=0; $i < $serviceTransaction->quantity; $i++) {

                    // }

                // check for update
                // $completedServiceTransaction = CompletedServiceTransaction::where([
                //     // 'service_transaction_id' => $serviceTransaction->id,
                //     'transaction_id' => $transaction->id,
                //     'branch_service_id' => $serviceTransaction->branch_service_id,
                // ])->first();

                // if($completedServiceTransaction == null) {

                for ($i = 0; $i < $serviceTransaction->quantity; $i++) {
                    CompletedServiceTransaction::create([
                        'customer_id' => $customer->id,
                        'branch_id' => $transaction->branch_id,
                        'service_id' => $serviceTransaction->branchService->service_id,
                        'service_type_id' => $serviceTransaction->branchService->service->service_type_id,
                        'user_id' => auth('api')->id(),
                        'transaction_id' => $transaction->id,
                        'service_transaction_id' => $serviceTransaction->id,
                        'price_sum' => $serviceTransaction->branchService[$serviceTransaction->service_group . '_service_price'],// * $serviceTransaction->quantity,
                        'quantity' => 1, //$serviceTransaction->quantity,
                        'points' => $serviceTransaction->branchService->points, // * $serviceTransaction->quantity,
                        'add_super_wash' => $serviceTransaction->branchService->add_super_wash,
                        'branch_service_id' => $serviceTransaction->branch_service_id,
                        'available' => 1, // $serviceTransaction->quantity,
                        'job_order' => $transaction->job_order,
                    ]);

                }

                // } else {
                //     $completedServiceTransaction->update([
                //         'user_id' => auth('api')->id(),
                //         'price_sum' => DB::raw('price_sum+' . $serviceTransaction->branchService[$serviceTransaction->service_group . '_service_price'] * $serviceTransaction->quantity),
                //         'quantity' => DB::raw('quantity+' . $serviceTransaction->quantity),
                //         'points' => DB::raw('points+' . $serviceTransaction->branchService->points * $serviceTransaction->quantity),
                //         //
                //         'available' => DB::raw('available+' . $serviceTransaction->quantity),
                //     ]);
                // }

                // Token::create([
                //     'customer_id' => $customer->id,
                //     'branch_id' => $transaction->branch_id,
                //     'service_transaction_id' => $serviceTransaction->id,
                //     'completed_service_transaction_id' => $serviceTransaction->id,
                //     'service_type_id' => $serviceTransaction->branchService->service->service_type_id,
                //     'pulse_count' => $serviceTransaction->branchService->pulse_count * $serviceTransaction->quantity,
                //     'minutes_per_pulse' => $serviceTransaction->branchService->minutes_per_pulse, // * $serviceTransaction->quantity * $serviceTransaction->branchService->pulse_count,
                //     'add_super_wash' => $serviceTransaction->branchService->add_super_wash,
                // ]);

                $serviceTransaction->update([
                    'saved' => true,
                    'added_by' => auth('api')->id(),
                    'unit_price' => $serviceTransaction->branchService[$serviceTransaction->service_group . '_service_price'],
                ]);
            }

            foreach($transaction->productTransactions as $productTransaction) {

                // $completedProductTransaction = CompletedProductTransaction::where([
                //     // 'product_transaction_id' => $productTransaction->id,
                //     'transaction_id' => $transaction->id,
                //     'branch_product_id' => $productTransaction->branch_product_id,
                // ])->first();

                // if($completedProductTransaction == null) {

                for ($i=0; $i < $productTransaction->quantity; $i++) {
                    CompletedProductTransaction::create([
                        'customer_id' => $customer->id,
                        'branch_id' => $transaction->branch_id,
                        'product_id' => $productTransaction->branchProduct->product_id,
                        'user_id' => auth('api')->id(),
                        'transaction_id' => $transaction->id,
                        'product_transaction_id' => $productTransaction->id,
                        'price_sum' => $productTransaction->branchProduct->price, // * $productTransaction->quantity,
                        'quantity' => 1, // $productTransaction->quantity,
                        'branch_product_id' => $productTransaction->branch_product_id,
                        'job_order' => $transaction->job_order,
                    ]);
                }
                // } else {
                //     $completedProductTransaction->update([
                //         'user_id' => auth('api')->id(),
                //         'price_sum' => DB::raw('price_sum+' . $productTransaction->branchProduct->price * $productTransaction->quantity),
                //         'quantity' => DB::raw('quantity+' . $productTransaction->quantity),
                //     ]);
                // }


                $productTransaction->update([
                    'saved' => true,
                    'added_by' => auth('api')->id(),
                    'price' => $productTransaction->branchProduct->price,
                ]);
            }

            $transaction->attachJobOrder();
            $transaction->update([
                'date_saved' => Carbon::now(),
                'user_id' => auth('api')->id(),
            ]);

            // $s = $transaction->serviceTransactions()->update([
            //     'saved' => true,
            //     'added_by' => auth('api')->id(),
            // ]);
            // $p = $transaction->productTransactions()->update([
            //     'saved' => true,
            //     'added_by' => auth('api')->id(),
            // ]);

            return response()->json([
                'messgae' => 'Transaction saved.',
                // 'serviceTransactions' => $transaction->serviceTransactions,
                // 'productTransactions' => $transaction->productTransactions,
            ], 200);
        });

    }

    public function removeServiceItem($serviceItemId) {
        $serviceItem = ServiceTransaction::findOrFail($serviceItemId);
        return DB::transaction(function () use ($serviceItem) {
            if($serviceItem->forceDelete()) {
                $transaction = Transaction::with('productTransactions', 'serviceTransactions')->find($serviceItem->transaction_id);
                $cleared = false;

                if($transaction->serviceTransactions()->count() == 0 && $transaction->productTransactions()->count() == 0) {
                    $cleared = $transaction->forceDelete();
                }

                return response()->json([
                    'serviceItem' => $serviceItem,
                    'cleared' => $cleared,
                ], 200);
            }
        });
    }

    public function removeProductItem($productItemId) {
        $productItem = ProductTransaction::findOrFail($productItemId);
        return DB::transaction(function () use ($productItem) {
            if($productItem->forceDelete()) {
                $transaction = Transaction::with('productTransactions', 'serviceTransactions')->find($productItem->transaction_id);
                $cleared = false;

                if($transaction->serviceTransactions()->count() == 0 && $transaction->productTransactions()->count() == 0) {
                    $cleared = $transaction->forceDelete();
                }

                return response()->json([
                    'productItem' => $productItem,
                    'cleared' => $cleared,
                ], 200);
            }
        });
    }
}
