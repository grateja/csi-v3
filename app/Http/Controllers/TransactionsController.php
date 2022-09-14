<?php

namespace App\Http\Controllers;

use App\Customer;
use App\Jobs\SendTransaction;
use App\ProductTransactionItem;
use App\ServiceTransactionItem;
use App\Transaction;
use App\TransactionRemarks;
use Carbon\Carbon;
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

    public function show($transactionId, Request $request) {
        $transaction = Transaction::withTrashed()
            ->with(
            'payment.user',
            'partialPayment.user'
            )
        ->where('id', $transactionId)
        ->orWhere(function($query) use ($transactionId, $request) {
            $query->where([
                'job_order' => $transactionId,
                // 'date' => $request->date,
            ]);
        })->first();

        if($transaction == null) {
            return response(['error'=>true],404);
        }

        $transaction->refreshAll($transaction->deleted_at);


        $transaction['birthdayToday'] = Carbon::createFromDate($transaction->customer['first_visit'])->setYear(date('Y'))->isToday();

        return response()->json([
            'transaction' => $transaction,
        ]);
    }

    public function unpaidTransactions(Request $request) {
        $sortBy = Transaction::filterKeys($request->sortBy);
        // $sortBy = $request->sortBy ? $request->sortBy : 'date';
        $order = $request->orderBy ? $request->orderBy : 'desc';

        $result = Transaction::with([
            'partialPayment' => function($query) {
                $query->select('id', 'transaction_id', 'balance');
            }
        ])->where(function($query) use ($request) {
            $query->where('customer_name', 'like', "%$request->keyword%")
                ->orWhere('job_order', 'like', "%$request->keyword%");
        })
            ->where('saved', true)
            ->whereDoesntHave('payment');

        $result = $result->orderBy($sortBy, $order);

        if($request->date) {
            $result = $result->whereDate('date', $request->date);
        }

        return response()->json([
            'result' => $result->paginate(10),
            'summary' => $this->unpaidSummary($request)
        ]);
    }

    private function unpaidSummary($request) {
        $result = Transaction::where(function($query) use ($request) {
            $query->where('customer_name', 'like', "%$request->keyword%")
                ->orWhere('job_order', 'like', "%$request->keyword%");
        })
            ->where('saved', true)
            ->whereDoesntHave('payment');

        if($request->date) {
            $result = $result->whereDate('date', $request->date);
        }
        return [
            'total_items' => $result->count(),
            'total_price' => $result->sum('total_price'),
        ];
    }

    private function jobOrderSummary($request) {
        $result = Transaction::where(function($query) use ($request) {
            $query->where('customer_name', 'like', "%$request->keyword%")
                ->orWhere('job_order', 'like', "%$request->keyword%");
        })->where('saved', true);

        if($request->date) {
            $result = $result->whereDate('date', $request->date);
        }

        if($request->datePaid) {
            $result = $result->whereDate('date_paid', $request->datePaid);
        }

        return [
            'total_items' => $result->count(),
            'total_price' => $result->sum('total_price'),
        ];
    }

    public function byJobOrders(Request $request) {
        // $sortBy = $request->sortBy ? $request->sortBy : 'date';
        $order = $request->orderBy ? $request->orderBy : 'desc';

        $sortBy = Transaction::filterKeys($request->sortBy);

        $result = Transaction::with(['payment' => function($query) {
            $query->select('id', 'date');
        }, 'partialPayment' => function($query) {
            $query->select('id', 'transaction_id', 'date', 'total_amount', 'balance');
        }])->where(function($query) use ($request) {
            $query->where('customer_name', 'like', "%$request->keyword%")
                ->orWhere('job_order', 'like', "%$request->keyword%");
        })->where('saved', true);

        $result = $result->orderBy($sortBy, $order);

        if($request->date) {
            $result = $result->whereDate('date', $request->date);
        }

        if($request->datePaid) {
            $result = $result->whereDate('date_paid', $request->datePaid);
        }

        if($request->hideDeleted == 'false') {
            $result = $result->withTrashed()->paginate(10);
        } else {
            $result = $result->paginate(10);
        }


        // $result->getCollection()->transform(function($item) {
        //     return [
        //         'id' => $item->id,
        //         'job_order' => $item->job_order,
        //         'customer_name' => $item->customer_name,
        //         'date' => $item->date,
        //         'total_price' => $item->total_price,
        //         'date_paid' => $item->payment == null ? null : $item->payment['date'],
        //     ];
        // });

        return response()->json([
            'result' => $result,
            'summary' => $this->jobOrderSummary($request),
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
            ->whereNull('service_transaction_items.deleted_at')
            ->join('transactions', 'transactions.id','=', 'service_transaction_items.transaction_id')
                ->groupBy('job_order', 'customer_name', 'name', 'date', 'transaction_id', 'date_paid')->selectRaw('job_order, customer_name, name, date, transaction_id, SUM(price) as price, COUNT(name) as quantity, transactions.date_paid as date_paid');

        if($request->date) {
            $result = $result->whereDate('transactions.date', $request->date);
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
            })->where('product_transaction_items.saved', true)
            ->whereNull('product_transaction_items.deleted_at')
            ->join('transactions', 'transactions.id','=', 'product_transaction_items.transaction_id')
                ->groupBy('job_order', 'customer_name', 'name', 'date', 'transaction_id', 'date_paid')->selectRaw('job_order, customer_name, name, date, transaction_id, SUM(price) as price, COUNT(name) as quantity, transactions.date_paid as date_paid');

        if($request->date) {
            $result = $result->whereDate('transactions.date', $request->date);
        }

        $result = $result->orderBy($sortBy, $order);

        return response()->json([
            'result' => $result->paginate(10)
        ]);
    }

    public function unsavedTransactions() {
        $transactions = Transaction::whereNull('job_order')
            ->whereDate('created_at','<>', Carbon::now())
            ->get();
        foreach($transactions as $transaction) {
            $transaction->forceDelete();
        }

        $result = Customer::with(['transactions' => function($query) {
            $query->where('saved', false);
        }])->whereHas('transactions', function($query) {
            $query->where('saved', false);
        })->orderBy('name')->get();

        return response()->json([
            'result' => $result,
            'fromDifferentDates' => $transactions,
        ]);
    }

    public function deleteTransaction($transactionId, Request $request) {
        return DB::transaction(function () use ($transactionId, $request) {
            $transaction = Transaction::withTrashed()->findOrFail($transactionId);

            if($request->permanent) {
                $transaction->forceDelete();
            } else {
                $transaction->delete();
                $name = auth('api')->user()->name;

                TransactionRemarks::create([
                    'transaction_id' => $transactionId,
                    'remarks' => 'Deleted by ' . $name,
                    'added_by' => $name,
                ]);

                $this->dispatch((new SendTransaction($transactionId))->delay(5));
            }
            return response()->json([
                'transaction' => $transaction,
            ]);
        });
    }
}
