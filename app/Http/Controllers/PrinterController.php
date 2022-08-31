<?php

namespace App\Http\Controllers;

use App\Client;
use Illuminate\Http\Request;
use App\Transaction;
use Illuminate\Support\Carbon;
use App\CompletedServiceTransaction;
use Illuminate\Support\Facades\DB;
use App\CompletedProductTransaction;
use App\RfidLoadTransaction;
use Illuminate\Support\Facades\File;
use Mike42\Escpos\PrintConnectors\CupsPrintConnector;
use Mike42\Escpos\Printer;
use App\ThermalPrinter;

class PrinterController extends Controller
{
    public function test(Request $request) {
        if($request->text) {
            $thermalPrinter = new ThermalPrinter;
            if($printerError = $thermalPrinter->hasError()) {
                return response()->json([
                    'errors' => $printerError
                ], 422);
            }
            $thermalPrinter->test($request->text);
        } else {
            return response()->json([
                'errors' => [
                    'message' => ['Empty text']
                ]
            ], 422);
        }
    }

    public function qrCode(Request $request) {
        if($request->text) {
            $thermalPrinter = new ThermalPrinter;
            if($printerError = $thermalPrinter->hasError()) {
                return response()->json([
                    'errors' => $printerError
                ], 422);
            }
            $thermalPrinter->qr($request->text);
        } else {
            return response()->json([
                'errors' => [
                    'message' => ['Empty text']
                ]
            ], 422);
        }
    }

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


    public function claimStub($transactionId, Request $request) {
        $transaction = Transaction::with('partialPayment.user', 'payment.user', 'customer', 'serviceTransactionItems', 'productTransactionItems')->findOrFail($transactionId);
        $transaction->refreshAll();
        // $transaction->withPayment();
        $thermalPrinter = new ThermalPrinter;
        if($printerError = $thermalPrinter->hasError()) {
            if(env('PRINTER_METHOD', 'rpi') == 'rpi') {
                return response()->json([
                    'errors' => $printerError,
                    'method' => 'rpi',
                ], 422);
            }
        } else {
            $thermalPrinter->claimStub($transaction, $request);
            return response()->json([
                'success' => 'Claim stub printed successfully',
                'method' => 'rpi'
            ]);
        }
        $client = Client::firstOrFail();

        if(!$transaction->saved) {
            return response()->json([
                'errors' => [
                    'message' => ['Cannot print claim stub. Transaction was modified']
                ]
            ], 422);
        }

        $data = [
            'shop_name' => $client->shop_name,
            'shop_address' => $client->address,
            'shop_email' => $client->shop_email,
            'shop_number' => $client->shop_number,
            'job_order' => $transaction->job_order,
            'date' => Carbon::createFromDate($transaction->date)->format('M-d, Y h:i A'),
            'customer_name' => $transaction->customer->name,
            'status' => $transaction->payment == null ? 'Not Paid' : 'Paid to: ' . $transaction->payment->user->name,
            'total_amount' => $transaction->total_price,
        ];

        return view('printer.claim-stub', $data);
    }

    public function jobOrder($transactionId, Request $request) {
        $client = Client::firstOrFail();

        $transaction = Transaction::with('user', 'payment.user', 'customer', 'serviceTransactionItems', 'productTransactionItems')->findOrFail($transactionId);


        if($transaction->date_paid == null) {
            return response()->json([
                'errors' => [
                    'message' => ['Cannot print unpaid job order']
                ]
            ], 422);
        }

        $transaction->refreshAll();
        $thermalPrinter = new ThermalPrinter;
        if($printerError = $thermalPrinter->hasError()) {
            if(env('PRINTER_METHOD', 'rpi') == 'rpi') {
                return response()->json([
                    'errors' => $printerError,
                    'method' => 'rpi',
                ], 422);
            }
        } else {
            $thermalPrinter->jobOrder($transaction, $request);
            return response()->json([
                'success' => 'Job Order Printed successfully',
                'method' => 'rpi',
            ]);
        }

        $data = [
            'shop_name' => $client->shop_name,
            'shop_address' => $client->address,
            'shop_email' => $client->shop_email,
            'shop_number' => $client->shop_number,
            'job_order' => $transaction->job_order,
            'date' => Carbon::createFromDate($transaction->date)->format('M-d, Y h:i A'),
            'customer_name' => $transaction->customer->name,
            'total_amount' => $transaction->total_price,
            'posServiceSummary' => $transaction->posServiceSummary(),
            'posServiceItems' => $transaction->posServiceItems(),
            'posProductItems' => $transaction->posProductItems(),
            'posProductSummary' => $transaction->posProductSummary(),
            'staff_name' => $transaction->staff_name,
            'date_paid' => Carbon::createFromDate($transaction->date_paid)->format('M-d, Y h:i A'),
            'paid_to' => $transaction->payment->paid_to,
            'cash' => $transaction->payment->cash,
            'change' => $transaction->payment->change,
            'points' => $transaction->payment->points,
            'points_in_peso' => $transaction->payment->points_in_peso,
            'discount' => $transaction->payment->discount,
            'discount_in_peso' => $transaction->payment->discount_in_peso,
            'rfid' => $transaction->payment->rfid,
            'card_load_used' => $transaction->payment->card_load_used,
        ];

        return view('printer.job-order', $data);
    }

    public function printShopPreferences() {
        $client = Client::firstOrFail();
        
    }

    public function loadTransaction($transactionId) {
        $client = Client::firstOrFail();
        $rfidLoadTransaction = RfidLoadTransaction::findOrFail($transactionId);

        $thermalPrinter = new ThermalPrinter;
        if($printerError = $thermalPrinter->hasError()) {
            if(env('PRINTER_METHOD', 'rpi') == 'rpi') {
                return response()->json([
                    'errors' => $printerError,
                    'method' => 'rpi',
                ], 422);
            }
        } else {
            $thermalPrinter->loadTransaction($rfidLoadTransaction);
            return response()->json([
                'success' => 'RFID Tap up Printed successfully'
            ]);
        }

        $data = [
            'shop_name' => $client->shop_name,
            'shop_address' => $client->address,
            'shop_email' => $client->shop_email,
            'shop_number' => $client->shop_number,
            'created_at' => $rfidLoadTransaction->created_at,
            'customer_name' => $rfidLoadTransaction->customer_name,
            'rfid' => $rfidLoadTransaction->rfid,
            'amount' => $rfidLoadTransaction->amount,
            'remaining_balance' => $rfidLoadTransaction->remaining_balance,
            'current_balance' => $rfidLoadTransaction->current_balance,
            'cash' => $rfidLoadTransaction->cash,
            'change' => $rfidLoadTransaction->change,
            'staff_name' => $rfidLoadTransaction->staff_name,
            'remarks' => $rfidLoadTransaction->remarks,
        ];

        return view('printer.rfid-load-transaction', $data);
    }
}
