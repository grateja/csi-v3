<?php

namespace App\Http\Controllers;

use App\Customer;
use App\LagoonPartner;
use App\LagoonPerKiloTransactionItem;
use App\LagoonTransactionItem;
use App\ScarpaCleaningTransactionItem;
use App\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QRTransactionController extends Controller
{
    public function save(Request $request) {
        if(!$request->jo) {
            return response()->json([
                'errors' => [
                    'message' => ['Invalid Job Order Number']
                ]
            ], 422);
        } else {
            $jo = Transaction::where('job_order', $request->jo)->first();
            if($jo != null) {
                return response()->json([
                    'errors' => [
                        'message' => ['Job Order Number Conflict']
                    ]
                ], 422);
            }
        }
        if(!$request->cust) {
            return response()->json([
                'errors' => [
                    'message' => ['Customer Profile']
                ]
            ], 422);
        }
        if(!$request->pid) {
            return response()->json([
                'errors' => [
                    'message' => ['Invalid Partner ID']
                ]
            ], 422);
        }
        return DB::transaction(function () use ($request) {
            $partner = LagoonPartner::find($request->pid);
            if($partner == null) {
                return response()->json([
                    'errors' => [
                        'message' => ['Lagoon partner ID not recognized or Unregistered!']
                    ]
                ], 422);
            }
            $customer = Customer::where('crn', $request->cust['crn'])->first();
            if($customer == null) {
                $customer = Customer::create([
                    'name' => $request->cust['nam'],
                    'address' => $request->cust['adr'],
                    'contact_number' => $request->cust['cn'],
                    'crn' => $request->cust['crn'],
                ]);
            } else {
                $customer->update([
                    'name' => $request->cust['nam'],
                    'address' => $request->cust['adr'],
                    'contact_number' => $request->cust['cn'],
                    'crn' => $request->cust['crn'],
                ]);
            }

            $transaction = Transaction::create([
                'job_order' => $request->jo,
                'customer_id' => $customer->id,
                'user_id' => auth('api')->id(),
                'staff_name' => auth('api')->user()->name,
                'customer_name' => $customer->name,
                'date' => Carbon::now(),
            ]);

            if($request->lag) {
                foreach($request->lag as $lagoonItem) {
                    $lagoonItem = (object)$lagoonItem;
                    for($i = 0; $i < $lagoonItem->qty; $i++) {
                        LagoonTransactionItem::create([
                            'transaction_id' => $transaction->id,
                            'name' => $lagoonItem->nam,
                            'price' => $lagoonItem->up,
                            // 'lagoon_id' => $lagoonItem->id,
                        ]);
                    }
                }
            }
            if($request->lpk) {
                foreach($request->lpk as $lagoonPerKiloItem) {
                    $lagoonPerKiloItem = (object) $lagoonPerKiloItem;
                    LagoonPerKiloTransactionItem::create([
                        'transaction_id' => $transaction->id,
                        'name' => $lagoonPerKiloItem->nam,
                        'kilos' => $lagoonPerKiloItem->qty,
                        'price_per_kilo' => $lagoonPerKiloItem->up,
                        'total_price' => $lagoonPerKiloItem->qty * $lagoonPerKiloItem->up,
                        // 'lagoon_per_kilo_id' => $lagoonPerKiloItem->id,
                    ]);
                }
            }
            if($request->sv) {
                foreach($request->sv as $svItem) {
                    $svItem = (object) $svItem;
                    for($i = 0; $i < $svItem->qty; $i++) {
                        ScarpaCleaningTransactionItem::create([
                            'transaction_id' => $transaction->id,
                            'name' => $svItem->nam,
                            'price' => $svItem->up,
                            // 'lagoon_id' => $svItem->id,
                        ]);
                    }
                }
            }

            $transaction->update([
                'saved' => true,
                'total_price' => $transaction->totalPrice(),
            ]);

            $partner->customers()->sync($customer->id);
            $partner->transactions()->sync($transaction->id);

            // DB::table('lagoon_partner_customers')->insert([
            //     'customer_id' => $customer->id,
            //     'lagoon_partner_id' => $request->pid,
            // ]);

            return response()->json([
                'success' => 'Job Order Saved',
            ]);
        });
    }
}
