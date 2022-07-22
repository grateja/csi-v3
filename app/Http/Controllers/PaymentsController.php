<?php

namespace App\Http\Controllers;

use App\Customer;
use App\Discount;
use App\Jobs\SendTransaction;
use App\LoyaltyPoint;
use App\PartialPayment;
use App\RfidCard;
use App\Transaction;
use App\TransactionPayment;
use App\MonitorChecker;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentsController extends Controller
{
    public function partialPayment($transactionId, Request $request) {
        $rules = [
            'cash' => 'required|numeric',
            'rfidCardLoad' => 'numeric|nullable',
            'pointsInPeso' => 'numeric|nullable',
        ];

        if($request->validate($rules)) {
            return DB::transaction(function () use ($transactionId, $request) {
                $discountInPeso = 0;
                $pointsToDeduct = 0;
                // $percentageDiscount = 0;
                $rfid = null;
                $transaction = Transaction::findOrFail($transactionId);
                $customer = Customer::findOrFail($transaction->customer_id);
                $transaction->refreshAll();

                if($request->cashlessAmount) {
                    DB::rollback();
                    return response()->json([
                        'errors' => [
                            'message' => ['Cash less cannot be used for partial payment']
                        ]
                    ], 422);
                }

                if($request->pointsInPeso) {
                    DB::rollback();
                    return response()->json([
                        'errors' => [
                            'message' => ['Loyalty points cannot be used for partial payment']
                        ]
                    ], 422);
                    // $loyaltyPoints = LoyaltyPoint::first();
                    // if($loyaltyPoints == null) {
                    //     return response()->json([
                    //         'errors' => [
                    //             'message' => ['Loyalty points not set up yet']
                    //         ]
                    //     ], 422);
                    // } else {
                    //     // totalPoints * amountInPeso / pointsInPeso
                    //     $pointsToDeduct =  $request->pointsInPeso / $loyaltyPoints->amount_in_peso;
                    // }
                }

                if($request->discountId) {
                    // $discount = Discount::findOrFail($request->discountId);
                    // $discountInPeso = $transaction->total_price * $discount->percentage / 100;
                    // $percentageDiscount = $discount->percentage;
                    return response()->json([
                        'errors' => [
                            'message' => ['Discounts cannot be used for partial payment']
                        ]
                    ], 422);
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

                // $earningPoints = $transaction->serviceTransactionItems()->sum('earning_points');
                // $customer->increment('earned_points', $earningPoints);
                $customer->decrement('earned_points', $pointsToDeduct);

                $totalCash = $request->cash + $discountInPeso + $request->pointsInPeso + $request->rfidCardLoad;
                //$change = $totalCash - $transaction->total_price;
                $balance = $transaction->total_price - $totalCash;
                $partialPayment = $transaction->partialPayment;

                // if($partialPayment != null) {
                //     $amountToPay = $partialPayment->balance;
                // } else {
                //     $amountToPay = $transaction->total_price;
                // }

                if($balance > 0 && $partialPayment != null) {
                    DB::rollback();
                    return response()->json([
                        'errors' => [
                            'message' => ['Cannot make additional partial payment']
                        ]
                    ], 422);
                } else {
                    $payment = PartialPayment::create([
                        'transaction_id' => $transaction->id,
                        'customer_id' => $transaction->customer_id,
                        'date' => Carbon::now(),
                        'cash' => $request->cash,
                        'points' => $pointsToDeduct,
                        'points_in_peso' => $request->pointsInPeso,
                        // 'discount' => $percentageDiscount,
                        'total_amount' => $transaction->total_price,
                        'total_cash' => $totalCash,
                        'user_id' => auth('api')->id(),
                        'paid_to' => auth('api')->user()->name,
                        'balance' => $balance,
                        'card_load_used' => $request->rfidCardLoad,
                        'rfid' => $rfid,
                    ]);

                    $this->dispatch((new SendTransaction($transaction->id))->delay(5));
                    // $monitorChecker = MonitorChecker::firstOrCreate(['id' => 'default']);
                    // $monitorChecker->update([
                    //     'transaction_id' => null,
                    //     'token' => null,
                    //     'action' => 'paid',
                    // ]);    
    
                    return response()->json([
                        'transaction' => $transaction,
                        'payment' => $payment,
                        // 'cash' => $request->cash,
                    ]);
                }
            });
        }
    }

    public function fullPayment($transactionId, Request $request) {
        $rules = [
            'cash' => 'required|numeric',
            'rfidCardLoad' => 'numeric|nullable',
            'pointsInPeso' => 'numeric|nullable',
        ];

        if($request->validate($rules)) {

            return DB::transaction(function () use ($transactionId, $request) {
                $discountInPeso = 0;
                $discountName = '';
                $pointsToDeduct = 0;
                $percentageDiscount = 0;
                $cashlessAmount = 0;
                $rfid = null;
                $transaction = Transaction::findOrFail($transactionId);
                $customer = Customer::findOrFail($transaction->customer_id);
                $transaction->refreshAll();

                if($request->cashlessAmount) {
                    $cashlessAmount = $request->cashlessAmount;
                    if(!is_numeric($cashlessAmount)) {
                        return response()->json([
                            'errors' => [
                                'message' => ['Not a valid number']
                            ]
                        ], 422);
                    }

                    if($cashlessAmount > $transaction->total_price) {
                        return response()->json([
                            'errors' => [
                                'message' => ['Cashless payment cannot be higher than total price of the job order']
                            ]
                        ], 422);
                    }
                }

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
                    $discountName = $discount->name;
                    $discountInPeso = $transaction->total_price * $discount->percentage / 100;
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

                $partialPayment = $transaction->partialPayment ? $transaction->partialPayment->total_paid : 0;
                $totalCash = $request->cash + $discountInPeso + $request->pointsInPeso + $request->rfidCardLoad + $partialPayment + $cashlessAmount;
                $change = $totalCash - $transaction->total_price;
                $balance = $transaction->total_price - $totalCash;

                // if($partialPayment != null) {
                //     $amountToPay = $partialPayment->balance;
                // } else {
                //     $amountToPay = $transaction->total_price;
                // }

                if($balance > 0) {
                    DB::rollback();
                    return response()->json([
                        'errors' => [
                            'message' => ['Not enough cash']
                        ]
                    ], 422);
                } else {
                    $payment = TransactionPayment::create([
                        'id' => $transaction->id,
                        'customer_id' => $transaction->customer_id,
                        'date' => Carbon::now(),
                        'cash' => $request->cash,
                        'points' => $pointsToDeduct,
                        'points_in_peso' => $request->pointsInPeso,
                        'discount' => $percentageDiscount,
                        'discount_name' => $discountName,
                        'cash_less_provider' => $request->cashlessProvider,
                        'cash_less_amount' => $request->cashlessAmount,
                        'cash_less_reference_number' => $request->cashlessReferenceNumber,
                        'total_amount' => $transaction->total_price,
                        'total_cash' => $totalCash,
                        'user_id' => auth('api')->id(),
                        'paid_to' => auth('api')->user()->name,
                        'change' => $change,
                        'card_load_used' => $request->rfidCardLoad,
                        'rfid' => $rfid,
                    ]);
                    $transaction->update([
                        'date_paid' => Carbon::now(),
                    ]);
                }


                $this->dispatch((new SendTransaction($transaction->id))->delay(5));
                $monitorChecker = MonitorChecker::firstOrCreate(['id' => 'default']);
                $monitorChecker->update([
                    'transaction_id' => null,
                    'token' => null,
                    'action' => 'idle',
                ]);    

                return response()->json([
                    'transaction' => $transaction,
                    'payment' => $payment,
                    // 'cash' => $request->cash,
                ]);
            });
        }
    }
}
