<?php

namespace App\Http\Controllers;

use App\Customer;
use App\CustomerDry;
use App\CustomerWash;
use App\DryingService;
use App\FullService;
use App\Jobs\SendTransaction;
use App\Lagoon;
use App\LagoonPerKilo;
use App\LagoonPerKiloTransactionItem;
use App\LagoonTransactionItem;
use App\OtherService;
use App\Product;
use App\ProductTransactionItem;
use App\ServiceTransactionItem;
use App\ScarpaCategory;
use App\ScarpaCleaningTransactionItem;
use App\ScarpaVariation;
use App\Transaction;
use App\WashingService;
use App\MonitorChecker;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PosTransactionController extends Controller
{
    public function currentTransaction($customerId) {
        $transaction = Transaction::with('customer', 'partialPayment')
            ->whereNull('date_paid')
            ->whereNull('cancelation_remarks')
            ->where('customer_id', $customerId)
            ->first();

        if($transaction) {
            $transaction->refreshAll();
            $transaction['birthdayToday'] = Carbon::createFromDate($transaction->customer['first_visit'])->setYear(date('Y'))->isToday();
        }

        // $monitorChecker = MonitorChecker::firstOrCreate(['id' => 'default']);

        if($transaction != null) {
            MonitorChecker::hasQue($transaction->id);
            // $monitorChecker->update([
            //     'transaction_id' => $transaction->id,
            //     'job_order' => $transaction->job_order,
            //     'token' => $transaction->id,
            //     'action' => 'hasQue',
            // ]);
        } else {
            MonitorChecker::selectCustomer($customerId);
            // $monitorChecker->update([
            //     'token' => $customerId,
            //     'action' => 'select-customer',
            // ]);
        }

        return response()->json([
            'transaction' => $transaction,
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
        // return response()->json(env('RESTRICT_ADD_SERVICE_DIFF_DATE', 'test'));
        return DB::transaction(function () use ($request, $category) {
            $transaction = Transaction::find($request->transactionId);
            $customer = Customer::findOrFail($request->customerId);

            if($transaction && env('RESTRICT_ADD_SERVICE_DIFF_DATE', true) && (!Carbon::createFromDate($transaction->date)->isToday() || !$transaction->created_at->isToday())) {
                DB::rollback();
                return response()->json([
                    'errors' => [
                        'message' => ['Cannot add item from other`s day transaction.'],
                    ]
                ], 422);
            }

            if($transaction == null) {
                $transaction = Transaction::create([
                    'customer_id' => $request->customerId,
                    'user_id' => auth('api')->id(),
                    'staff_name' => auth('api')->user()->name,
                    'customer_name' => $customer->name,
                ]);
            } else {
                $transaction->update([
                    'saved' => false,
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
                    $item = FullService::with('fullServiceProducts', 'fullServiceItems')->find($request->itemId);
                    break;
            }

            if(!$item) {
                return response()->json([
                    'errors' => [
                        'message' => ['Item was not found or deleted.']
                    ]
                ], 422);
            }

            if($category == 'full') {
                if($item) {

                    $fullServiceItems = $item->fullServiceItems;
                    $fullServiceProducts = $item->fullServiceProducts;

                    $_fullServiceItems = null;
                    $_fullServiceProducts = null;

                    foreach($fullServiceItems as $fullServiceItem) {
                        for($i = 1; $i <= $fullServiceItem->quantity; $i++) {
                            $_fullServiceItems[] = ServiceTransactionItem::create([
                                'transaction_id' => $transaction->id,
                                'name' => $fullServiceItem->name . ' (' . $item->name . ')',
                                'price' => $fullServiceItem->price,
                                'category' => $fullServiceItem->category,
                                'earning_points' => $fullServiceItem->points,
                                'washing_service_id' => $fullServiceItem->washing_service_id,
                                'drying_service_id' => $fullServiceItem->drying_service_id,
                                'other_service_id' => $fullServiceItem->other_service_id,
                            ]);
                        }
                    }

                    foreach($fullServiceProducts as $fullServiceProduct) {
                        for($i = 1; $i <= $fullServiceProduct->quantity; $i++) {
                            $_fullServiceProducts[] = ProductTransactionItem::create([
                                'transaction_id' => $transaction->id,
                                'name' => $fullServiceProduct->name . ' (' . $item->name . ')',
                                'price' => $fullServiceProduct->price, // $fullServiceProduct->quantity,
                                'product_id' => $fullServiceProduct->product_id,
                            ]);
                        }
                        $product = Product::find($fullServiceProduct->product_id);
                        if($product) {
                            $product->decrement('current_stock', $fullServiceProduct->quantity);
                            if($product->current_stock < 0) {
                                DB::rollback();
                                return response()->json([
                                    'errors' => [
                                        'message' => ["Not enough stock in inventory for $product->name"]
                                    ]
                                ], 422);
                            }
                        }
                    }
                }
            } else {
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
            }

            if($transaction) {
                $transaction->refreshAll();
            }

            $this->dispatch((new SendTransaction($transaction->id))->delay(5));

            MonitorChecker::hasQue($transaction->id);

            return response()->json([
                'transaction' => $transaction,
            ]);
        });
    }

    public function addPerKilo($category, Request $request) {
        $rules = [
            'kilo' => 'required|numeric',
            'itemId' => 'required',
            'customerId' => 'required'
        ];

        if($request->validate($rules)) {
            return 
            DB::transaction(function () use ($category, $request) {
                $transaction = Transaction::find($request->transactionId);
                $customer = Customer::findOrFail($request->customerId);
                $transactionItems = [];
                $item = null;
                $kilo = null;
                $price = 0;

                if($transaction == null) {
                    $transaction = Transaction::create([
                        'customer_id' => $request->customerId,
                        'user_id' => auth('api')->id(),
                        'staff_name' => auth('api')->user()->name,
                        'customer_name' => $customer->name,
                    ]);
                } else {
                    $transaction->update([
                        'saved' => false,
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
                        $item = FullService::with('fullServiceProducts', 'fullServiceItems')->find($request->itemId);
                        break;
                }

                $price = $item->pricePerKilo * $request->kilo;
                $kilo = $request->kilo / $request->load;

                for($i = 0; $i <= $request->load; $i++) {
                    $transactionItems[] = ServiceTransactionItem::create([
                        'transaction_id' => $transaction->id,
                        'name' => "($kilo KG) $item->name",
                        'price' => $price,
                        'category' => $category,
                        'saved' => false,
                        'washing_service_id' => $category == 'washing' ? $request->itemId : null,
                        'drying_service_id' => $category == 'drying' ? $request->itemId : null,
                        'other_service_id' => $category == 'other' ? $request->itemId : null,
                        'full_service_id' => $category == 'full' ? $request->itemId : null,
                    ]);
                }

                MonitorChecker::hasQue($transaction->id);

                return response()->json($transactionItems);
            });
        }
    }

    public function addProduct(Request $request) {
        return DB::transaction(function () use ($request) {

            $product = Product::findOrFail($request->itemId);

            if($product->current_stock <= 0) {
                return response()->json([
                    'errors' => [
                        'message' => [$product->name . ' is not available']
                    ]
                ], 422);
            }

            $transaction = Transaction::find($request->transactionId);
            $customer = Customer::findOrFail($request->customerId);

            if($transaction && env('RESTRICT_ADD_SERVICE_DIFF_DATE', true) && (!Carbon::createFromDate($transaction->date)->isToday() || !$transaction->created_at->isToday())) {
                DB::rollback();
                return response()->json([
                    'errors' => [
                        'message' => ['Cannot add item from other`s day transaction.'],
                    ]
                ], 422);
            }

            if($transaction && $transaction->partialPayment) {
                return response()->json([
                    'errors' => [
                        'message' => ['Cannot add item to partially paid Job Order.'],
                    ]
                ], 422);
            }

            if($transaction == null) {
                $transaction = Transaction::create([
                    'customer_id' => $request->customerId,
                    'user_id' => auth('api')->id(),
                    'staff_name' => auth('api')->user()->name,
                    'customer_name' => $customer->name,
                ]);
            } else {
                $transaction->update([
                    'saved' => false,
                ]);
            }


            $transactionItem = ProductTransactionItem::create([
                'transaction_id' => $transaction->id,
                'name' => $product->name,
                'price' => $product->selling_price,
                'product_id' => $product->id,
            ]);

            // $product->decrement('current_stock');

            $product->update([
                'synched' => null,
                'current_stock' => DB::raw('current_stock - 1')
            ]);

            // TransactionRemarks::create([
            //     'transaction_id' => $transaction->id,
            //     'remarks' => 'Item removed(' . $product->name . ')',
            //     'added_by' => auth('api')->user()->name,
            // ]);

            if($transaction) {
                $transaction->refreshAll();
            }

            $this->dispatch((new SendTransaction($transaction->id))->delay(5));
            $this->dispatch($product->queSynch());

            MonitorChecker::hasQue($transaction->id);

            return response()->json([
                'transaction' => $transaction,
                'product' => $product->fresh(),
            ]);
        });
    }

    public function scarpaCleanings() {
        $result = ScarpaCategory::orderBy('name')->get();
        return response()->json([
            'result' => $result,
        ]);
    }

    public function scarpaVariations($serviceId, $groupBy) {
        $variations = ScarpaVariation::where('scarpa_category_id', $serviceId)
            ->get();

        return response()->json([
            'variations' => $variations->groupBy($groupBy),
        ]);
    }

    public function addScarpaCleaning($variationId, Request $request) {
        return DB::transaction(function () use ($variationId, $request) {
            $transaction = Transaction::find($request->transactionId);
            $customer = Customer::findOrFail($request->customerId);

            $variation = ScarpaVariation::with('scarpaCategory')->findOrFail($variationId);

            if($transaction && env('RESTRICT_ADD_SERVICE_DIFF_DATE', true) && (!Carbon::createFromDate($transaction->date)->isToday() || !$transaction->created_at->isToday())) {
                DB::rollback();
                return response()->json([
                    'errors' => [
                        'message' => ['Cannot add item from other`s day transaction.'],
                    ]
                ], 422);
            }

            if($transaction && $transaction->partialPayment) {
                return response()->json([
                    'errors' => [
                        'message' => ['Cannot add item to partially paid Job Order.'],
                    ]
                ], 422);
            }

            if($transaction == null) {
                $transaction = Transaction::create([
                    'customer_id' => $request->customerId,
                    'user_id' => auth('api')->id(),
                    'staff_name' => auth('api')->user()->name,
                    'customer_name' => $customer->name,
                ]);
            } else {
                $transaction->update([
                    'saved' => false,
                ]);
            }

            $scarpaCleaningTransactionItem = ScarpaCleaningTransactionItem::create([
                'transaction_id' => $transaction->id,
                'scarpa_category_id' => $variation->scarpa_category_id,
                'scarpa_variation_id' => $variationId,
                'name' => $variation->scarpaCategory->name . " (" . $variation->action . ")",
                // 'size' => $variation->size,
                // 'color' => $variation->color,
                'price' => $variation->selling_price,
            ]);

            if($transaction) {
                $transaction->refreshAll();
            }

            $this->dispatch((new SendTransaction($transaction->id))->delay(5));

            MonitorChecker::hasQue($transaction->id);

            return response()->json([
                'transaction' => $transaction,
            ]);
        });
    }

    public function addLagoon(Request $request) {
        return DB::transaction(function () use ($request) {

            $lagoon = Lagoon::findOrFail($request->itemId);

            $transaction = Transaction::find($request->transactionId);
            $customer = Customer::findOrFail($request->customerId);

            if($transaction && env('RESTRICT_ADD_SERVICE_DIFF_DATE', true) && (!Carbon::createFromDate($transaction->date)->isToday() || !$transaction->created_at->isToday())) {
                DB::rollback();
                return response()->json([
                    'errors' => [
                        'message' => ['Cannot add item from other`s day transaction.'],
                    ]
                ], 422);
            }

            if($transaction && $transaction->partialPayment) {
                return response()->json([
                    'errors' => [
                        'message' => ['Cannot add item to partially paid Job Order.'],
                    ]
                ], 422);
            }

            if($transaction == null) {
                $transaction = Transaction::create([
                    'customer_id' => $request->customerId,
                    'user_id' => auth('api')->id(),
                    'staff_name' => auth('api')->user()->name,
                    'customer_name' => $customer->name,
                ]);
            } else {
                $transaction->update([
                    'saved' => false,
                ]);
            }


            $transactionItem = LagoonTransactionItem::create([
                'transaction_id' => $transaction->id,
                'name' => $lagoon->category . '(' . $lagoon->name . ')',
                'price' => $lagoon->price,
                'lagoon_id' => $lagoon->id,
            ]);

            if($transaction) {
                $transaction->refreshAll();
            }

            $this->dispatch((new SendTransaction($transaction->id))->delay(5));

            MonitorChecker::hasQue($transaction->id);

            return response()->json([
                'transaction' => $transaction,
            ]);
        });
    }

    public function addLagoonPerKilo(Request $request) {
        return DB::transaction(function () use ($request) {

            $lagoon = LagoonPerKilo::findOrFail($request->itemId);

            $transaction = Transaction::find($request->transactionId);
            $customer = Customer::findOrFail($request->customerId);

            if($transaction && env('RESTRICT_ADD_SERVICE_DIFF_DATE', true) && (!Carbon::createFromDate($transaction->date)->isToday() || !$transaction->created_at->isToday())) {
                DB::rollback();
                return response()->json([
                    'errors' => [
                        'message' => ['Cannot add item from other`s day transaction.'],
                    ]
                ], 422);
            }

            if($transaction && $transaction->partialPayment) {
                return response()->json([
                    'errors' => [
                        'message' => ['Cannot add item to partially paid Job Order.'],
                    ]
                ], 422);
            }

            if($transaction == null) {
                $transaction = Transaction::create([
                    'customer_id' => $request->customerId,
                    'user_id' => auth('api')->id(),
                    'staff_name' => auth('api')->user()->name,
                    'customer_name' => $customer->name,
                ]);
            } else {
                $transaction->update([
                    'saved' => false,
                ]);
            }

            $transactionItem = LagoonPerKiloTransactionItem::where([
                'transaction_id' => $transaction->id,
                'lagoon_per_kilo_id' => $request->itemId,
            ])->first();
            if($transactionItem != null) {
                $transactionItem->update([
                    'kilos' => DB::raw('kilos+' . $request->kg),
                    'total_price' => DB::raw('total_price+' . ($request->kg * $lagoon->price_per_kilo))
                ]);
            } else {
                $transactionItem = LagoonPerKiloTransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'name' => $lagoon->name,
                    'kilos' => $request->kg,
                    'price_per_kilo' => $lagoon->price_per_kilo,
                    'total_price' => $request->kg * $lagoon->price_per_kilo,
                    'lagoon_per_kilo_id' => $lagoon->id,
                ]);
            }

            if($transaction) {
                $transaction->refreshAll();
            }

            $this->dispatch((new SendTransaction($transaction->id))->delay(5));

            MonitorChecker::hasQue($transaction->id);

            return response()->json([
                'transaction' => $transaction,
            ]);
        });
    }

    public function reduceScarpaCleaning(Request $request) {
        return DB::transaction(function () use ($request) {
            $scarpaCleaningTransactionItem = ScarpaCleaningTransactionItem::with('transaction')
                ->where([
                    'scarpa_variation_id' => $request->scarpaVariationId,
                    'transaction_id' => $request->transactionId,
                ])->orderByDesc('created_at')->first();

            if(!$scarpaCleaningTransactionItem->created_at->isToday()) {
                return response()->json([
                    'errors' => [
                        'message' => ['Cannot remove item. Transaction is from previous day']
                    ],
                ], 422);
            }

            if($scarpaCleaningTransactionItem->delete()) {
                $scarpaCleaningTransactionItem->transaction()->update([
                    'saved' => false,
                ]);

                $this->dispatch((new SendTransaction($scarpaCleaningTransactionItem->transaction_id))->delay(5));

                MonitorChecker::hasQue($scarpaCleaningTransactionItem->transaction_id);

                return response()->json([
                    'scarpaCleaningTransactionItem' => $scarpaCleaningTransactionItem
                ]);
            }


            return $scarpaCleaningTransactionItem;
        });
    }
    public function reduceLagoon(Request $request) {
        return DB::transaction(function () use ($request) {
            $lagonTransactionItem = LagoonTransactionItem::with('transaction')
                ->where([
                    'lagoon_id' => $request->lagoonId,
                    'transaction_id' => $request->transactionId,
                ])->orderByDesc('created_at')->first();

            if(!$lagonTransactionItem->created_at->isToday()) {
                return response()->json([
                    'errors' => [
                        'message' => ['Cannot remove item. Transaction is from previous day']
                    ],
                ], 422);
            }

            if($lagonTransactionItem->delete()) {
                $lagonTransactionItem->transaction()->update([
                    'saved' => false,
                ]);

                $this->dispatch((new SendTransaction($lagonTransactionItem->transaction_id))->delay(5));

                return response()->json([
                    'lagoonTransactionItem' => $lagonTransactionItem
                ]);
            }

            MonitorChecker::hasQue($lagonTransactionItem->transaction_id);

            return $lagonTransactionItem;
        });
    }

    public function reduceLagoonPerKilo(Request $request) {
        return DB::transaction(function () use ($request) {
            $lagonTransactionItem = LagoonPerKiloTransactionItem::with('transaction')->findOrFail($request->lagoonId);

            if(!$lagonTransactionItem->created_at->isToday()) {
                return response()->json([
                    'errors' => [
                        'message' => ['Cannot remove item. Transaction is from previous day']
                    ],
                ], 422);
            }

            if($lagonTransactionItem->delete()) {
                $lagonTransactionItem->transaction()->update([
                    'saved' => false,
                ]);

                $this->dispatch((new SendTransaction($lagonTransactionItem->transaction_id))->delay(5));

                return response()->json([
                    'lagoonTransactionItem' => $lagonTransactionItem
                ]);
            }

            MonitorChecker::hasQue($lagonTransactionItem->transaction_id);

            return $lagonTransactionItem;
        });
    }

    public function reduceProducts(Request $request) {
        return DB::transaction(function () use ($request) {
            $productItem = ProductTransactionItem::where([
                'product_id' => $request->productId,
                'transaction_id' => $request->transactionId,
            ])->orderByDesc('created_at')->first();

            if(!$productItem->created_at->isToday()) {
                return response()->json([
                    'errors' => [
                        'message' => ['Cannot remove item. Transaction iS from previous day']
                    ],
                ], 422);
            }

            if($productItem->delete()) {
                $product = Product::withTrashed()->find($request->productId);
                if($product) {
                    $product->increment('current_stock');
                }
            }

            $productItem->transaction()->update([
                'saved' => false,
            ]);

            $this->dispatch((new SendTransaction($product->transaction_id))->delay(5));
            $this->dispatch($product->queSynch());

            MonitorChecker::hasQue($product->transaction_id);

            return response()->json([
                'productItem' => $productItem
            ]);
        });
    }

    public function saveTransaction($transactionId) {

        return DB::transaction(function () use ($transactionId) {
            $earningPoints = 0;

            $transaction = Transaction::with('customer')->findOrFail($transactionId);
            if(!$transaction->job_order) {
                $transaction->attachJobOrder();
                $transaction->date = Carbon::now();
            }
            $transaction->update([
                'saved' => true,
                'total_price' => $transaction->totalPrice(),
            ]);

            // $transaction->saved = Carbon::now();
            // $transaction->save();

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

            $otherServices = ServiceTransactionItem::with('fullService.fullServiceItems')->where([
                'category' => 'other',
                'transaction_id' => $transactionId,
                'saved' => false,
            ])->update([
                'saved' => true,
            ]);

            $products = $transaction->productTransactionItems()->update([
                'saved' => true,
            ]);

            $scarpaCleanings = $transaction->scarpaCleaningTransactionItems()->update([
                'saved' => true,
            ]);

            $lagoon = $transaction->lagoonTransactionItems()->update([
                'saved' => true,
            ]);

            $lagoon = $transaction->lagoonPerKiloTransactionItems()->update([
                'saved' => true,
            ]);

            foreach ($washingServices as $item) {
                CustomerWash::create([
                    'service_name' => $item->name,
                    'customer_id' => $transaction->customer_id,
                    'service_transaction_item_id' => $item['id'],
                    'machine_type' => $item->washingService['machine_type'],
                    // 'minutes' => $item->washingService['regular_minutes'] + $item->washingService['additional_minutes'],
                    'minutes' => $item->washingService['minutes'],
                    // 'pulse_count' => $item->washingService['additional_minutes'] ? 2 : 1,
                    'pulse_count' => $item->washingService['pulse_count'],
                    'price' => $item->washingService->price,
                    'job_order' => $transaction->job_order,
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
                    'price' => $item->dryingService->price,
                    'job_order' => $transaction->job_order,
                ]);

                $item->update([
                    'saved' => true,
                    'earning_points' => $item->dryingService['points'],
                ]);
            }

            // foreach ($fullServices as $item) {
            //     foreach ($item->fullService->fullServiceItems as $fullServiceItem) {
            //         $earningPoints = 0;
            //         if($fullServiceItem->category == 'drying') {

            //             $dryingService = DryingService::find($fullServiceItem->drying_service_id);

            //             for ($i=0; $i < $fullServiceItem->quantity; $i++) {

            //                 CustomerDry::create([
            //                     'job_order' => $transaction->job_order,
            //                     'service_name' => $dryingService->name . ' (Full service)',
            //                     'customer_id' => $transaction->customer_id,
            //                     'service_transaction_item_id' => $item['id'],
            //                     'machine_type' => $dryingService['machine_type'],
            //                     'minutes' => $dryingService['minutes'],
            //                     'pulse_count' => $dryingService['minutes'] / 10,
            //                 ]);
            //                 $earningPoints += $fullServiceItem->points;
            //             }

            //         }

            //         if($fullServiceItem->category == 'washing') {

            //             $washingService = WashingService::find($fullServiceItem->washing_service_id);

            //             for ($i=0; $i < $fullServiceItem->quantity; $i++) {

            //                 CustomerWash::create([
            //                     'job_order' => $transaction->job_order,
            //                     'service_name' => $washingService->name . '(Full service)',
            //                     'customer_id' => $transaction->customer_id,
            //                     'service_transaction_item_id' => $item['id'],
            //                     'machine_type' => $washingService['machine_type'],
            //                     'minutes' => $washingService['regular_minutes'] + $washingService['additional_minutes'],
            //                     'pulse_count' => $washingService['additional_minutes'] ? 2 : 1,
            //                 ]);
            //                 $earningPoints += $fullServiceItem->points;
            //             }
            //         }
            //     }

            //     $item->update([
            //         'saved' => true,
            //         'earning_points' => $earningPoints,
            //     ]);
            // }

            if($transaction) {
                $transaction->refreshAll();
            }

            $this->dispatch((new SendTransaction($transaction->id))->delay(5));

            // $monitorChecker = MonitorChecker::firstOrCreate(['id' => 'default']);
            // $monitorChecker->update([
            //     'transaction_id' => $transaction->id,
            //     'token' => $transaction->id,
            //     'action' => 'save',
            // ]);

            return response()->json([
                'transaction' => $transaction,
            ]);
        });
    }

    public function cancelTransaction($transactionId) {
        MonitorChecker::idle();
        $transaction = Transaction::find($transactionId);
        if($transaction != null) {
            $transaction->forceDelete();
        }
    }

    public function voidTransaction($transactionId, Request $request) {
        MonitorChecker::idle();
        return  DB::transaction(function () use ($transactionId, $request) {
            $transaction = Transaction::find($transactionId);
            if($transaction != null) {
                $transaction->update([
                    'cancelation_remarks' => $request->remarks . "(Canceled by: ".auth('api')->user()->name.")",
                ]);

                foreach ($transaction->serviceTransactionItems as $value) {
                    if($value->category == 'full') {
                        $productItems = $value->fullService->fullServiceProducts;
                        foreach($productItems as $productItem) {
                            $product = Product::find($productItem->product_id);
                            if($product) {
                                $product->increment('current_stock', $productItem->quantity);
                            }
                        }
                    }
                    $value->delete();
                }
    
                foreach ($transaction->productTransactionItems as $value) {
                    $value->product()->increment('current_stock');
                    $value->delete();
                    $this->dispatch($value->product->queSynch());
                }
    
                $this->dispatch((new SendTransaction($transaction->id))->delay(5));
            }
        });
        
    }

    public function clearMonitorView() {
        MonitorChecker::idle();
        // $monitorChecker = MonitorChecker::firstOrCreate(['id' => 'default']);
        // $monitorChecker->update([
        //     'transaction_id' => null,
        //     'token' => null,
        //     'action' => 'idle',
        // ]);
    }
}
