<?php

namespace App\Http\Controllers;

use App\Customer;
use App\CustomerDry;
use App\CustomerWash;
use Illuminate\Http\Request;

class PendingServicesController extends Controller
{
    public function customers(Request $request) {
        $machineSize = $request->machineType[0] == 't' ? 'TITAN' : 'REGULAR';
        $machineType = $request->machineType[1] == 'w' ? 'customerWashes' : 'customerDries';

        $result = Customer::withCount([$machineType => function($query) use ($machineSize) {
            $query->whereNull('used')->where('machine_type', $machineSize);
        }])->whereHas($machineType, function($query) use ($machineSize) {
            $query->where('machine_type', $machineSize)->whereNull('used');
        })->where('name', 'like', "%$request->keyword%")->orderBy('name')->get();

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
}
