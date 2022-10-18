<?php

namespace App\Http\Controllers;

use App\Client;
use Illuminate\Http\Request;
use App\Customer;
use App\LoyaltyPoint;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client as GuzzleHttpClient;

class CustomersController extends Controller
{
    public function index(Request $request) {
        $sortBy = Customer::filterKeys($request->sortBy);
        $order = $request->orderBy ? $request->orderBy : 'desc';

        $customers = Customer::withCount(['rfidCards',
            'customerDries' => function($query) {
                //$query->whereNotNull('used');
            }, 'customerWashes' => function($query) {
                //$query->whereNotNull('used');
            }
        ])->where(function($query) use ($request) {
            $query->where('name', 'like', "%$request->keyword%")
                ->orWhere('crn', 'like', "%$request->keyword%")
                ->orWhere('remarks', 'like', "%$request->keyword%");
        });

        $customers = $customers->orderBy(DB::raw($sortBy), $order);
        // $customers = $customers->orderBy('customer_washes_count');

        // $count = Customer::where(function($query) use ($request) {
        //     $query->where('name', 'like', "%$request->keyword%");
        // })->count();

        return response()->json([
            'result' => $customers->paginate(10),
            'summary' => [
                // 'total_items' => $count,
            ]
        ], 200);
    }

    public function show($customerId) {
        $customer = Customer::findOrFail($customerId);
        return response()->json([
            'customer' => $customer,
        ]);
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
            'name' => 'required|unique:customers,name,NULL,id,deleted_at,NULL',
            'crn' => 'required|unique:customers|digits:5',
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
                    'crn' => $request->crn,
                    'remarks' => $request->remarks,
                    'address' => $request->address,
                    'contact_number' => $request->contactNumber,
                    'email' => $request->email,
                    'first_visit' => $request->birthday,
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
//            'name' => 'required|unique:customers,name,NULL,id,deleted_at,NULL',
            'name' => 'required',
            'date' => 'nullable|date'
        ];

        if($customer->name != $request->name) {
            $rules['name'] = 'required|unique:customers';
        }
        if($customer->crn != $request->crn) {
            $rules['crn'] = 'required|unique:customers|digits:4';
        }


        if($request->validate($rules)) {
            return DB::transaction(function () use ($customer, $request) {
                $customer->update([
                    'crn' => $request->crn,
                    'remarks' => $request->remarks,
                    'name' => $request->name,
                    'address' => $request->address,
                    'contact_number' => $request->contactNumber,
                    'email' => $request->email,
                    'first_visit' => $request->birthday,
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

    public function getCRN() {
        $customer = Customer::withTrashed()->orderByDesc('created_at')->first();
        if($customer) {
            $num = (int) $customer->crn;
            return str_pad(++$num, 5, '0', STR_PAD_LEFT);
        } else {
            return '00001';
        }
    }

    public function preRegistered(Request $request) {
        $clientRequest = new GuzzleHttpClient();
        $shopId = Client::first()->user_id;
        $url = env('CLOUD_URL', '139.162.73.87');

        $response = $clientRequest->get('http://'.$url.'/api/auto-complete/pre-registered/' . $shopId . '?keyword=' . $request->keyword, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ],
            'http_errors' => false
        ]);
        return $response->getBody()->getContents();
    }
}
