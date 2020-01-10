<?php

namespace App\Http\Controllers;

use App\ServiceTransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServiceTransactionsController extends Controller
{
    public function deleteItem($serviceTransactionItemId) {

        return DB::transaction(function () use ($serviceTransactionItemId) {
            $serviceTransactionItem = ServiceTransactionItem::find($serviceTransactionItemId);

            if($serviceTransactionItem->delete()) {

                if($customerWash = $serviceTransactionItem->customerWash) {

                    if($customerWash->used) {
                        DB::rollback();
                        return response()->json([
                            'errors' => [
                                'message' => ['Cannot remove item. Service is already used']
                            ]
                        ], 422);
                    }

                    $customerWash->delete();
                }

                if($customerDry = $serviceTransactionItem->customerDry) {

                    if($customerDry->used) {
                        DB::rollback();
                        return response()->json([
                            'errors' => [
                                'message' => ['Cannot remove item. Service is already used']
                            ]
                        ], 422);
                    }

                    $customerDry->delete();
                }
                return response()->json([
                    'message' => 'Item deleted successfully',
                    'serviceTransactionItem' => $serviceTransactionItem,
                ]);
            }
        });
    }
}
