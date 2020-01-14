<?php

namespace App\Http\Controllers;

use App\Customer;
use App\CustomerDry;
use App\CustomerWash;
use Illuminate\Http\Request;
use App\Machine;
use App\MachineRemarks;
use App\MachineUsage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class MachinesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $result = [
            'washers' => Machine::where(['machine_type' => 'rw'])->orderBy('machine_name')->get(),
            'dryers' => Machine::where(['machine_type' => 'rd'])->orderBy('machine_name')->get(),
            'titan_washers' => Machine::where(['machine_type' => 'tw'])->orderBy('machine_name')->get(),
            'titan_dryers' => Machine::where(['machine_type' => 'td'])->orderBy('machine_name')->get(),
        ];

        return response()->json([
            'result' => $result,
        ], 200);
    }

    public function lastActivated() {
        $machine = Machine::latest('updated_at')->first();
        return response()->json([
            'machine' => $machine,
        ], 200);
    }

    public function activate(Request $request) {
        // customer, machineSize, serviceType
        return DB::transaction(function () use ($request) {
            $customerWash = null;
            $customerDry = null;
            $totalMinutes = 0;
            $customer = Customer::findOrFail($request->customerId);
            $machine = Machine::findOrFail($request->machineId);
            $pulse = 0;

            if($request->serviceType == 'washing') {
                $customerWash = CustomerWash::where([
                    'service_name' => $request->serviceName,
                    'machine_type' => $request->machineSize,
                    'used' => null,
                ])->first();
                $totalMinutes = $customerWash->minutes;
                $pulse = $customerWash->pulse;

                $customerWash->update([
                    'washer_name' => $machine->machine_name,
                    'used' => Carbon::now(),
                    'user_id' => auth('api')->id(),
                ]);

            } else if($request->serviceType == 'drying') {
                $customerDry = CustomerDry::where([
                    'service_name' => $request->serviceName,
                    'machine_type' => $request->machineSize,
                    'used' => null,
                ])->first();
                $totalMinutes = $customerDry->minutes;
                $pulse = $customerDry->pulse;

                $customerDry->update([
                    'dryer_name' => $customerDry->machine_name,
                    'used' => Carbon::now(),
                    'user_id' => auth('api')->id(),
                ]);
            }

            $machine->update([
                'time_activated' => Carbon::now(),
                'total_minutes' => $totalMinutes,
                'customer_name' => $customer->name,
            ]);

            MachineUsage::create([
                'machine_id' => $machine->id,
                'customer_name' => $customer->name,
                'minutes' => $totalMinutes,
            ]);

            $output = $machine->remoteActivate($pulse);

            if($output) {
                return response()->json([
                    'machine' => $machine,
                ]);
            } else {

                DB::rollback();

                return response()->json([
                    'errors' => [
                        'message' => ['Cannot activate machine'],
                        'machine' => $machine,
                        'output' => $output,
                    ]
                ], 422);
            }
        });
    }

    public function forceStop(Request $request) {
        $rules = [
            'remarks' => 'required',
            'machineId' => 'required',
        ];

        if($request->validate($rules)) {
            return DB::transaction(function () use ($request) {
                $machine = Machine::findOrFail($request->machineId);
                $machineRemarks = MachineRemarks::create([
                    'remarks' => $request->remarks,
                    'user_id' => auth('api')->id(),
                    'remaining_time' => $machine->remainingTime(),
                    'machine_id' => $machine->id,
                ]);

                $machine->update([
                    'total_minutes' => 0,
                    'customer_name' => $machine->customer_name . '(Force Stopped)'
                ]);

                return response()->json([
                    'machine' => $machine,
                ]);
            });
        }
    }

    public function reset() {
        Machine::where('total_minutes', '>', 0)->update([
            'total_minutes' => 0,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
