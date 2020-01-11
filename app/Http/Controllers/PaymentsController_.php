<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Transaction;
use App\CompletedServiceTransaction;
use Illuminate\Support\Facades\DB;
use App\CompletedProductTransaction;
use App\Customer;
use Carbon\Carbon;
use App\TransactionPayment;
use App\PaymentRemarks;
use App\RfidCard;
use App\RfidPosTransaction;
use App\Discount;
use App\LoyaltyPoint;

class PaymentsController extends Controller
{
    public function transactionPayment($transactionId) {
        $transaction = Transaction::with('serviceTransactions', 'productTransactions')->findOrFail($transactionId);
        $customer = Customer::findOrFail($transaction->customer_id);
        $pointsInPeso = 0;

        $loyaltyServices = CompletedServiceTransaction::with(['service' => function($query) {
            $query->select('id', 'name');
        }])->groupBy('service_id')
            ->selectRaw('SUM(quantity) as total, service_id')
            ->where([
                'customer_id' => $transaction->customer_id,
                'branch_id' => $transaction->branch_id,
                ['date_paid', '<>', null]
            ])->get();

        $totalServiceAmount = CompletedServiceTransaction::where([
            'customer_id' => $transaction->customer_id,
            'branch_id' => $transaction->branch_id,
            'date_paid' => null
        ])->sum('price_sum');

        $earningPoints = CompletedServiceTransaction::where([
            'customer_id' => $transaction->customer_id,
            'branch_id' => $transaction->branch_id,
            'date_paid' => null
        ])->sum('points');

        $totalProductAmount = CompletedProductTransaction::where([
            'customer_id' => $transaction->customer_id,
            'branch_id' => $transaction->branch_id,
            'date_paid' => null
        ])->sum('price_sum');

        $customerCards = RfidCard::where('customer_id', $transaction->customer_id)->get();

        $discounts = Discount::where('branch_id', auth('api')->user()->active_branch_id)->get();

        $loyaltyPoint = LoyaltyPoint::where('branch_id', auth('api')->user()->active_branch_id)->first();

        if($loyaltyPoint != null) {
            $pointsInPeso = round($loyaltyPoint->amount_in_peso * $customer->earned_points, 2);
        }


        return response()->json([
            'customerCards' => $customerCards,
            'loyalty' => $loyaltyServices,
            'currentPoints' => round($customer->earned_points, 2),
            'earningPoints' => round($earningPoints, 2),
            'transaction' => $transaction,
            'totalServiceAmount' => $totalServiceAmount,
            'totalProductAmount' => $totalProductAmount,
            'pointsInPeso' => $pointsInPeso,
            'discounts' => $discounts,
        ], 200);
    }

    public function loyaltyServices($customerId) {
        $customer = Customer::with([
            'completedPaidServices' => function($query) {
                $query->with(['service' => function($query) {
                    // $query->select('id', 'name');
                }])
                    ->groupBy('service_id')
                    ->selectRaw('SUM(quantity) as total, service_id')
                    ->where('branch_id', auth('api')->user()->active_branch_id);
            },
        ])->findOrFail($customerId);



        // $loyaltyServices = CompletedServiceTransaction::with(['service' => function($query) {
        //     $query->select('id', 'name');
        // }])->groupBy('service_id')
        //     ->selectRaw('SUM(quantity) as total, service_id')
        //     ->where([
        //         'customer_id' => $customerId,
        //         'branch_id' => $customer->branch_id,
        //         ['date_paid', '<>', null]
        //     ])->get();

        return response()->json($customer, 200);
    }

    public function proceedToPayment($transactionId, Request $request) {
        $rules = [
            'cash' => 'required',
        ];

        if($request->validate($rules)) {
            return DB::transaction(function () use ($request, $transactionId) {

                // get the actual transaction
                $transaction = Transaction::findOrFail($transactionId);

                // initialize vars
                $discountInPeso = 0;
                $pointsUsed = 0;
                $loadUsed = 0;

                $loyaltyPoint = LoyaltyPoint::where('branch_id', auth('api')->user()->active_branch_id)->first();
                if($loyaltyPoint != null && (float)$request->pointsInPeso > 0) {
                    $pointsUsed = $request->pointsInPeso / $loyaltyPoint->amount_in_peso;
                }

                // get transaction services
                $completedServices =  CompletedServiceTransaction::where([
                    'customer_id' => $transaction->customer_id,
                    'branch_id' => $transaction->branch_id,
                    'date_paid' => null
                ]);

                // get transaction products
                $completedProducts = CompletedProductTransaction::where([
                    'customer_id' => $transaction->customer_id,
                    'branch_id' => $transaction->branch_id,
                    'date_paid' => null
                ]);

                // compute the total amount from services and products
                $totalServiceAmount = $completedServices->sum('price_sum');
                $totalProductAmount = $completedProducts->sum('price_sum');

                // get the grand total
                $totalAmount = $totalProductAmount + $totalServiceAmount;

                // check if using a discount
                // discount in percent
                if($request->discount > 0) {
                    $discountInPeso = $totalAmount * $request->discount / 100;
                }

                // points will earn after successful transaction
                $earningPoints = $completedServices->sum('points');

                // get customer cards used
                $loadUsed = collect($request->customerCards)->sum('use');

                // get the total cash
                $totalCash = $request->cash + $discountInPeso + $request->pointsInPeso + $loadUsed;

                // balance
                $balance = $totalAmount - $totalCash;

                if($balance > 0) {
                    return response()->json([
                        'errors' => [
                            'balance' => ['Cannot have remaining balance.']
                        ],
                    ], 422);
                }

                if($request->customerCards && $loadUsed > 0) {
                    // deduct customer cards
                    foreach ($request->customerCards as $rawRfidCard) {
                        $rfidCard = RfidCard::findOrFail($rawRfidCard['id']);

                        // validate rfid
                        // check if used load is greater than balance
                        if($rfidCard->balance < $rawRfidCard['use']) {
                            // rollback entire trnasaction
                            DB::rollback();
                            return response()->json([
                                'errors' => [
                                    $rawRfidCard['rfid'] => ['Not enough balance.']
                                ],
                            ], 422);
                        }

                        $rfidCard->update([
                            'balance' => DB::raw('balance-' . $rawRfidCard['use'])
                        ]);

                        // record transaction for each cards
                        RfidPosTransaction::create([
                            'rfid_card_id' => $rfidCard->id,
                            'remarks' => $transaction->job_order,
                            'amount_deducted' => $rawRfidCard['use'],
                            'user_id' => auth('api')->id(),
                            'transaction_id' => $transactionId,
                        ]);
                    }
                }

                // update the actual transaction
                $transaction->update([
                    'paid_to' => auth('api')->id(),
                    'date_paid' => Carbon::now(),
                ]);

                $transactionPayment = TransactionPayment::create([
                    'transaction_id' => $transactionId,
                    'customer_id' => $request->customerId,
                    'job_order' => $transaction->job_order,
                    'cash' => $request->cash,
                    'points' => $pointsUsed,
                    'points_in_peso' => $request->pointsInPeso,
                    'discount' => $request->discount,
                    'total_amount' => $totalAmount,
                    // 'change' => round($request->cash - $totalAmount + $discountInPeso + $request->pointsInPeso, 2),
                    'change' => $totalCash - $totalAmount,
                    'balance' => $balance < 0 ? 0 : $balance,
                    'total_cash' => $totalCash,
                    'user_id' => auth('api')->id(),
                ]);

                $customer->update([
                    'earned_points' => DB::raw('earned_points+' . $earningPoints . '-' . $pointsUsed),
                ]);

                if($request->remarks) {
                    PaymentRemarks::create([
                        'transaction_payment_id' => $transactionPayment->transaction_id,
                        'user_id' => auth('api')->id(),
                        'remarks' => $request->remarks,
                    ]);
                }

                $completedProducts->update([
                    'date_paid' => Carbon::now(),
                ]);

                $completedServices->update([
                    'date_paid' => Carbon::now(),
                ]);

                return response()->json([
                    'transactionId' => $transaction->id,
                    'totalAmount' => $totalAmount,
                    'transactionPayment' => $transactionPayment,
                ], 200);
            });
        }
    }
}
