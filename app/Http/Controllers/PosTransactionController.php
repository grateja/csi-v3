<?php

namespace App\Http\Controllers;

use App\CustomerDry;
use App\CustomerWash;
use App\DryingService;
use App\FullService;
use App\OtherService;
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
            $transaction['posServiceItems'] = $transaction->posServiceItems();
            $transaction['posServiceSummary'] = $transaction->posServiceSummary();
        }


        return response()->json([
            'transaction' => $transaction
        ]);
    }

    public function services() {
        $washingServices = WashingService::orderBy('name')->get();
        $dryingServices = DryingService::orderBy('name')->get();
        $otherServices = OtherService::orderBy('name')->get();
        $fullServices = FullService::with('fullServiceItems')->orderBy('name')->get();

        return response()->json([
            'washingServices' => $washingServices,
            'dryingServices' => $dryingServices,
            'otherServices' => $otherServices,
            'fullServices' => $fullServices,
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

            $transaction['posServiceItems'] = $transaction->posServiceItems();
            $transaction['posServiceSummary'] = $transaction->posServiceSummary();

            return response()->json([
                'transaction' => $transaction,
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
                ]);
            }

            $transaction['posServiceItems'] = $transaction->posServiceItems();
            $transaction['posServiceSummary'] = $transaction->posServiceSummary();

            return response()->json([
                'transaction' => $transaction,
            ]);
        });
    }
}
