<?php

namespace App\Http\Controllers;

use App\CustomerDry;
use App\CustomerWash;
use App\Jobs\SendTransaction;
use App\ServiceTransactionItem;
use App\TransactionRemarks;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Product;

class ServiceTransactionsController extends Controller
{
    public function deleteItem($serviceTransactionItemId) {

        return DB::transaction(function () use ($serviceTransactionItemId) {
            $serviceTransactionItem = ServiceTransactionItem::with('customerDry', 'customerWash')->find($serviceTransactionItemId);

            $usedDry = $serviceTransactionItem->customerDry()->whereNotNull('used')->exists();
            $usedWash = $serviceTransactionItem->customerWash()->wherenotNull('used')->exists();

            if($usedDry || $usedWash) {
                return response()->json([
                    'errors' => [
                        'message' => ['Cannot remove item. Service is already used']
                    ]
                ], 422);
            } else if(!$serviceTransactionItem->created_at->isToday()) {
                return response()->json([
                    'errors' => [
                        'message' => ['Cannot remove item. Transaction is from previous day']
                    ]
                ], 422);
            }

            // $customerDries = $serviceTransactionItem->customerDry(); //CustomerDry::where('service_transaction_item_id', $serviceTransactionItemId)->get();
            // $customerWashes = $serviceTransactionItem->customerWash(); //CustomerWash::where('service_transaction_item_id', $serviceTransactionItemId)->get();
            // foreach ($customerDries as $customerDry) {
            //     if($customerDry['used'] != null) {
            //         DB::rollback();
            //         return response()->json([
            //             'errors' => [
            //                 'message' => ['Cannot remove item. "' . $customerDry->service_name . '" is already used']
            //             ]
            //         ], 422);
            //         break;
            //     } else {
            //         $customerDry->forceDelete();
            //     }
            // }

            // foreach ($customerWashes as $customerWash) {
            //     if($customerWash->used != null) {
            //         DB::rollback();
            //         return response()->json([
            //             'errors' => [
            //                 'message' => ['Cannot remove item. "' . $customerDry->service_name . '" is already used']
            //             ]
            //         ], 422);
            //     } else {
            //         $customerWash->forceDelete();
            //     }
            // }

            if($serviceTransactionItem->delete()) {
                $serviceTransactionItem->transaction()->update([
                    'saved' => false,
                ]);

                $serviceTransactionItem->customerDry()->delete();
                $serviceTransactionItem->customerWash()->delete();

                if($serviceTransactionItem->category == 'full') {
                    $productItems = $serviceTransactionItem->fullService->fullServiceProducts;
                    foreach($productItems as $productItem) {
                        $product = Product::find($productItem->product_id);
                        if($product) {
                            $product->increment('current_stock', $productItem->quantity);
                        }
                    }
                }

                TransactionRemarks::create([
                    'transaction_id' => $serviceTransactionItem->transaction_id,
                    'remarks' => 'Item removed(' . $serviceTransactionItem->name . ')',
                    'added_by' => auth('api')->user()->name,
                ]);

                $this->dispatch((new SendTransaction($serviceTransactionItem->transaction_id))->delay(5));

                return response()->json([
                    'message' => 'Item deleted successfully',
                    'serviceTransactionItem' => $serviceTransactionItem,
                ]);
            }
        });
    }
}
