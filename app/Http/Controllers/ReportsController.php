<?php

namespace App\Http\Controllers;

use App\Exports\ReportTemplate;
use App\RfidCardTransaction;
use App\RfidLoadTransaction;
use App\Transaction;
use App\TransactionPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ReportsController extends Controller
{
    public function excelPosTransactions(Request $request) {
        $serviceTransactions = DB::table('transactions')
            ->whereDate('transactions.date', $request->date)
            ->whereNull('transactions.deleted_at')
            ->whereNull('service_transaction_items.deleted_at')
            ->join('service_transaction_items', 'transactions.id', '=', 'service_transaction_items.transaction_id')
            ->selectRaw('job_order, customer_name, service_transaction_items.name as item_name, service_transaction_items.price as item_price, transactions.date as jo_date, date_paid');

        $result = DB::table('transactions')
            ->whereDate('transactions.date', $request->date)
            ->whereNull('transactions.deleted_at')
            ->whereNull('product_transaction_items.deleted_at')
            ->join('product_transaction_items', 'transactions.id', '=', 'product_transaction_items.transaction_id')
            ->selectRaw('job_order, customer_name, product_transaction_items.name as item_name, product_transaction_items.price as item_price, transactions.date as jo_date, date_paid')
            ->unionAll($serviceTransactions)
            ->orderByDesc('job_order')
            ->get();

        return Excel::download(new ReportTemplate($result, [
            'JOB ORDER',
            'CUSTOMER',
            'ITEM NAME',
            'ITEM PRICE',
            'DATE',
            'DATE PAID',
        ]), 'parts.xls');
    }

    public function excelPosCollections(Request $request) {
        $serviceTransactions = DB::table('transactions')
            ->whereDate('transactions.date_paid', $request->date)
            ->whereNull('transactions.deleted_at')
            ->whereNull('service_transaction_items.deleted_at')
            ->join('service_transaction_items', 'transactions.id', '=', 'service_transaction_items.transaction_id')
            ->selectRaw('job_order, customer_name, service_transaction_items.name as item_name, service_transaction_items.price as item_price, transactions.date as jo_date, date_paid');

        $result = DB::table('transactions')
            ->whereDate('transactions.date_paid', $request->date)
            ->whereNull('transactions.deleted_at')
            ->whereNull('product_transaction_items.deleted_at')
            ->join('product_transaction_items', 'transactions.id', '=', 'product_transaction_items.transaction_id')
            ->selectRaw('job_order, customer_name, product_transaction_items.name as item_name, product_transaction_items.price as item_price, transactions.date as jo_date, date_paid')
            ->unionAll($serviceTransactions)
            ->orderByDesc('job_order')
            ->get();

        return Excel::download(new ReportTemplate($result, [
            'JOB ORDER',
            'CUSTOMER',
            'ITEM NAME',
            'ITEM PRICE',
            'DATE',
            'DATE PAID',
        ]), 'parts.xls');
    }

    public function excelRfidTransactions(Request $request) {
        $result = RfidCardTransaction::whereDate('created_at', $request->date);

        $result = $result->select('created_at', 'owner_name', 'machine_name', 'minutes', 'price', 'rfid', 'card_type')->orderByDesc('created_at')->get();

        return Excel::download(new ReportTemplate($result, [
            'DATE & TIME',
            'CARD OWNER',
            'MACHINE NAME',
            'MINUTES',
            'PRICE',
            'RFID',
            'CARD TYPE',
        ]), 'parts.xls');
    }

    public function excelRfidLoadTransactions(Request $request) {
        $result = RfidLoadTransaction::whereDate('created_at', $request->date);

        $result = $result->select('created_at', 'customer_name', 'rfid', 'amount', 'remarks')->orderByDesc('created_at')->get();

        return Excel::download(new ReportTemplate($result, [
            'DATE & TIME',
            'CUSTOMER',
            'RFID',
            'AMOUNT',
            'REMARKS',
        ]), 'parts.xls');
    }

    // public function posCollections(Request $request) {
    //     $serviceTransactions = DB::table('transactions')
    //         ->whereDate('transactions.date_paid', $request->date)
    //         ->whereNull('transactions.deleted_at')
    //         ->whereNull('service_transaction_items.deleted_at')
    //         ->join('service_transaction_items', 'transactions.id', '=', 'service_transaction_items.transaction_id')
    //         ->selectRaw('transactions.id as transaction_id, job_order, date, customer_name, service_transaction_items.name as item_name, service_transaction_items.price as item_price, transactions.date_paid as _date');

    //     $result = DB::table('transactions')
    //         ->whereDate('transactions.date_paid', $request->date)
    //         ->whereNull('transactions.deleted_at')
    //         ->whereNull('product_transaction_items.deleted_at')
    //         ->join('product_transaction_items', 'transactions.id', '=', 'product_transaction_items.transaction_id')
    //         ->selectRaw('transactions.id as transactions_id, job_order, date, customer_name, product_transaction_items.name as item_name, product_transaction_items.price as item_price, transactions.date_paid as _date')
    //         ->unionAll($serviceTransactions)
    //         ->orderBy(DB::raw('job_order, item_name'))
    //         ->get();
    //         // ->groupBy('job_order');


    //     return view('printer.pos-collections', [
    //         'result' => $result,
    //     ]);
    // }

    public function printPosCollections(Request $request) {
        $result = Transaction::with('serviceTransactionItems', 'productTransactionItems', 'payment')
            ->whereDate('date_paid', $request->date)
            ->orderBy('job_order')
            ->get();

        $posCollections = TransactionPayment::whereDate('created_at', $request->date)
            ->selectRaw('SUM(`total_amount` - (`total_amount` /  100 * `discount`) - `points_in_peso` - `card_load_used`) as total_price, COUNT(id) as total_count')->first();

        $summary = [
            'totalCount' => $posCollections->total_count,
            'totalCollections' => $posCollections->total_price,
            'totalSales' => $result->sum('total_price'),
        ];

        return view('printer.pos-collections', [
            'result' => $result,
            'summary' => $summary,
        ]);
    }

    public function printPosTransactions(Request $request) {
        $result = Transaction::with('serviceTransactionItems', 'productTransactionItems', 'payment')
            ->whereDate('date', $request->date)
            ->orderBy('job_order')
            ->get();

        $unpaid = $result->filter(function($item) {
            return $item->date_paid == null;
        });

        $paid = $result->filter(function($item) {
            return $item->date_paid != null;
        });

        $summary = [
            'totalCount' => $result->count(),
            'totalSales' => $result->sum('total_price'),
            'paidCount' => $paid->count(),
            'totalCollections' => $paid->sum('total_price'),
            'unpaidCount' => $unpaid->count(),
            'totalUnpaid' => $unpaid->sum('total_price'),
        ];

        return view('printer.pos-transactions', [
            'result' => $result,
            'summary' => $summary,
        ]);
    }

    public function printRfidTransactions(Request $request) {
        $result = RfidCardTransaction::whereDate('created_at', $request->date);

        if($request->cardType == 'c') {
            $result = $result->where('card_type', 'c');
        } else if($request->cardType == 'u') {
            $result = $result->where('card_type', 'u');
        }

        $result = $result->orderByDesc('created_at')->get();

        $customerCards = $result->filter(function($item) {
            return $item->card_type == 'c';
        });
        $userCards = $result->filter(function($item) {
            return $item->card_type == 'u';
        });

        $summary = [
            'customerCount' => $customerCards->count(),
            'customerCollections' => $customerCards->sum('price'),
            'userCount' => $userCards->count(),
            'userCollections' => $userCards->sum('price'),
            'totalCount' => $result->count(),
            'totalSales' => $result->sum('price'),
        ];

        return view('printer.rfid-transactions', [
            'result' => $result,
            'summary' => $summary,
        ]);
    }

    public function printRfidLoadTransactions(Request $request) {
        $result = RfidLoadTransaction::whereDate('created_at', $request->date)
            ->orderByDesc('created_at')->get();

        $summary = [
            'totalCount' => $result->count(),
            'totalPrice' => $result->sum('amount'),
        ];

        return view('printer.rfid-load-transactions', [
            'result' => $result,
            'summary' => $summary,
        ]);
    }

    public function printDailySale($date) {
        return view('printer.daily', [
            'date' => $date,
        ]);
    }
}
