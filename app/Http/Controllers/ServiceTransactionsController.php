<?php

namespace App\Http\Controllers;

use App\CustomerDry;
use App\CustomerWash;
use App\ServiceTransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServiceTransactionsController extends Controller
{
    public function deleteItem($serviceTransactionItemId) {

        return DB::transaction(function () use ($serviceTransactionItemId) {
            $serviceTransactionItem = ServiceTransactionItem::find($serviceTransactionItemId);

            if($serviceTransactionItem->forceDelete()) {

                $customerDries = CustomerDry::where('service_transaction_item_id', $serviceTransactionItemId)->get();
                $customerWashes = CustomerWash::where('service_transaction_item_id', $serviceTransactionItemId)->get();

                foreach ($customerDries as $customerDry) {
                    if($customerDry->used != null) {
                        DB::rollback();
                        return response()->json([
                            'errors' => [
                                'message' => ['Cannot remove item. "' . $customerDry->service_name . '" is already used']
                            ]
                        ], 422);
                    } else {
                        $customerDry->forceDelete();
                    }
                }

                foreach ($customerWashes as $customerWash) {
                    if($customerWash->used != null) {
                        DB::rollback();
                        return response()->json([
                            'errors' => [
                                'message' => ['Cannot remove item. "' . $customerDry->service_name . '" is already used']
                            ]
                        ], 422);
                    } else {
                        $customerWash->forceDelete();
                    }
                }


                $serviceTransactionItem->transaction()->update([
                    'saved' => null,
                ]);


                return response()->json([
                    'message' => 'Item deleted successfully',
                    'serviceTransactionItem' => $serviceTransactionItem,
                ]);
            }
        });
    }
}
