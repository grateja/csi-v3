<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Customer;
use App\LoyaltyPoint;
use Illuminate\Support\Facades\DB;

class CustomersController extends Controller
{
    public function index(Request $request) {

        $customers = Customer::withCount(['rfidCards',
            'customerDries' => function($query) {
                $query->whereNull('used');
            }, 'customerWashes' => function($query) {
                $query->whereNull('used');
            }
        ])->where(function($query) use ($request) {
            $query->where('name', 'like', "%$request->keyword%");
        })->orderBy('name');



        $count = Customer::where(function($query) use ($request) {
            $query->where('name', 'like', "%$request->keyword%");
        })->count();

        return response()->json([
            'result' => $customers->paginate(10),
            'summary' => [
                'total_items' => $count,
            ]
        ], 200);
    }

    public function autocomplete(Request $request) {
        $data = Customer::where(function($query) use ($request) {
            $query->where('name', 'like', "%$request->keyword%");
        })->orderBy('name')->limit(10)->get();

        return response()->json([
            'data' => $data
        ], 200);
    }

    public function checkPoints($customerId) {
        $customer = Customer::findOrFail($customerId);
        $loyaltyPoint = LoyaltyPoint::first();
        if(!$loyaltyPoint) {
            return response()->json([
                'errors' => [
                    'message' => ['Loyalty points not yet setup']
                ]
            ], 422);
        }

        return response()->json([
            'customer' => $customer,
            'pointsInPeso' => $customer->earned_points * $loyaltyPoint->amount_in_peso,
        ]);
    }

    public function store(Request $request) {
        $rules = [
            'name' => 'required',
        ];

        $customer = Customer::where('name', $request->name)->first();
        if($customer != null) {
            return response()->json([
                'errors' => [
                    'name' => ['Customer name already exist']
                ]
            ], 422);
        }

        if($request->validate($rules)) {
            return DB::transaction(function () use ($request) {
                $customer = Customer::create([
                    'name' => $request->name,
                    'address' => $request->address,
                    'contact_number' => $request->contactNumber,
                    'email' => $request->email,
                    'first_visit' => $request->firstVisit,
                ]);

                $this->dispatch($customer->queSynch());

                return response()->json([
                    'customer' => $customer
                ], 200);
            });
        }
    }

    public function update($customerId, Request $request) {
        $customer = Customer::findOrFail($customerId);

        $rules = [
            'name' => 'required',
            'date' => 'nullable|date'
        ];

        if($customer->name != $request->name) {
            $rules['name'] = 'required|unique:customers';
        }

        if($request->validate($rules)) {
            return DB::transaction(function () use ($customer, $request) {
                $customer->update([
                    'name' => $request->name,
                    'address' => $request->address,
                    'contact_number' => $request->contactNumber,
                    'email' => $request->email,
                    'first_visit' => $request->firstVisit,
                ]);

                $this->dispatch($customer->queSynch());

                return response()->json([
                    'customer' => $customer,
                ], 200);
            });
        }
    }

    public function deleteCustomer($customerId) {
        $customer = Customer::findOrFail($customerId);
        if($customer->delete()) {
            $this->dispatch($customer->queSynch());
            return response()->json([
                'customer' => $customer,
            ]);
        }
    }
}
