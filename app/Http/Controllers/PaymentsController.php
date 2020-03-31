<?php

namespace App\Http\Controllers;

use App\Customer;
use App\Discount;
use App\Jobs\SendTransaction;
use App\LoyaltyPoint;
use App\RfidCard;
use App\Transaction;
use App\TransactionPayment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentsController extends Controller
{
    public function proceedToPayment($transactionId, Request $request) {
        $rules = [
            'cash' => 'required|numeric',
            'rfidCardLoad' => 'numeric|nullable',
            'pointsInPeso' => 'numeric|nullable',
        ];

        if($request->validate($rules)) {

            return DB::transaction(function () use ($transactionId, $request) {
                $discountInPeso = 0;
                $pointsToDeduct = 0;
                $percentageDiscount = 0;
                $rfid = null;
                $transaction = Transaction::findOrFail($transactionId);
                $customer = Customer::findOrFail($transaction->customer_id);
                $transaction->refreshAll();

                if($request->pointsInPeso) {
                    $loyaltyPoints = LoyaltyPoint::first();
                    if($loyaltyPoints == null) {
                        return response()->json([
                            'errors' => [
                                'message' => ['Loyalty points not set up yet']
                            ]
                        ], 422);
                    } else {
                        // totalPoints * amountInPeso / pointsInPeso
                        $pointsToDeduct =  $request->pointsInPeso / $loyaltyPoints->amount_in_peso;
                    }
                }

                if($request->discountId) {
                    $discount = Discount::findOrFail($request->discountId);
                    $discountInPeso = $transaction->total_amount * $discount->percentage / 100;
                    $percentageDiscount = $discount->percentage;
                }


                if($request->rfidCardId) {
                    $rfidCard = RfidCard::findOrFail($request->rfidCardId);
                    if($rfidCard->balance < $request->rfidCardLoad) {
                        DB::rollback();
                        return response()->json([
                            'errors' => [
                                'rfidCardId' => ['Card balance not enough']
                            ]
                        ], 422);
                    } else {
                        $rfid = $rfidCard->rfid;
                        $rfidCard->decrement('balance', $request->rfidCardLoad);
                    }
                }

                $earningPoints = $transaction->serviceTransactionItems()->sum('earning_points');
                $customer->increment('earned_points', $earningPoints);
                $customer->decrement('earned_points', $pointsToDeduct);

                $totalCash = $request->cash + $discountInPeso + $request->pointsInPeso + $request->rfidCardLoad;
                $change = $totalCash - $transaction->total_amount;

                if($change < 0) {
                    DB::rollback();
                    return response()->json([
                        'errors' => [
                            'cash' => ['Not enough cash']
                        ]
                    ], 422);
                }

                $payment = TransactionPayment::create([
                    'id' => $transaction->id,
                    'customer_id' => $transaction->customer_id,
                    'date' => Carbon::now(),
                    'cash' => $request->cash,
                    'points' => $pointsToDeduct,
                    'points_in_peso' => $request->pointsInPeso,
                    'discount' => $percentageDiscount,
                    'total_amount' => $transaction->total_amount,
                    'total_cash' => $totalCash,
                    'user_id' => auth('api')->id(),
                    'paid_to' => auth('api')->user()->name,
                    'change' => $totalCash - $transaction->total_amount,
                    'card_load_used' => $request->rfidCardLoad,
                    'rfid' => $rfid,
                ]);

                $transaction->update([
                    'date_paid' => Carbon::now(),
                ]);

                $this->dispatch((new SendTransaction($transaction->id))->delay(5));

                return response()->json([
                    'transaction' => $transaction,
                    'payment' => $payment,
                    'cash' => $request->cash,
                ]);
            });
        }
    }
}
