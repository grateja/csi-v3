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
            'washers' => Machine::with('customer')->where(['machine_type' => 'rw'])->orderBy('machine_name')->get(),
            'dryers' => Machine::with('customer')->where(['machine_type' => 'rd'])->orderBy('machine_name')->get(),
            'titan_washers' => Machine::with('customer')->where(['machine_type' => 'tw'])->orderBy('machine_name')->get(),
            'titan_dryers' => Machine::with('customer')->where(['machine_type' => 'td'])->orderBy('machine_name')->get(),
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
            $avWash = null;
            $totalMinutes = 0;
            $customer = Customer::findOrFail($request->customerId);
            $machine = Machine::findOrFail($request->machineId);
            $pulse = 0;

            if($request->serviceType == 'washing') {
                if($machine->is_running) {
                    return response()->json([
                        'errors' => [
                            'message' => ['Machine is already running']
                        ]
                    ], 422);
                }

                $customerWash = CustomerWash::where([
                    'service_name' => $request->serviceName,
                    'machine_type' => $request->machineSize,
                    'used' => null,
                    'customer_id' => $request->customerId,
                ])->first();
                $totalMinutes = $customerWash->minutes;
                $pulse = $customerWash->pulse_count;

                $customerWash->update([
                    'washer_name' => $machine->machine_name,
                    'used' => Carbon::now()->toDateTimeString(),
                    'staff_name' => auth('api')->user()->name,
                ]);

                $avWash = $customerWash;

            } else if($request->serviceType == 'drying') {
                if($machine->is_running && $customer->name != $machine->user_name) {
                    return response()->json([
                        'errors' => [
                            'message' => ['Machine is already running']
                        ]
                    ], 422);
                }

                $customerDry = CustomerDry::where([
                    'service_name' => $request->serviceName,
                    'machine_type' => $request->machineSize,
                    'used' => null,
                    'customer_id' => $request->customerId,
                ])->first();
                $totalMinutes = $customerDry->minutes;
                $pulse = $customerDry->pulse_count;

                $customerDry->update([
                    'dryer_name' => $machine->machine_name,
                    'used' => Carbon::now()->toDateTimeString(),
                    'staff_name' => auth('api')->user()->name,
                ]);

                $avWash = $customerDry;

            } else {
                return response()->json([
                    'errors' => [
                        'message' => ['Invalid Service type']
                    ]
                ], 422);
            }

            if($request->additional && $machine->is_running) {
                $machine->update([
                    'total_minutes' => DB::raw('total_minutes+'. $totalMinutes),
                    'remarks' => 'Additional ' . $totalMinutes,
                    'user_name' => $customer->name,
                    'customer_id' => $customer->id,
                    'customer_wash_id' => $customerWash ? $customerWash->id : null,
                    'customer_dry_id' => $customerDry ? $customerDry->id : null,
                ]);

                $machineUsage = MachineUsage::where('machine_id', $machine->id)->orderByDesc('updated_at')->first();
                $machineUsage->update([
                    'total_minutes' => DB::raw('total_minutes+', $totalMinutes),
                    'price' => DB::raw('price+' . $request->serviceType == 'washing' ? $customerWash->price : $customerDry->price),
                ]);

            } else {
                $machine->update([
                    'time_activated' => Carbon::now(),
                    'total_minutes' => $totalMinutes,
                    'remarks' => 'Remotely activated',
                    'user_name' => $customer->name,
                    'customer_id' => $customer->id,
                    'customer_wash_id' => $customerWash ? $customerWash->id : null,
                    'customer_dry_id' => $customerDry ? $customerDry->id : null,
                ]);

                $machineUsage = MachineUsage::create([
                    'machine_id' => $machine->id,
                    'customer_name' => $customer->name,
                    'minutes' => $totalMinutes,
                    'activation_type' => 'remote',
                    'price' => $request->serviceType == 'washing' ? $customerWash->price : $customerDry->price,
                ]);
            }

            // $output = $machine->remoteActivate($pulse);
            $url = "$machine->ip_address/activate?pulse=$pulse&token=$avWash->id";
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_TIMEOUT, 35);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            $output = curl_exec($curl);
            curl_close($curl);

            if($output) {
                // DB::rollBack();

                $this->dispatch($machine->queSynch());
                $this->dispatch($machineUsage->queSynch());
                $this->dispatch($avWash->queSynch());


                return response()->json([
                    'machine' => $machine->fresh('customer'),
                    'customerWash' => $customerWash,
                    'customerDry' => $customerDry,
                    'pulse' => $pulse,
                    'output' => $output,
                ]);
            } else {

                DB::rollback();

                return response()->json([
                    'errors' => [
                        'message' => ['Cannot connect to ' . $machine->machine_name . ' (' . $machine->ip_address . ')'],
                        'machine' => $machine,
                        'output' => $output,
                    ]
                ], 422);
            }
        });
    }

    public function remarks(Request $request) {
        $result = MachineRemarks::with('machine', 'user')
            ->where('title', 'like' , "%$request->keyword%")
            ->orWhere('remarks', 'like', "%$request->keyword%")
            ->orWhereHas('machine', function($query) use ($request) {
                $query->where('machine_name', 'like', "%$request->keyword%");
            })->orderByDesc('created_at');

        return response()->json([
            'result' => $result->paginate(10),
        ]);
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
                    'title' => 'Force stopped',
                    'remarks' => $request->remarks,
                    'user_id' => auth('api')->id(),
                    'remaining_time' => $machine->remainingTime(),
                    'machine_id' => $machine->id,
                ]);

                $machine->update([
                    'total_minutes' => 0,
                    'remarks' => $machine->user_name . '(Force Stopped)',
                    'customer_id' => null,
                ]);

                $this->dispatch($machine->queSynch());
                $this->dispatch($machineRemarks->queSynch());

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

    public function viewByType($machineType, Request $request) {
        $result = Machine::withCount([
            'machineUsages as usage_today' => function($query) use ($request) {
                $query->whereDate('created_at', $request->date);
            },
            'totalUsage as total_usage'
        ])->where('machine_type', $machineType)->orderBy('machine_name')->get();
        return response()->json([
            'result' => $result,
        ]);
    }

    public function history($machineId, Request $request) {
        $result = MachineUsage::where('machine_id', $machineId)
            ->whereDate('created_at', $request->date)
            ->orderByDesc('created_at')
            ->get();
        return response()->json([
            'result' => $result,
        ]);
    }

    public function updateSettings($machineId, Request $request) {
        $rules = [
            'initialPrice' => 'required|numeric',
            'additionalPrice' => 'numeric',
            'initialTime' => 'required|numeric',
            'additionalTime' => 'numeric',
        ];

        if($request->validate($rules)) {
            return DB::transaction(function () use ($request, $machineId) {
                $result = null;
                $machine = Machine::findOrFail($machineId);
                if($request->applyToAll) {
                    $machines = Machine::where('machine_type', $machine->machine_type);
                    $machines->update([
                        'initial_price' => $request->initialPrice,
                        'additional_price' => $request->additionalPrice,
                        'initial_time' => $request->initialTime,
                        'additional_time' => $request->additionalTime,
                    ]);
                    $result = $machines->get();
                    foreach ($result as $_machine) {
                        $this->dispatch($_machine->queSynch());
                    }
                } else {
                    $machine->update([
                        'initial_price' => $request->initialPrice,
                        'additional_price' => $request->additionalPrice,
                        'initial_time' => $request->initialTime,
                        'additional_time' => $request->additionalTime,
                    ]);
                    $result = $machine;
                    $this->dispatch($machine->queSynch());
                }

                return response()->json([
                    'result' => $result,
                ]);
            });
        }
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
