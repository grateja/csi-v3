<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Customer;
use App\Transaction;
use App\CustomerWash;
use App\CustomerDry;
use Carbon\Carbon;
use App\ProductTransactionItem;
use App\ServiceTransactionItem;
use App\LagoonTransactionItem;
use App\LagoonPerKiloTransactionItem;
use App\ScarpaCleaningTransactionItem;
use Illuminate\Support\Facades\DB;

class DailyReportController extends Controller
{
    public function index(Request $request) {
        $customers = Customer::whereDate('created_at', $request->date)->get();
        $jobOrdersToday = Transaction::withTrashed()->with('payment', 'partialPayment', 'remarks')
            ->whereDate('date', $request->date)
            ->orWhereHas('payment', function($query) use ($request) {
                $query->whereDate('date', $request->date);
            })->orWhereHas('partialPayment', function($query) use ($request) {
                $query->whereDate('date', $request->date);
            })->orWhereDate('deleted_at', $request->date)->orderByDesc('date')->get();

        $jobOrdersToday = [
            // 'created' => $jobOrdersToday->filter(function($item) use ($request) {
            //     return $item->deleted_at == null && Carbon::createFromDate($item->date)->format('Y-m-d') == $request->date;
            // })->values(),
            'paid' => $jobOrdersToday->filter(function($item) use ($request) {
                if($item->deleted_at == null && $item->payment != null) {
                    return Carbon::createFromDate($item->date_paid)->format('Y-m-d') == $request->date;
                }

                if($item->deleted_at == null && $item->partialPayment != null) {
                    return Carbon::createFromDate($item->partialPayment->date)->format('Y-m-d') == $request->date;
                }
            })->values(),
            // 'unCollected' => $jobOrdersToday->filter(function($item) use ($request) {
            //     return $item->deleted_at == null && $item->payment == null && $item->partialPayment == null;
            // })->values(),
            'canceled' => $jobOrdersToday->filter(function($item) use ($request) {
                return $item->deleted_at != null && Carbon::createFromDate($item->deleted_at)->format('Y-m-d') == $request->date;
            })->values(),
        ];

        $jobOrdersOtherDays = [
            'unCollected' => Transaction::with('partialPayment', 'payment')->where(function($query) use ($request) {
                $query//->whereDate('date', '<>', $request->date)
                    ->whereNull('date_paid')->whereNull('cancelation_remarks');
            })->get()->values(),
            'canceled' => Transaction::withTrashed()->where(function($query) use ($request) {
                $query->where(function($query) {
                    $query->whereNotNull('deleted_at')->orWhereNotNull('cancelation_remarks');
                });//->whereDate('date', '<>', $request->date);
            })->get()->values(),
        ];

        $collections = [
            'jobOrders' => null,
        ];

        $pendingServices = Transaction::withCount(['customerDries' => function($query) {
            $query->whereNull('used');
        }, 'customerWashes' => function($query) {
            $query->whereNull('used');
        }])
            ->whereHas('customerDries', function($query) {
                $query->whereNull('used');
            })->orWhereHas('customerWashes', function($query) {
                $query->whereNull('used');
            })->orderBy('job_order')->get();

        $pendingAllTime = collect($pendingServices->filter(function($item) use ($request) {
            return Carbon::createFromDate($item->date)->format('Y-m-d') != $request->date;
        }))->values();

        $pendingToday = collect($pendingServices->filter(function($item) use ($request) {
            return Carbon::createFromDate($item->date)->format('Y-m-d') == $request->date;
        }))->values();

        // $pendingWashes = CustomerWash::with('customer')->where(function($query) {
        //     $query->whereNull('used');
        // })->groupBy('job_order', 'customer_id', 'date')->selectRaw('job_order, customer_id, date(created_at) as date, count(*) as total')->get();

        // $pendingDries = CustomerDry::with('customer')->where(function($query) {
        //     $query->whereNull('used');
        // })->groupBy('job_order', 'customer_id', 'date')->selectRaw('job_order, customer_id, date(created_at) as date, count(*) as total')->get();


        // $pendingWashToday = collect($pendingWashes)->filter(function($item) use ($request) {
        //     return $item->date == $request->date;
        // });
        // $pendingWashAllTime = collect($pendingWashes)->filter(function($item) use ($request) {
        //     return $item->date != $request->date;
        // });
        // $pendingDryToday = collect($pendingDries)->filter(function($item) use ($request) {
        //     return $item->date == $request->date;
        // });
        // $pendingDryAllTime = collect($pendingDries)->filter(function($item) use ($request) {
        //     return $item->date != $request->date;
        // });


        $customerWashes = collect(CustomerWash::with(['customer', 'serviceTransactionItem'])->whereNotNull('used')->where(function($query) use ($request) {
            $query->whereDate('created_at', $request->date)->orWhereDate('used', $request->date);
        })->orderBy(DB::raw('washer_name, used'))->get())->groupBy('washer_name');

        $customerDries = collect(CustomerDry::with(['customer', 'serviceTransactionItem'])->whereNotNull('used')->where(function($query) use ($request) {
            $query->whereDate('created_at', $request->date)->orWhereDate('used', $request->date);
        })->orderBy(DB::raw('dryer_name, used'))->get())->groupBy('dryer_name');


        // $usedProducts = collect(ProductTransactionItem::with(['transaction' => function($query) {
        //     $query->select('id', 'job_order', 'customer_name');
        // }])->where('saved', true)->where(function($query) use ($request) {
        //     $query->whereDate('created_at', $request->date);
        // })->orderBy('name')->get())->groupBy('name');
        $usedProducts = collect(ProductTransactionItem::with(['transaction' => function($query) {
            $query->select('id', 'job_order', 'customer_name');
        }])->where('saved', true)->where(function($query) use ($request) {
            $query->whereDate('created_at', $request->date);
        })->selectRaw('transaction_id, name, product_id, count(*) as quantity, price, sum(price) as total')
            ->groupBy('transaction_id', 'name', 'product_id', 'price')
            ->orderBy('name')
            ->get())->groupBy('name');


        $otherServices = collect(ServiceTransactionItem::with(['transaction' => function($query) {
            $query->select('id', 'job_order', 'customer_name');
        }])->where('category', 'other')->where('saved', true)->where(function($query) use ($request) {
            $query->whereDate('created_at', $request->date);
        })->selectRaw('transaction_id, name, other_service_id, count(*) as quantity, price, sum(price) as total')
            ->groupBy('transaction_id', 'name', 'other_service_id', 'price')
            ->orderBy('name')
            ->get())->groupBy('name');

        $lagoon = collect(LagoonTransactionItem::with(['transaction' => function($query) {
            $query->select('id', 'job_order', 'customer_name');
        }])->where('saved', true)->where(function($query) use ($request) {
            $query->whereDate('created_at', $request->date);
        })->selectRaw('transaction_id, name, lagoon_id, count(*) as quantity, price, sum(price) as total')
            ->groupBy('transaction_id', 'name', 'lagoon_id', 'price')
            ->orderBy('name')
            ->get())->groupBy('name');

        $lagoonPerKilo = collect(LagoonPerKiloTransactionItem::with(['transaction' => function($query) {
            $query->select('id', 'job_order', 'customer_name');
        }])->where('saved', true)->where(function($query) use ($request) {
            $query->whereDate('created_at', $request->date);
        })->selectRaw('transaction_id, name, lagoon_per_kilo_id, sum(kilos) as quantity, price_per_kilo as price, sum(total_price) as total')
            ->groupBy('transaction_id', 'name', 'lagoon_per_kilo_id', 'price')
            ->orderBy('name')
            ->get())->groupBy('name');

        $scarpa = collect(ScarpaCleaningTransactionItem::with(['transaction' => function($query) {
                $query->select('id', 'job_order', 'customer_name');
            }])->where('saved', true)->where(function($query) use ($request) {
                $query->whereDate('created_at', $request->date);
            })->selectRaw('transaction_id, name, count(*) as quantity, price, sum(price) as total')
                ->groupBy('transaction_id', 'name', 'price')
                ->orderBy('name')
                ->get())->groupBy('name');





        $summary = [
            'customerCount' => $customers->count(),
            'jobOrdersToday' => [
                // 'created' => count($jobOrdersToday['created']),
                'paid' => [
                    'count' => $jobOrdersToday['paid']->count(),
                    'total' => $jobOrdersToday['paid']->sum('total_price'),
                    // 'discount' => $jobOrdersToday['created']->sum(function($item) {
                    //     if($item->payment && $item->payment->discount) {
                    //         return $item->payment->discount_in_peso;
                    //     }
                    // }),
                    'collections' => $jobOrdersToday['paid']->sum(function($item) {
                        $total = 0;
                        if($item->payment) {
                            $total += $item->payment->collected;
                        }
                        if($item->partialPayment && $item->payment == null) {
                            $total += $item->partialPayment->total_paid;
                        }
                        return $total;
                    }),
                    'balance' => $jobOrdersToday['paid']->sum(function($item) {
                        $total = 0;
                        if($item->partialPayment && $item->payment == null) {
                            $total += $item->partialPayment->balance;
                        }
                        return $total;
                    }),
                    'cashless' => $jobOrdersToday['paid']->sum(function($item) {
                        $total = 0;
                        if($item->payment) {
                            $total += $item->payment->cash_less_amount;
                        }
                        return $total;
                    }),
                    'points' => $jobOrdersToday['paid']->sum(function($item) {
                        $total = 0;
                        if($item->payment) {
                            $total += $item->payment->points_in_peso;
                        }
                        return $total;
                    }),
                    'rfid' => $jobOrdersToday['paid']->sum(function($item) {
                        $total = 0;
                        if($item->payment) {
                            $total += $item->payment->rfid;
                        }
                        return $total;
                    }),
                ],
                // 'unCollected' => [
                //     'count' => $jobOrdersToday['unCollected']->count(),
                //     'sum' => $jobOrdersToday['unCollected']->sum('total_price'),
                // ],
                'canceled' => [
                    'count' => $jobOrdersToday['canceled']->count(),
                    'sum' => $jobOrdersToday['canceled']->sum('total_price'),
                ]
            ],
            'jobOrdersOtherDays' => [
                'unCollected' => [
                    'count' => $jobOrdersOtherDays['unCollected']->count(),
                    'sum' => $jobOrdersOtherDays['unCollected']->sum('total_price'),
                ],
                'canceled' => [
                    'count' => $jobOrdersOtherDays['canceled']->count(),
                    'sum' => $jobOrdersOtherDays['canceled']->sum('total_price'),
                ]
            ],
            'collections' => [
                'fullyPaidJobOrders' => [
                    'cash' => null,
                    'cashless' => null,
                    'loyaltyPoints' => null,
                    'rfidCard' => null,
                    'discount' => null,
                ],
                'partiallyPaidJobOrders' => null,
                'tapCards' => null,
                'rfidLoads' => null,
            ],
            'pendingServices' => [
                'today' => [
                    'washes' => $pendingToday->sum('customer_washes_count'),
                    'dries' => $pendingToday->sum('customer_dries_count'),
                ],
                'allTime' => [
                    'washes' => $pendingAllTime->sum('customer_washes_count'),
                    'dries' => $pendingAllTime->sum('customer_dries_count'),
                ]
            ],
            'collectedJobOrders' => [
                'today' => [
                    'partialPayment' => [
                        'count' => 0,
                        'total' => 0,
                    ],
                    'cash' => [
                        'count' => 0,
                        'total' => 0,
                    ],
                    'cashless' => [
                        'count' => 0,
                        'total' => 0,
                    ],
                    'points' => [
                        'count' => 0,
                        'total' => 0,
                    ],
                ],
                'overAll' => [
                    'partialPayment' => [
                        'count' => 0,
                        'total' => 0,
                    ],
                    'cash' => [
                        'count' => 0,
                        'total' => 0,
                    ],
                    'cashless' => [
                        'count' => 0,
                        'total' => 0,
                    ],
                    'points' => [
                        'count' => 0,
                        'total' => 0,
                    ],
                ]
            ]
        ];


        return response()->json([
            'customers' => $customers,
            'jobOrdersToday' => $jobOrdersToday,
            'jobOrdersOtherDays' => $jobOrdersOtherDays,
            'pendingServices' => [
                'allTime' => $pendingAllTime,
                'today' => $pendingToday,
            ],
            'washes' => $customerWashes,
            'dries' => $customerDries,
            'usedProducts' => $usedProducts,
            'otherServices' => $otherServices,
            'lagoon' => $lagoon->merge($lagoonPerKilo),
            'scarpa' => $scarpa,
            // 'lagoonPerKilo' => $lagoonPerKilo,
            'summary' => $summary,
        ]);
    }
}
