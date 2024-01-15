<?php

namespace App\Http\Controllers;

use App\Customer;
use App\CustomerDry;
use App\CustomerWash;
use App\Transaction;
use Illuminate\Http\Request;

class PendingServicesController extends Controller
{
    public function customers(Request $request) {
        $machineSize = $request->machineType[0] == 't' ? 'TITAN' : 'REGULAR';
        $machineType = $request->machineType[1] == 'w' ? 'customerWashes' : 'customerDries';

        if($model = $request->model) {
            $result = Customer::withCount(['eluxTokens' => function($query) use ($model, $request) {
                $query->whereNull('used')->where('model', $model);
            }])->whereHas('eluxTokens', function($query) use ($model) {
                $query->where('model', $model);
            })->where('name', 'like', "%$request->keyword%")->orderBy('name')->get();
        } else {
            $result = Customer::withCount([$machineType => function($query) use ($machineSize) {
                $query->whereNull('used')->where('machine_type', $machineSize);
            }])->whereHas($machineType, function($query) use ($machineSize) {
                $query->where('machine_type', $machineSize)->whereNull('used');
            })->where('name', 'like', "%$request->keyword%")->orderBy('name')->get();
        }

        return response()->json([
            'result' => $result,
        ]);
    }

    public function washingServices(Request $request) {
        $result = CustomerWash::where(function($query) use ($request) {
            $query->where('customer_id', $request->customerId)->where('machine_type', $request->machineSize)->whereNull('used');
        })->groupBy('service_name', 'customer_id', 'machine_type', 'minutes')->selectRaw('COUNT(service_name) as total_available, minutes, service_name, customer_id, machine_type')->get();

        return response()->json([
            'result' => $result,
        ]);
    }

    public function dryingServices(Request $request) {
        $result = CustomerDry::where(function($query) use ($request) {
            $query->where('customer_id', $request->customerId)->where('machine_type', $request->machineSize)->whereNull('used');
        })->groupBy('service_name', 'customer_id', 'machine_type', 'minutes')->selectRaw('COUNT(service_name) as total_available, minutes, service_name, customer_id, machine_type')->get();

        return response()->json([
            'result' => $result,
        ]);
    }

    public function index(Request $request) {

        $result = Transaction::withCount(['customerDries' => function($query) {
            $query->whereNull('used');
        }, 'customerWashes' => function($query) {
            $query->whereNull('used');
        }])
            ->whereHas('customerDries', function($query) {
                $query->whereNull('used');
            })->orWhereHas('customerWashes', function($query) {
                $query->whereNull('used');
            })->orderBy('job_order');

        // $result = Customer::whereHas('customerWashes', function($query) {
        //     $query->whereNull('used');//->whereHas('serviceTransactionItem');
        // })->orWhereHas('customerDries', function($query) {
        //     $query->whereNull('used');//->whereHas('serviceTransactionItem');
        // })->join('customer_washes', 'customer_washes.customer_id', '=', 'customers.id');

        // $result = Customer::withCount([
        //     'customerWashes' => function($query) {
        //         $query->whereNull('used')->whereHas('serviceTransactionItem');
        //     },
        //     'customerDries' => function($query) {
        //         $query->whereNull('used')->whereHas('serviceTransactionItem');
        //     }
        // ])->whereHas('customerWashes', function($query) {
        //     $query->whereNull('used');
        // })->orWhereHas('customerDries', function($query) {
        //     $query->whereNull('used');
        // })->orderBy('name');

        return response()->json([
            'result' => $result->paginate(10),
        ]);
    }

    public function viewAll($customerId) {
        $customerWashes = CustomerWash::with('serviceTransactionItem.transaction')
            ->where('customer_id', $customerId)
            ->whereHas('serviceTransactionItem')
            ->whereNull('used')->get();

        $customerDries = CustomerDry::with('serviceTransactionItem.transaction')
            ->where('customer_id', $customerId)
            ->whereHas('serviceTransactionItem')
            ->whereNull('used')->get();

        $customerWashes = $customerWashes->transform(function($item) {
            return [
                'id' => $item->id,
                'service_name' => $item->service_name,
                'machine_size' => $item->machine_type,
                'minutes' => $item->minutes,
                'created_at' => $item->created_at,
                'job_order' => $item->serviceTransactionItem->transaction->job_order,
                'transaction_id' => $item->serviceTransactionItem->transaction_id,
            ];
        });

        $customerDries = $customerDries->transform(function($item) {
            return [
                'id' => $item->id,
                'service_name' => $item->service_name,
                'machine_size' => $item->machine_type,
                'minutes' => $item->minutes,
                'created_at' => $item->created_at,
                'job_order' => $item->serviceTransactionItem->transaction->job_order,
                'transaction_id' => $item->serviceTransactionItem->transaction_id,
            ];
        });

        return response()->json([
            'customerWashes' => $customerWashes,
            'customerDries' => $customerDries,
        ]);
    }

    public function disposeService($serviceType, $serviceId) {
        if($serviceType == 'washing') {
            $service = CustomerWash::findOrFail($serviceId);
        } else if($serviceType == 'drying') {
            $service = CustomerDry::findOrFail($serviceId);
        } else {
            return response()->json([
                'errors' => [
                    'message' => ['Invalid service type']
                ]
            ], 422);
        }

        if($service) {
            $service->delete();
            $this->dispatch($service->queSynch());

            return response()->json([
                'service' => $service,
            ]);
        }
    }
}
