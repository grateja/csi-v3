<?php

namespace App\Http\Controllers;

use App\Client;
use App\Customer;
use App\CustomerDry;
use App\CustomerWash;
use App\Discount;
use App\DryingService;
use App\Expense;
use App\FullService;
use App\FullServiceItem;
use App\FullServiceProduct;
use App\JobOrderFormat;
use App\Jobs\SendShopPreferences;
use App\LoyaltyPoint;
use App\Machine;
use App\MachineRemarks;
use App\MachineUsage;
use App\OtherService;
use App\Product;
use App\ProductPurchase;
use App\ProductTransactionItem;
use App\RfidCard;
use App\RfidCardTransaction;
use App\RfidLoadTransaction;
use App\ServiceTransactionItem;
use App\Transaction;
use App\TransactionPayment;
use App\TransactionRemarks;
use App\UnregisteredCard;
use App\User;
use App\WashingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ClientsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = User::whereHas('roles', function($query) {
            $query->where('name', 'admin');
        })->first();
        $client = Client::first();
        $machines = Machine::all();

        return response()->json([
            'client' => $client,
            'user' => $user,
            'machines' => $machines,
        ]);
    }

    public function unsynch() {
        return DB::transaction(function () {
            TransactionRemarks::whereNotNull('synched')->update(['synched' => null]);
            RfidCardTransaction::whereNotNull('synched')->update(['synched' => null]);

            MachineRemarks::whereNotNull('synched')->update(['synched' => null]);
            MachineUsage::whereNotNull('synched')->update(['synched' => null]);

            ProductTransactionItem::whereNotNull('synched')->update(['synched' => null]);
            ServiceTransactionItem::whereNotNull('synched')->update(['synched' => null]);
            CustomerDry::whereNotNull('synched')->update(['synched' => null]);
            CustomerWash::whereNotNull('synched')->update(['synched' => null]);
            Expense::whereNotNull('synched')->update(['synched' => null]);
            RfidLoadTransaction::whereNotNull('synched')->update(['synched' => null]);
            RfidCard::whereNotNull('synched')->update(['synched' => null]);
            TransactionPayment::whereNotNull('synched')->update(['synched' => null]);
            Transaction::whereNotNull('synched')->update(['synched' => null]);
            Machine::whereNotNull('synched')->update(['synched' => null]);
            Customer::whereNotNull('synched')->update(['synched' => null]);

            ProductPurchase::whereNotNull('synched')->update(['synched' => null]);
            Product::whereNotNull('synched')->update(['synched' => null]);
        });
    }

    public function reset() {
        return DB::transaction(function () {
            TransactionRemarks::where(function($query){})->forceDelete();
            UnregisteredCard::where(function($query){})->forceDelete();
            RfidCardTransaction::where(function($query){})->forceDelete();

            MachineRemarks::where(function($query){})->forceDelete();
            MachineUsage::where(function($query){})->forceDelete();

            ProductTransactionItem::where(function($query){})->forceDelete();
            FullServiceProduct::where(function($query){})->forceDelete();
            ServiceTransactionItem::where(function($query){})->forceDelete();
            FullServiceItem::where(function($query){})->forceDelete();
            FullService::where(function($query){})->forceDelete();
            OtherService::where(function($query){})->forceDelete();
            DryingService::where(function($query){})->forceDelete();
            CustomerDry::where(function($query){})->forceDelete();
            CustomerWash::where(function($query){})->forceDelete();
            WashingService::where(function($query){})->forceDelete();
            Client::where(function($query){})->forceDelete();
            JobOrderFormat::where(function($query){})->forceDelete();
            LoyaltyPoint::where(function($query){})->forceDelete();
            Discount::where(function($query){})->forceDelete();
            Expense::where(function($query){})->forceDelete();
            RfidLoadTransaction::where(function($query){})->forceDelete();
            RfidCard::where(function($query){})->forceDelete();
            TransactionPayment::where(function($query){})->forceDelete();
            Transaction::where(function($query){})->forceDelete();
            Machine::where(function($query){})->forceDelete();
            Customer::where(function($query){})->forceDelete();

            ProductPurchase::where(function($query){})->forceDelete();
            Product::where(function($query){})->forceDelete();

            User::whereDoesntHave('roles', function($query) {
                $query->where('name', 'developer');
            })->forceDelete();
        });
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createUser(Request $request)
    {
        $rules = [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:4|confirmed',
        ];

        if($request->validate($rules)) {
            return DB::transaction(function () use ($request) {
                $userId = $request->id ? $request->id : Str::uuid();
                $user = User::create([
                    'id' => $userId,
                    'name' => $request->name,
                    'email' => $request->email,
                    'contact_number' => $request->contactNumber,
                    'password' => bcrypt($request->password),
                ]);

                $user->assignRole(2);

                return response()->json([
                    'user' => $user
                ], 200);
            });
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateUser(Request $request, $userId)
    {
        $rules = [
            'name' => 'required',
        ];

        $user = User::findOrFail($userId);
        if($user->email != $request->email) {
            $rules['email'] = 'required|email|unique:users';
        }

        if($request->validate($rules)) {
            return DB::transaction(function () use ($user, $request) {
                $user->update([
                    'id' => $request->id,
                    'name' => $request->name,
                    'email' => $request->email,
                    'contact_number' => $request->contactNumber,
                ]);

                return response()->json([
                    'user' => $user,
                ]);
            });
        }
    }

    public function setUpClient(Request $request) {
        $rules = [
            'shopName' => 'required',
        ];


        if($request->validate($rules)) {
            return DB::transaction(function () use ($request) {
                $client = Client::first();

                if($client == null) {
                    $user = User::whereHas('roles', function($query) {
                        $query->where('name', 'admin');
                    })->first();

                    if($user == null) {
                        return response()->json([
                            'errors' => [
                                'message' => ['No admin set up']
                            ]
                        ], 422);
                    }

                    $client = Client::create([
                        'user_id' => $user->id,
                        'shop_name' => $request->shopName,
                        'shop_email' => $request->shopEmail,
                        'shop_number' => $request->shopNumber,
                        'address' => $request->shopAddress,
                    ]);
                } else {
                    $client->update([
                        'shop_name' => $request->shopName,
                        'shop_email' => $request->shopEmail,
                        'shop_number' => $request->shopNumber,
                        'address' => $request->shopAddress,
                    ]);
                }

                $this->dispatch((new SendShopPreferences())->delay(5));

                return response()->json([
                    'client' => $client,
                ]);
            });
        }
    }

    public function setUpMachines(Request $request) {
        return DB::transaction(function () use ($request) {
            for ($i=1; $i <= $request->washerCount; $i++) {
                Machine::create([
                    'ip_address' => $request->gateWay . '.' . ($request->wStartIp + $i - 1),
                    'machine_type' => 'rw',
                    'machine_name' => 'Washer ' . $i,
                    'initial_time' => $request->wInitialTime,
                    'additional_time' => $request->wAdditionalTime,
                    'initial_price' => $request->wInitialPrice,
                    'additional_price' => $request->wAdditionalPrice,
                ]);
            }
            for ($i=1; $i <= $request->dryerCount; $i++) {
                Machine::create([
                    'ip_address' => $request->gateWay . '.' . ($request->dStartIp + $i - 1),
                    'machine_type' => 'rd',
                    'machine_name' => 'Dryer ' . $i,
                    'initial_time' => $request->dInitialTime,
                    'additional_time' => $request->dAdditionalTime,
                    'initial_price' => $request->dInitialPrice,
                    'additional_price' => $request->dAdditionalPrice,
                ]);
            }
            for ($i=1; $i <= $request->twasherCount; $i++) {
                Machine::create([
                    'ip_address' => $request->gateWay . '.' . ($request->twStartIp + $i - 1),
                    'machine_type' => 'tw',
                    'machine_name' => 'Titan Washer ' . $i,
                    'initial_time' => $request->twInitialTime,
                    'additional_time' => $request->twAdditionalTime,
                    'initial_price' => $request->twInitialPrice,
                    'additional_price' => $request->twAdditionalPrice,
                ]);
            }
            for ($i=1; $i <= $request->tdryerCount; $i++) {
                Machine::create([
                    'ip_address' => $request->gateWay . '.' . ($request->tdStartIp + $i - 1),
                    'machine_type' => 'td',
                    'machine_name' => 'Titan Dryer ' . $i,
                    'initial_time' => $request->tdInitialTime,
                    'additional_time' => $request->tdAdditionalTime,
                    'initial_price' => $request->tdInitialPrice,
                    'additional_price' => $request->tdAdditionalPrice,
                ]);
            }

            return response()->json([
                'machines' => Machine::all(),
            ]);
        });
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($shopId)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $client = Client::firstOrFail();
        return response()->json([
            'client' => $client,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
