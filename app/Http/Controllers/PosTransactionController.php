<?php

namespace App\Http\Controllers;

use App\CustomerDry;
use App\CustomerWash;
use App\DryingService;
use App\FullService;
use App\OtherService;
use App\Product;
use App\ProductTransactionItem;
use App\ServiceTransactionItem;
use App\Transaction;
use App\WashingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PosTransactionController extends Controller
{
    public function currentTransaction($customerId) {
        $transaction = Transaction::where(function($query) use ($customerId) {
            $query->whereDoesntHave('payment', function($query) {

            })->where('customer_id', $customerId);
        })->first();

        if($transaction) {
            $transaction->refreshAll();
        }


        return response()->json([
            'transaction' => $transaction
        ]);
    }

    public function services() {
        $washingServices = WashingService::orderBy('name')->get();
        $dryingServices = DryingService::orderBy('name')->get();
        $otherServices = OtherService::orderBy('name')->get();
        $fullServices = FullService::with('fullServiceItems', 'fullServiceProducts')->orderBy('name')->get();

        return response()->json([
            'washingServices' => $washingServices,
            'dryingServices' => $dryingServices,
            'otherServices' => $otherServices,
            'fullServices' => $fullServices,
        ]);
    }

    public function products() {
        $products = Product::orderBy('name')->get();

        return response()->json([
            'products' => $products,
        ]);
    }

    public function addService($category, Request $request) {
        return DB::transaction(function () use ($request, $category) {
            $transaction = Transaction::find($request->transactionId);
            if($transaction == null) {
                $transaction = Transaction::create([
                    'customer_id' => $request->customerId,
                    'user_id' => auth('api')->id(),
                ]);
            } else {
                $transaction->update([
                    'saved' => null,
                ]);
            }

            switch ($category) {
                case 'washing':
                    $item = WashingService::find($request->itemId);
                    break;
                case 'drying':
                    $item = DryingService::find($request->itemId);
                    break;
                case 'other':
                    $item = OtherService::find($request->itemId);
                    break;
                case 'full':
                    $item = FullService::find($request->itemId);
                    break;
            }

            if(!$item) {
                return response()->json([
                    'errors' => [
                        'message' => ['Item was not found or deleted.']
                    ]
                ], 422);
            }

            $transactionItem = ServiceTransactionItem::create([
                'transaction_id' => $transaction->id,
                'name' => $item->name,
                'price' => $item->price,
                'category' => $category,
                'saved' => false,
                'washing_service_id' => $category == 'washing' ? $request->itemId : null,
                'drying_service_id' => $category == 'drying' ? $request->itemId : null,
                'other_service_id' => $category == 'other' ? $request->itemId : null,
                'full_service_id' => $category == 'full' ? $request->itemId : null,
            ]);

            if($transaction) {
                $transaction->refreshAll();
            }


            return response()->json([
                'transaction' => $transaction,
            ]);
        });
    }

    public function addProduct(Request $request) {
        return DB::transaction(function () use ($request) {

            $product = Product::findOrFail($request->itemId);

            $transaction = Transaction::find($request->transactionId);
            if($transaction == null) {
                $transaction = Transaction::create([
                    'customer_id' => $request->customerId,
                    'user_id' => auth('api')->id(),
                ]);
            } else {
                $transaction->update([
                    'saved' => null,
                ]);
            }

            $transactionItem = ProductTransactionItem::create([
                'transaction_id' => $transaction->id,
                'name' => $product->name,
                'price' => $product->selling_price,
                'product_id' => $product->id,
            ]);

            $product->decrement('current_stock');

            if($transaction) {
                $transaction->refreshAll();
            }


            return response()->json([
                'transaction' => $transaction,
                'product' => $product,
            ]);
        });
    }

    public function reduceProducts(Request $request) {
        return DB::transaction(function () use ($request) {
            $productItem = ProductTransactionItem::where([
                'product_id' => $request->productId,
                'transaction_id' => $request->transactionId,
            ])->orderByDesc('created_at')->first();

            if($productItem->forceDelete()) {
                $product = Product::findOrFail($request->productId);
                $product->increment('current_stock');
            }

            return response()->json([
                'productItem' => $productItem
            ]);
        });
    }

    public function saveTransaction($transactionId) {

        return DB::transaction(function () use ($transactionId) {

            $transaction = Transaction::findOrFail($transactionId);
            if(!$transaction->job_order) {
                $transaction->attachJobOrder();
                $transaction->date = Carbon::now();
            }
            $transaction->saved = Carbon::now();
            $transaction->save();

            $washingServices = ServiceTransactionItem::with('washingService')->where([
                'category' => 'washing',
                'transaction_id' => $transactionId,
                'saved' => false,
            ])->get();

            $dryingServices = ServiceTransactionItem::with('dryingService')->where([
                'category' => 'drying',
                'transaction_id' => $transactionId,
                'saved' => false,
            ])->get();

            $fullServices = ServiceTransactionItem::with('fullService.fullServiceItems')->where([
                'category' => 'full',
                'transaction_id' => $transactionId,
                'saved' => false,
            ])->get();

            foreach ($washingServices as $item) {
                CustomerWash::create([
                    'service_name' => $item->name,
                    'customer_id' => $transaction->customer_id,
                    'service_transaction_item_id' => $item['id'],
                    'machine_type' => $item->washingService['machine_type'],
                    'minutes' => $item->washingService['regular_minutes'] + $item->washingService['additional_minutes'],
                    'pulse_count' => $item->washingService['additional_minutes'] ? 2 : 1,
                ]);

                $item->update([
                    'saved' => true,
                    'earning_points' => $item->washingService['points'],
                ]);
            }

            foreach ($dryingServices as $item) {
                CustomerDry::create([
                    'service_name' => $item->name,
                    'customer_id' => $transaction->customer_id,
                    'service_transaction_item_id' => $item['id'],
                    'machine_type' => $item->dryingService['machine_type'],
                    'minutes' => $item->dryingService['minutes'],
                    'pulse_count' => $item->dryingService['minutes'] / 10,
                ]);

                $item->update([
                    'saved' => true,
                    'earning_points' => $item->dryingService['points'],
                ]);
            }

            foreach ($fullServices as $item) {
                foreach ($item->fullService->fullServiceItems as $fullServiceItem) {
                    $earningPoints = 0;
                    if($fullServiceItem->category == 'drying') {

                        $dryingService = DryingService::find($fullServiceItem->drying_service_id);

                        for ($i=0; $i < $fullServiceItem->quantity; $i++) {

                            CustomerDry::create([
                                'service_name' => $dryingService->name . ' (Full service)',
                                'customer_id' => $transaction->customer_id,
                                'service_transaction_item_id' => $item['id'],
                                'machine_type' => $dryingService['machine_type'],
                                'minutes' => $dryingService['minutes'],
                                'pulse_count' => $dryingService['minutes'] / 10,
                            ]);
                            $earningPoints += $fullServiceItem->points;
                        }

                    }

                    if($fullServiceItem->category == 'washing') {

                        $washingService = WashingService::find($fullServiceItem->washing_service_id);

                        for ($i=0; $i < $fullServiceItem->quantity; $i++) {

                            CustomerWash::create([
                                'service_name' => $washingService->name . '(Full service)',
                                'customer_id' => $transaction->customer_id,
                                'service_transaction_item_id' => $item['id'],
                                'machine_type' => $washingService['machine_type'],
                                'minutes' => $washingService['regular_minutes'] + $washingService['additional_minutes'],
                                'pulse_count' => $washingService['additional_minutes'] ? 2 : 1,
                            ]);
                            $earningPoints += $fullServiceItem->points;
                        }
                    }
                }

                $item->update([
                    'saved' => true,
                    'earning_points' => $earningPoints,
                ]);
            }

            if($transaction) {
                $transaction->refreshAll();
            }


            return response()->json([
                'transaction' => $transaction,
            ]);
        });
    }
}