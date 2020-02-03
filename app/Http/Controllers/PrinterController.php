<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Transaction;
use Illuminate\Support\Carbon;
use App\CompletedServiceTransaction;
use Illuminate\Support\Facades\DB;
use App\CompletedProductTransaction;

class PrinterController extends Controller
{
    public function printReceipt($transactionId) {
        $transaction = Transaction::with(['customer' => function($query) {
            $query->select('id', 'name');
        }, 'branch' => function($query) {
            $query->select('id', 'name', 'contact_no', 'email', 'address');
        }, 'user' => function($query) {
            $query->select('id', 'firstname', 'lastname');
        }, 'payment' => function($query) {
            $query->with('user')->select('transaction_id', 'date', 'cash', 'points', 'points_in_peso', 'discount', 'total_amount', 'balance', 'change', 'total_cash', 'user_id');
        }, 'completedServiceTransactions.service', 'completedProductTransactions', 'rfidPosTransactions.rfidCard'])
            ->findOrFail($transactionId);

        $services = CompletedServiceTransaction::with('branchService.service')->select('branch_service_id', DB::raw('SUM(quantity) AS totalQuantity, SUM(price_sum) as totalPrice'))->where('transaction_id', $transactionId)
            ->groupBy(DB::raw('branch_service_id'))->get();

        $products = CompletedProductTransaction::with('branchProduct.product')->select('branch_product_id', DB::raw('SUM(quantity) AS totalQuantity, SUM(price_sum) as totalPrice'))->where('transaction_id', $transactionId)
            ->groupBy(DB::raw('branch_product_id'))->get();


        $discountInPeso = number_format($transaction->payment->discount * $transaction->payment->total_amount / 100, 2);

        $earningPoints = CompletedServiceTransaction::where([
            'transaction_id' => $transactionId,
        ])->sum('points');


        $data = [
            'date' => Carbon::createFromDate($transaction->date)->format('M-d, Y H:i A'),
            'jobOrder' => $transaction->job_order,
            'customerName' => $transaction->customer['name'],
            'branch' => $transaction->branch,
            'payment' => $transaction->payment,
            'services' => collect($services)->transform(function($item) {
                return [
                    'name' => $item->branchService->service['name'],
                    'quantity' => $item->totalQuantity,
                    'price' => number_format($item->totalPrice, 2),
                ];
            }),
            'serviceSummary' => [
                'totalQuantity' => collect($services)->sum('totalQuantity'),
                'totalPrice' => number_format(collect($services)->sum('totalPrice'), 2),
            ],
            'products' => collect($products)->transform(function($item) {
                return [
                    'name' => $item->branchProduct->product['name'],
                    'quantity' => $item->totalQuantity,
                    'price' => number_format($item->totalPrice, 2),
                ];
            }),
            'productSummary' => [
                'totalQuantity' => collect($products)->sum('totalQuantity'),
                'totalPrice' => number_format(collect($products)->sum('totalPrice'), 2),
            ],
            'discountInPeso' => $discountInPeso,
            'amountDue' => number_format($transaction->payment->total_amount - $discountInPeso - $transaction->payment->points_in_peso, 2),
            'hasPoints' => $transaction->payment->points,
            'points' => "P " . number_format($transaction->payment->points_in_peso, 2) . " (" . $transaction->payment->points . " Point(s))",
            'cardsUsed' => $transaction->rfidPosTransactions,
            'earningPoints' => $earningPoints,
        ];

         return view('printer.receipt', $data);
    }


    public function claimStub($transactionId) {
        $transaction = Transaction::with('payment.user', 'customer', 'serviceTransactionItems', 'productTransactionItems')->findOrFail($transactionId);

        if(!$transaction->saved) {
            return response()->json([
                'errors' => [
                    'message' => ['Cannot print claim stub. Transaction was modified']
                ]
            ]);
        }

        $data = [
            'job_order' => $transaction->job_order,
            'date' => Carbon::createFromDate($transaction->date)->format('M-d, Y h:i A'),
            'customer_name' => $transaction->customer->name,
            'status' => $transaction->payment == null ? 'Not Paid' : 'Paid to: ' . $transaction->payment->user->name,
            'total_amount' => $transaction->total_price,
        ];

        return view('printer.claim-stub', $data);
    }
}
