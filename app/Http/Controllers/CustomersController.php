<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Customer;
use Illuminate\Support\Facades\DB;

class CustomersController extends Controller
{
    public function index(Request $request) {

        $customers = Customer::withCount('rfidCards')->where(function($query) use ($request) {
            $query->where('name', 'like', "%$request->keyword%");
        });

        return response()->json([
            'result' => $customers->paginate(20)
        ], 200);
    }

    public function autocomplete(Request $request) {
        $data = Customer::where(function($query) use ($request) {
            $query->where('name', 'like', "%$request->keyword%");
        })->limit(10)->get();

        return response()->json([
            'data' => $data
        ], 200);
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
                    'birthday' => $request->birthday,
                ]);

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

        if($request->validate($rules)) {
            return DB::transaction(function () use ($customer, $request) {
                $customer->update([
                    'name' => $request->name,
                    'address' => $request->address,
                    'contact_number' => $request->contactNumber,
                    'email' => $request->email,
                    'birthday' => $request->birthday,
                ]);

                return response()->json([
                    'customer' => $customer,
                ], 200);
            });
        }
    }
}
