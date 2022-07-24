<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReportTemplate;
use App\CompletedServiceTransaction;
use App\CompletedProductTransaction;
use App\Transaction;
use App\RfidTransaction;
use App\RfidLoadTransaction;
use Illuminate\Support\Facades\DB;

class ExcelReportsController extends Controller
{
    public function posServices(Request $request, $clientId, $branchId = null) {
        if($clientId == 'self') {
            $clientId = auth('api')->user()->client_id;
            $branchId = auth('api')->user()->active_branch_id;
        }

        // $result = ServiceTransaction::with(['branchService.service', 'transaction.customer', 'transaction.payment.user'])
        //     ->where(function($query) use ($request) {
        //         $query->whereHas('branchService.service', function($query) use ($request) {
        //             $query->where('name', 'like', "%$request->keyword%");
        //         })->orWhereHas('transaction.customer', function($query) use ($request) {
        //             $query->where('name', 'like', "%$request->keyword%");
        //         });
        //     })->whereHas('branchService', function($query) use ($branchId) {
        //         $query->where('branch_id', $branchId);
        //     })->where('saved', true)
        //     ->orderByDesc('created_at');

        // if($request->transactionId) {
        //     $result = $result->where('transaction_id', $request->transactionId);
        // }

        // $result = $result->get()->transform(function($item) {
        //     return [
        //         'customerName' => $item->transaction->customer['name'],
        //         'serviceName' => $item->branchService->service['name'],
        //         'unitPrice' => $item->unit_price,
        //         'quantity' => $item->quantity,
        //         'totalAmount' => $item->unit_price * $item->quantity,
        //         'datePaid' => $item->transaction->payment['date'],
        //         'paidTo' => $item->transaction->payment ? $item->transaction->payment->user->fullname : '',
        //     ];
        // });


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
        }

        if($request->dateRange) {
            $dateRange = json_decode($request->dateRange);
            $result = $result->whereBetween(DB::raw('DATE(created_at)'), [
                $dateRange->from,
                $dateRange->to,
            ]);
        }

        if($result->count() == 0) {
            return response()->json([
                'errors' => [
                    'message' => ['No data']
                ]
            ], 422);
        }

        $result = $result->get()->transform(function($item) {
            return [
                'jobOrder' => $item->transaction['job_order'],
                'customerName' => $item->transaction->customer['name'],
                'serviceName' => $item->branchService->service['name'],
                'unitPrice' => $item->serviceTransaction['unit_price'],
                'quantity' => $item->quantity,
                'datePaid' => $item->transaction->payment['date'] ? date('d-m-Y', strtotime($item->transaction->payment['date'])) : '',
                'paidTo' => $item->transaction->payment ? $item->transaction->payment->user->fullname : '',
                'addedBy' => $item->user['fullname'],
                'machineName' => $item->machine_name,
                'available' => $item->available,
            ];
        });


        return Excel::download(new ReportTemplate($result, [
            'JOB ORDER',
            'CUSTOMER NAME',
            'SERVICE NAME',
            'UNIT PRICE',
            'QUANTITY',
            'DATE PAID',
            'PAID TO',
            'ADDED BY',
            'MACHINE NAME'
        ]), 'parts.xls');
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

        if($result->count() == 0) {
            return response()->json([
                'errors' => [
                    'message' => ['No data']
                ]
            ], 422);
        }

        $result = collect($result->get())->transform(function($item) {
            return [
                'jobOrder' => $item->transaction['job_order'],
                'customerName' => $item->transaction->customer['name'],
                'productName' => $item->product['name'],
                'unitPrice' => $item->productTransaction['unit_price'],
                'quantity' => $item->quantity,
                'datePaid' => $item->transaction->payment['date'] ? date('d-m-Y', strtotime($item->transaction->payment['date'])) : '',
                'paidTo' => $item->transaction->payment ? $item->transaction->payment->user->fullname : '',
                'addedBy' => $item->user['fullname'],
            ];
        });

        return Excel::download(new ReportTemplate($result, [
            'JOB ORDER',
            'CUSTOMER NAME',
            'PRODUCT NAME',
            'UNIT PRICE',
            'QUANTITY',
            'DATE PAID',
            'PAID TO',
            'ADDED BY',
        ]), 'parts.xls');
    }
    public function posJobOrder(Request $request, $branchId) {
        $branchId = $branchId == 'self' ? auth('api')->user()->active_branch_id : $branchId;

        $result = Transaction::with(['customer', 'payment.user'])->where(function($query) use ($request) {
            $query->whereHas('customer', function($query) use ($request) {
                $query->where('name', 'like', "%$request->keyword%");
            })->orWhere('job_order', 'like', "%$request->keyword%");
        })->whereHas('branch', function($query) use ($branchId) {
            $query->where([
                'branch_id' => $branchId,
            ]);
        })->whereNotNull('date_saved')
        ->orderByDesc('created_at');

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

        if($result->count() == 0) {
            return response()->json([
                'errors' => [
                    'message' => ['No data']
                ]
            ], 422);
        }

        $result = collect($result->get())->transform(function($item) {
            return [
                'date' => $item->date,
                'customerName' => $item->customer['name'],
                'jobOrder' => $item->job_order,
                'datePaid' => $item->date_paid ? $item->date_paid : '(Not paid)',
                'userName' => $item->user['fullname'],
                'paidTo' => $item->payment ? $item->payment->user->fullname : '(Not paid)',
                'productsCount' => $item->total_products,
                'servicesCount' => $item->total_services,
                'serviceAmount' => $item->service_amount,
                'productAmount' => $item->product_amount,
                'totalAmount' => $item->service_amount + $item->product_amount,
            ];
        });

        return Excel::download(new ReportTemplate($result, [
            'DATE',
            'CUSTOMER NAME',
            'JOB ORDER',
            'DATE PAID',
            'CREATED BY',
            'PAID TO',
            'PRODUCT COUNT',
            'SERVICE COUNT',
            'PRODUCT AMOUNT',
            'SERVICE AMOUNT',
            'TOTAL AMOUNT',
        ]), 'parts.xls');
    }

    public function rfidServices($branchId, Request $request) {
        $branchId = $branchId == 'self' ? auth('api')->user()->active_branch_id : $branchId;

        $result = RfidTransaction::with('machine', 'rfidCard.customer', 'user', 'rfidServicePrice')->where(function($query) use ($request) {
            $query->whereHas('rfidCard', function($query) use ($request) {
                $query->whereHas('customer', function($query) use ($request) {
                    $query->where('name', 'like', "%$request->keyword%");
                });
            })->orWhereHas('user', function($query) use ($request) {
                $query->where('firstname', 'like', "%$request->keyword%")
                    ->orWhere('lastname', 'like', "%$request->keyword%");
            })->orWhereHas('machine', function($query) use ($request) {
                $query->where('name', 'like', "%$request->keyword%");
            })->orWhereHas('rfidCard', function($query) use ($request) {
                $query->where('rfid', 'like', "%$request->keyword%");
            });
        })->where('branch_id', $branchId)->orderByDesc('created_at');


        if($request->dateRange) {
            $dateRange = json_decode($request->dateRange);
            $result = $result->whereBetween(DB::raw('DATE(created_at)'), [
                $dateRange->from,
                $dateRange->to,
            ]);
        }

        if($result->count() == 0) {
            return response()->json([
                'errors' => [
                    'message' => ['No data']
                ]
            ], 422);
        }

        $result = collect($result->get())->transform(function($item) {
            return [
                'date' => $item->created_at,
                'machineName' => $item->machine['name'],
                'customerName' => $item->rfidCard['owner_name'],
                'rfid' => $item->rfidCard['rfid'],
                'serviceName' => $item->rfidServicePrice['name'],
                'servicePrice' => $item->price,
                // 'productsCount' => $item->total_products,
                // 'servicesCount' => $item->total_services,
                // 'serviceAmount' => $item->service_amount,
                // 'productAmount' => $item->product_amount,
                // 'totalAmount' => $item->service_amount + $item->product_amount,
            ];
        });



        return Excel::download(new ReportTemplate($result, [
            'DATE',
            'MACHINE NAME',
            'CUSTOMER NAME',
            'RFID',
            'SERVICE NAME',
            'PRICE',
        ]), 'parts.xls');
    }

    public function rfidTopups(Request $request) {
        $result = RfidLoadTransaction::with('user', 'rfidCard')
            ->where(function($query) use ($request) {
                $query->whereHas('rfidCard.customer', function($query) use ($request) {
                    $query->where('name', 'like', "%$request->keyword%");
                })->orWhereHas('rfidCard', function($query) use ($request) {
                    $query->where('rfid', 'like', "%$request->keyword%");
                });
            })->whereHas('rfidCard', function($query) use ($request) {
                $query->where('branch_id', auth('api')->user()->active_branch_id)->orderByDesc('created_at');
            });

        if(auth('api')->user()->hasAnyRole(['oic', 'staff'])) {
            $result = $result->where(function($query) {
                $query->where('user_id', auth('api')->id());
            });
        }


        if($request->dateRange) {
            $dateRange = json_decode($request->dateRange);
            $result = $result->whereBetween(DB::raw('DATE(created_at)'), [
                $dateRange->from,
                $dateRange->to,
            ]);
        }

        if($result->count() == 0) {
            return response()->json([
                'errors' => [
                    'message' => ['No data']
                ]
            ], 422);
        }

        $result = collect($result->get())->transform(function($item) {
            return [
                'date' => $item->created_at,
                'customerName' => $item->rfidCard['owner_name'],
                'rfid' => $item->rfidCard['rfid'],
                'amount' => $item->amount,
                'remarks' => $item->remarks,
                'user' => $item->user['fullname'],
                // 'productsCount' => $item->total_products,
                // 'servicesCount' => $item->total_services,
                // 'serviceAmount' => $item->service_amount,
                // 'productAmount' => $item->product_amount,
                // 'totalAmount' => $item->service_amount + $item->product_amount,
            ];
        });

        return Excel::download(new ReportTemplate($result, [
            'DATE',
            'CUSTOMER',
            'RFID',
            'AMOUNT',
            'REMARKS',
            'USER',
        ]), 'parts.xls');

    }
}
