<?php

namespace App\Http\Controllers;

use App\ServiceTransactionItem;
use App\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionsController extends Controller
{
    public function viewServiceItems($transactionId, Request $request) {
        $result = ServiceTransactionItem::with('customerWash')->where([
            'transaction_id' => $transactionId,
            'name' => $request->serviceName,
        ])->orderBy('created_at')->get();

        $result = $result->transform(function($item) {
            return [
                'service_transaction_item_id' => $item->id,
                'name' => $item->name,
                'saved' => $item->saved,
                'added' => $item->created_at,
                'machine_name' => $item->machineName(),
                'used' => $item->used(),
                'created_at' => $item->created_at,
            ];
        });

        return response()->json([
            'result' => $result,
        ]);
    }

    public function show($transactionId) {
        $transaction = Transaction::with('customer', 'payment')->findOrfail($transactionId);
        $transaction->refreshAll();

        return response()->json([
            'transaction' => $transaction
        ]);
    }

    public function unpaidTransactions(Request $request) {
        // $result = DB::table('transactions')
        //     ->where('customers.name', 'like', "%$request->keyword%")
        //     ->whereNotNull('saved')
        //     ->select('transactions.id','job_order', 'saved', 'total_price', 'customer_id', 'customers.id', 'customers.name')
        //     ->join('customers', 'customers.id', '=', 'customer_id')
        //     ->orderBy('name');

        $sortBy = $request->sortBy ? $request->sortBy : 'date';
        $order = $request->orderBy ? $request->orderBy : 'asc';

        $result = Transaction::where(function($query) use ($request) {
            $query->where('customer_name', 'like', "%$request->keyword%")
                ->orWhere('job_order', 'like', "%$request->keyword%");
        })
            ->whereNotNull('saved')
            ->whereDoesntHave('payment');

        $result = $result->orderBy($sortBy, $order);

        if($request->date) {
            $result = $result->whereDate('saved', $request->date);
        }

        return response()->json([
            'result' => $result->paginate(),
        ]);
    }

    public function byJobOrders(Request $request) {
        $sortBy = $request->sortBy ? $request->sortBy : 'date';
        $order = $request->orderBy ? $request->orderBy : 'desc';

        $result = Transaction::with(['payment' => function($query) {
            $query->select('transaction_id', 'date');
        }])->where(function($query) use ($request) {
            $query->where('customer_name', 'like', "%$request->keyword%")
                ->orWhere('job_order', 'like', "%$request->keyword%");
        })->whereNotNull('saved');

        $result = $result->orderBy($sortBy, $order);

        if($request->date) {
            $result = $result->whereDate('date', $request->date);
        }

        $result = $result->paginate(10);

        $result->getCollection()->transform(function($item) {
            return [
                'id' => $item->id,
                'job_order' => $item->job_order,
                'customer_name' => $item->customer_name,
                'date' => $item->date,
                'total_price' => $item->total_price,
                'date_paid' => $item->payment == null ? null : $item->payment['date'],
            ];
        });

        return response()->json([
            'result' => $result,
        ]);
    }

    public function byServiceItems(Request $request) {
        $sortBy = $request->sortBy ? $request->sortBy : 'date';
        $order = $request->orderBy ? $request->orderBy : 'desc';

        $result = DB::table('service_transaction_items')
            ->where(function($query) use ($request) {
                $query->where('customer_name', 'like', "%$request->keyword%")
                    ->orWhere('name', 'like', "%$request->keyword%")
                    ->orWhere('job_order', 'like', "%$request->keyword%");
            })->where('service_transaction_items.saved', true)
            ->join('transactions', 'transactions.id','=', 'service_transaction_items.transaction_id')
                ->groupBy('job_order', 'customer_name', 'name', 'date', 'transaction_id')->selectRaw('job_order, customer_name, name, date, transaction_id, SUM(price) as price, COUNT(name) as quantity');

        if($request->date) {
            $result = $result->whereDate('transactions.saved', $request->date);
        }

        $result = $result->orderBy($sortBy, $order);

        return response()->json([
            'result' => $result->paginate(10),
        ]);
    }

    public function byProductItems(Request $request) {
        $sortBy = $request->sortBy ? $request->sortBy : 'date';
        $order = $request->orderBy ? $request->orderBy : 'desc';

        $result = DB::table('product_transaction_items')
            ->where(function($query) use ($request) {
                $query->where('customer_name', 'like', "%$request->keyword%")
                    ->orWhere('name', 'like', "%$request->keyword%")
                    ->orWhere('job_order', 'like', "%$request->keyword%");
            })
            ->join('transactions', 'transactions.id','=', 'product_transaction_items.transaction_id')
                ->groupBy('job_order', 'customer_name', 'name', 'date', 'transaction_id')->selectRaw('job_order, customer_name, name, date, transaction_id, SUM(price) as price, COUNT(name) as quantity');

        if($request->date) {
            $result = $result->whereDate('transactions.saved', $request->date);
        }

        $result = $result->orderBy($sortBy, $order);

        return response()->json([
            'result' => $result->paginate(10),
        ]);
    }
}
