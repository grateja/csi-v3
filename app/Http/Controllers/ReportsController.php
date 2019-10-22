<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Transaction;
use App\ServiceTransaction;
use App\ProductTransaction;
use Illuminate\Support\Facades\DB;
use App\CompletedServiceTransaction;
use App\CompletedProductTransaction;

class ReportsController extends Controller
{
    public function posTransactionsByCustomers(Request $request, $clientId, $branchId = null) {
        if($clientId == 'self') {
            $clientId = auth('api')->user()->client_id;
            $branchId = auth('api')->user()->active_branch_id;
        }

        $result = Transaction::with(['customer', 'payment.user'])->where(function($query) use ($request) {
            $query->whereHas('customer', function($query) use ($request) {
                $query->where('name', 'like', "%$request->keyword%");
            })->orWhere('job_order', 'like', "%$request->keyword%");
        })->whereHas('branch', function($query) use ($clientId, $branchId) {
            $query->where([
                'client_id' => $clientId,
                'branch_id' => $branchId,
            ]);
        })->whereNotNull('date_saved')
        ->orderByDesc('job_order');

        if(auth('api')->user()->hasAnyRole(['staff', 'oic'])) {
            $result = $result->where(function($query) {
                $query->whereHas('payment.user', function($query) {
                    $query->where('user_id', auth('api')->id());
                })->orWhere('user_id', auth('api')->id());
            });
            // $result = $result->whereHas('payment.user', function($query) {
            //     $query->where('user_id', auth('api')->id());
            // })->orWhere('user_id', auth('api')->id());
        }

        if($request->dateRange) {
            $dateRange = json_decode($request->dateRange);
            $result = $result->whereBetween(DB::raw('DATE(date)'), [
                $dateRange->from,
                $dateRange->to,
            ]);
        }

        $get = collect($result->get());

        $summary = [
            'totalProducts' => $get->sum('total_products'),
            'totalServices' => $get->sum('total_services'),
            'totalProductsAmount' => $get->sum('product_amount'),
            'totalServicesAmount' => $get->sum('service_amount'),
        ];

        $result = $result->paginate(10);

        $result->getCollection()->transform(function($item) {
            return [
                'id' => $item->id,
                'date' => $item->date,
                'customerName' => $item->customer['name'],
                'jobOrder' => $item->job_order,
                'datePaid' => $item->date_paid ? $item->date_paid : '(Not paid)',
                'paidTo' => $item->payment ? $item->payment->user->firstname : '(Not paid)',
                'userName' => $item->user['firstname'],
                'productsCount' => $item->total_products,
                'servicesCount' => $item->total_services,
                'serviceAmount' => $item->service_amount,
                'productAmount' => $item->product_amount,
                'paid' => $item->date_paid != null,
            ];
        });

        return response()->json([
            'result' => $result,
            'summary' => $summary,
        ], 200);
    }

    public function posServices(Request $request, $branchId) {
        $branchId = $branchId == 'self' ? auth('api')->user()->active_branch_id : $branchId;
        $summary = null;

        $result = CompletedServiceTransaction::with(['customer' => function($query) {
            $query->select('id', 'name');
        }, 'service' => function($query) {
            $query->select('id', 'name');
        }, 'transaction.payment', 'serviceTransaction'])->where(function($query) use ($request) {
            $query->whereHas('service', function($query) use ($request) {
                $query->where('name', 'like', "%$request->keyword%");
            })->orWhereHas('transaction.customer', function($query) use ($request) {
                $query->where('name', 'like', "%$request->keyword%");
            });
        })->where('branch_id', $branchId)->orderByDesc('id');

        if($request->transactionId) {
            $result = $result->where('transaction_id', $request->transactionId);
        }

        if(auth('api')->user()->hasAnyRole(['staff', 'oic'])) {
            $result = $result->where(function($query) {
                $query->whereHas('transaction.payment.user', function($query) {
                    $query->where('user_id', auth('api')->id());
                })->orWhere('user_id', auth('api')->id());
            });

            // $result = $result->whereHas('transaction.payment.user', function($query) {
            //     $query->where('user_id', auth('api')->id());
            // })->orWhere('user_id', auth('api')->id());
        }

        if($request->dateRange) {
            $dateRange = json_decode($request->dateRange);
            $result = $result->whereBetween(DB::raw('DATE(created_at)'), [
                $dateRange->from,
                $dateRange->to,
            ]);
        }

        $resultData = $result->paginate(10);
        $resultData->data = $resultData->getCollection()->transform(function($item) {
            return [
                'date' => $item->created_at,
                'jobOrder' => $item->transaction['job_order'],
                'customerName' => $item->transaction->customer['name'],
                'serviceName' => $item->branchService->service['name'],
                'unitPrice' => $item->serviceTransaction['unit_price'],
                'quantity' => $item->quantity,
                'totalAmount' => $item->price_sum,
                'datePaid' => $item->transaction->payment['date'],
                'paidTo' => $item->transaction->payment ? $item->transaction->payment->user->fullname : '',
                'addedBy' => $item->user['fullname'],
                'machineName' => $item->machine_name,
                'id' => $item->id,
                'available' => $item->available,
            ];
        });

        if($result->count()) {
            $groupResult = $result->skip(0)->with(['branchService' => function($query) {
                $query->with(['service' => function($query) {
                    $query->select('id', 'name');
                }])->select('id', 'service_id');
            }])->groupBy('branch_service_id')->selectRaw('branch_service_id, SUM(quantity) as quantitySum, SUM(price_sum) as priceSum')->get();

            $summary = collect($groupResult)->transform(function($item) {
                return [
                    'name' => $item->branchService->service['name'],
                    'quantity' => $item->quantitySum,
                    'amount' => $item->priceSum,
                ];
            });
        }

        return response()->json([
            'result' => $resultData,
            'summary' => $summary
        ], 200);
    }

    public function posProducts(Request $request, $branchId) {
        $branchId = $branchId == 'self' ? auth('api')->user()->active_branch_id : $branchId;
        $summary = null;

        $result = CompletedProductTransaction::with(['customer' => function($query) {
            $query->select('id', 'name');
        }, 'product' => function($query) {
            $query->select('id', 'name');
        }, 'transaction.payment', 'productTransaction'])->where(function($query) use ($request) {
            $query->whereHas('product', function($query) use ($request) {
                $query->where('name', 'like', "%$request->keyword%");
            })->orWhereHas('transaction.customer', function($query) use ($request) {
                $query->where('name', 'like', "%$request->keyword%");
            });
        })->where('branch_id', $branchId)->orderByDesc('id');

        if($request->transactionId) {
            $result = $result->where('transaction_id', $request->transactionId);
        }

        if(auth('api')->user()->hasAnyRole(['staff', 'oic'])) {
            $result = $result->where(function($query) {
                $query->whereHas('transaction.payment.user', function($query) {
                    $query->where('user_id', auth('api')->id());
                })->orWhere('user_id', auth('api')->id());
            });
            // $result = $result->whereHas('transaction.payment.user', function($query) {
            //     $query->where('user_id', auth('api')->id());
            // })->orWhere('user_id', auth('api')->id());
        }

        if($request->dateRange) {
            $dateRange = json_decode($request->dateRange);
            $result = $result->whereBetween(DB::raw('DATE(created_at)'), [
                $dateRange->from,
                $dateRange->to,
            ]);
        }

        $resultData = $result->paginate(10);

        $resultData->getCollection()->transform(function($item) {
            return [
                'date' => $item->created_at,
                'jobOrder' => $item->transaction['job_order'],
                'customerName' => $item->transaction->customer['name'],
                'productName' => $item->product['name'],
                'unitPrice' => $item->productTransaction['unit_price'],
                'quantity' => $item->quantity,
                'totalAmount' => $item->price_sum,
                'datePaid' => $item->transaction->payment['date'],
                'paidTo' => $item->transaction->payment ? $item->transaction->payment->user->fullname : '',
                'addedBy' => $item->user['fullname'],
                'id' => $item->id,
            ];
        });

        if($result->count()) {
            $groupResult = $result->skip(0)->with(['branchProduct' => function($query) {
                $query->with(['product' => function($query) {
                    $query->select('id', 'name');
                }])->select('id', 'product_id');
            }])->groupBy('branch_product_id')->selectRaw('branch_product_id, SUM(quantity) as quantitySum, SUM(price_sum) as priceSum')->get();

            $summary = collect($groupResult)->transform(function($item) {
                return [
                    'name' => $item->branchProduct->product['name'],
                    'quantity' => $item->quantitySum,
                    'amount' => $item->priceSum,
                ];
            });
        }

        return response()->json([
            'result' => $resultData,
            'summary' => $summary,
        ], 200);
    }
}
