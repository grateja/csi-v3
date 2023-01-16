<?php

namespace App\Http\Controllers;

use App\CustomerDry;
use App\CustomerWash;
use App\Machine;
use App\MachineUsage;
use App\Rework;
use App\ServiceTransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReworksController extends Controller
{
    public function index(Request $request) {
        $sortBy = Rework::filterKeys($request->sortBy);
        $order = $request->orderBy ? $request->orderBy : 'desc';
        $reworks = Rework::where(function($query) use ($request) {
            $query->where(DB::raw('reworks.job_order'), 'like', "%$request->keyword%");
        })->join('machines', 'machines.id', '=', 'reworks.machine_id')
            ->join('transactions', 'transactions.job_order', '=', 'reworks.job_order')
            ->selectRaw('transactions.id as transaction_id, reworks.id, reworks.remarks, reworks.customer_name, tag, reworks.job_order, account_name, reworks.created_at, machine_name');

        if($request->date) {
            $reworks = $reworks->whereDate('reworks.created_at', $request->date);
        }

        $reworks = $reworks->orderBy($sortBy, $order);

        return response()->json([
            'result' => $reworks->paginate(10),
        ]);
    }

    public function rework($machineId, Request $request) {
        $rules = [
            'remarks' => 'required',
        ];
        if($request->validate($rules)) {
            $machine = Machine::findOrFail($machineId);

            $customerWash = null;
            $customerDry = null;

            if($machine->machine_type[1] == 'w') {
                $customerWash = CustomerWash::with('customer')->find($machine->customer_wash_id);
                if(!$customerWash) {
                    return response()->json([
                        'errors' => [
                            'message' => ['Customer wash not available or deleted']
                        ]
                    ], 422);
                }
            } else if($machine->machine_type[1] == 'd') {
                $customerDry = CustomerDry::with('customer')->find($machine->customer_dry_id);
                if(!$customerDry) {
                    return response()->json([
                        'errors' => [
                            'message' => ['Customer dry not available or deleted']
                        ]
                    ], 422);
                }
            }

            if(!Carbon::createFromDate($machine->time_activated)->isToday()) {
                return response()->json([
                    'errors' => [
                        'message' => ['Cannot rework if it was not from today']
                    ]
                ], 422);
            }

            if($customerDry != null) {
                return $this->redry($machine, $customerDry, $request->remarks);
            } else if($customerWash != null) {
                return $this->rewash($machine, $customerWash, $request->remarks);
            }
        }
    }

    private function rewash($machine, $customerWash, $remarks) {
        return DB::transaction(function () use ($machine, $customerWash, $remarks) {

            $reToken = Str::random(5);

            $url = "$machine->ip_address/activate?pulse=$customerWash->pulse_count&token=$customerWash->id" . $reToken;
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_TIMEOUT, 35);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            $output = curl_exec($curl);
            curl_close($curl);

            if($output) {
                $customerWash->update([
                    'washer_name' => $machine->machine_name,
                    'used' => Carbon::now()->toDateTimeString(),
                    'staff_name' => auth('api')->user()->name,
                ]);
                $machine->update([
                    'total_minutes' => $customerWash->minutes,
                    'remarks' => "Rewash ($remarks)",
                    'time_activated' => Carbon::now(),
                ]);
                $machineUsage = MachineUsage::create([
                    'machine_id' => $machine->id,
                    'customer_name' => $customerWash->customer->name,
                    'minutes' => $customerWash->minutes,
                    'activation_type' => 'remote',
                    'price' => $customerWash->price, //$request->serviceType == 'washing' ? $customerWash->price : $customerDry->price,
                    'remarks' => "Rewash ($remarks)",
                ]);
                $rework = Rework::create([
                    'remarks' => "Rewash ($remarks)",
                    'customer_name' => $customerWash->customer->name,
                    'job_order' => $customerWash->job_order,
                    'account_name' => auth('api')->user()->name,
                    'tag' => 'reload',
                    'machine_id' => $machine->id,
                ]);

                $this->dispatch($machine->queSynch());
                $this->dispatch($machineUsage->queSynch());
                $this->dispatch($customerWash->queSynch());
                $this->dispatch($rework->queSynch());

                return response()->json([
                    'machine' => $machine->fresh('customer'),
                    'customerWash' => $customerWash,
                ]);
            }

            DB::rollback();

            return response()->json([
                'errors' => [
                    'message' => ['Cannot connect to ' . $machine->machine_name . ' (' . $machine->ip_address . ')'],
                ]
            ], 422);
        });
    }

    private function redry($machine, $customerDry, $remarks) {
        return DB::transaction(function () use ($machine, $customerDry, $remarks) {
            $reToken = Str::random(5);

            $url = "$machine->ip_address/activate?pulse=$customerDry->pulse_count&token=$customerDry->id" . $reToken;
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_TIMEOUT, 35);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            $output = curl_exec($curl);
            curl_close($curl);

            if($output) {
                $customerDry->update([
                    'dryer_name' => $machine->machine_name,
                    'used' => Carbon::now()->toDateTimeString(),
                    'staff_name' => auth('api')->user()->name,
                ]);
                $machine->update([
                    'total_minutes' => $customerDry->minutes,
                    'remarks' => "Redry ($remarks)",
                    'time_activated' => Carbon::now(),
                ]);
                $machineUsage = MachineUsage::create([
                    'machine_id' => $machine->id,
                    'customer_name' => $customerDry->customer->name,
                    'minutes' => $customerDry->minutes,
                    'activation_type' => 'remote',
                    'price' => $customerDry->price, //$request->serviceType == 'washing' ? $customerWash->price : $customerDry->price,
                    'remarks' => "Redry ($remarks)",
                ]);
                $rework = Rework::create([
                    'remarks' => "Redry ($remarks)",
                    'customer_name' => $customerDry->customer->name,
                    'job_order' => $customerDry->job_order,
                    'account_name' => auth('api')->user()->name,
                    'tag' => 'reload',
                    'machine_id' => $machine->id,
                ]);

                $this->dispatch($machine->queSynch());
                $this->dispatch($machineUsage->queSynch());
                $this->dispatch($customerDry->queSynch());
                $this->dispatch($rework->queSynch());

                return response()->json([
                    'machine' => $machine->fresh('customer'),
                    'customerDry' => $customerDry,
                ]);
            }

            DB::rollback();

            return response()->json([
                'errors' => [
                    'message' => ['Cannot connect to ' . $machine->machine_name . ' (' . $machine->ip_address . ')'],
                ]
            ], 422);
        });
    }

    public function customerWash($customerWashId) {
        $customerWash = CustomerWash::with('customer')->find($customerWashId);
        if($customerWash) {
            return response()->json([
                'result' => $customerWash
            ]);
        } else {
            return response()->json([
                'errors' => [
                    'message' => ['Customer wash not available or deleted']
                ]
            ], 422);
        }
    }

    public function customerDry($customerDryId) {
        $customerDry = CustomerDry::with('customer')->find($customerDryId);
        if($customerDry) {
            return response()->json([
                'result' => $customerDry
            ]);
        } else {
            return response()->json([
                'errors' => [
                    'message' => ['Customer dry not available or deleted']
                ]
            ], 422);
        }
    }

    public function transfer(Request $request, $from, $to) {
        $rules = [
            'remarks' => 'required',
        ];

        if($request->validate($rules)) {
            return DB::transaction(function () use ($request, $from, $to) {
                $transferFrom = Machine::findOrFail($from);
                $transferTo = Machine::findOrFail($to);

                if(!$transferFrom->is_running) {
                    return response()->json([
                        'errors' => [
                            'message' => ['Cannot transfer. Machine already stopped.']
                        ]
                    ], 422);
                }

                if(!Carbon::createFromDate($transferFrom->time_activated)->isToday()) {
                    return response()->json([
                        'errors' => [
                            'message' => ['Cannot rework if it was not from today']
                        ]
                    ], 422);
                }

                if($transferTo->is_running) {
                    return response()->json([
                        'errors' => [
                            'message' => ['Cannot transfer to a running machine.']
                        ]
                    ], 422);
                }

                $customerWash = null;
                $customerDry = null;

                if($transferFrom->machine_type[1] == 'w') {
                    $customerWash = CustomerWash::with('customer')->find($transferFrom->customer_wash_id);
                    if(!$customerWash) {
                        return response()->json([
                            'errors' => [
                                'message' => ['Customer wash not available or deleted']
                            ]
                        ], 422);
                    }
                } else if($transferFrom->machine_type[1] == 'd') {
                    $customerDry = CustomerDry::with('customer')->find($transferFrom->customer_dry_id);
                    if(!$customerDry) {
                        return response()->json([
                            'errors' => [
                                'message' => ['Customer dry not available or deleted']
                            ]
                        ], 422);
                    }
                }

                if($customerDry != null) {
                    return $this->transferDry($customerDry, $transferFrom, $transferTo, $request->remarks);
                } else if($customerWash != null) {
                    return $this->transferWash($customerWash, $transferFrom, $transferTo, $request->remarks);
                }
            });
        }
    }

    private function transferDry($customerDry, $transferFrom, $transferTo, $remarks) {
        return DB::transaction(function () use ($customerDry, $transferFrom, $transferTo, $remarks) {
            $reToken = Str::random(5);
            $url = "$transferTo->ip_address/activate?pulse=$customerDry->pulse_count&token=$customerDry->id" . $reToken;
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_TIMEOUT, 35);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            $output = curl_exec($curl);
            curl_close($curl);

            if($output) {
                $customerDry->update([
                    'dryer_name' => $transferTo->machine_name,
                    'used' => Carbon::now()->toDateTimeString(),
                    'staff_name' => auth('api')->user()->name,
                ]);
                $transferTo->update([
                    'total_minutes' => $customerDry->minutes,
                    'remarks' => "Transfered from $transferFrom->machine_name to $transferTo->machine_name ($remarks)",
                    'time_activated' => Carbon::now(),
                    'user_name' => $customerDry->customer->name,
                    'customer_dry_id' => $customerDry->id,
                    'updated_at' => Carbon::now(),
                ]);
                $transferFrom->update([
                    'total_minutes' => 0,
                    'remarks' => "Transfered from $transferFrom->machine_name to $transferTo->machine_name ($remarks)",
                    'time_activated' => Carbon::now(),
                    'user_name' => null,
                    'customer_dry_id' => null,
                    'updated_at' => Carbon::now(),
                ]);
                $machineUsage = MachineUsage::create([
                    'machine_id' => $transferTo->id,
                    'customer_name' => $customerDry->customer->name,
                    'minutes' => $customerDry->minutes,
                    'activation_type' => 'remote',
                    'price' => $customerDry->price, //$request->serviceType == 'washing' ? $customerDry->price : $customerDry->price,
                    'remarks' => "Transfered from $transferFrom->machine_name to $transferTo->machine_name ($remarks)",
                ]);
                $rework = Rework::create([
                    'remarks' => "Transfered from $transferFrom->machine_name to $transferTo->machine_name ($remarks)",
                    'customer_name' => $customerDry->customer->name,
                    'job_order' => $customerDry->job_order,
                    'account_name' => auth('api')->user()->name,
                    'tag' => 'transfered',
                    'machine_id' => $transferTo->id,
                ]);

                $this->dispatch($transferTo->queSynch());
                $this->dispatch($transferFrom->queSynch());
                $this->dispatch($machineUsage->queSynch());
                $this->dispatch($customerDry->queSynch());
                $this->dispatch($rework->queSynch());

                return response()->json([
                    'to' => $transferTo->fresh('customer'),
                    'from' => $transferFrom->fresh('customer'),
                ]);
            }

            DB::rollback();

            return response()->json([
                'errors' => [
                    'message' => ['Cannot connect to ' . $transferTo->machine_name . ' (' . $transferTo->ip_address . ')'],
                ]
            ], 422);
        });
    }

    private function transferWash($customerWash, $transferFrom, $transferTo, $remarks) {
        return DB::transaction(function () use ($customerWash, $transferFrom, $transferTo, $remarks){
            $reToken = Str::random(5);
            $url = "$transferTo->ip_address/activate?pulse=$customerWash->pulse_count&token=$customerWash->id" . $reToken;
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_TIMEOUT, 35);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            $output = curl_exec($curl);
            curl_close($curl);

            if($output) {
                $customerWash->update([
                    'washer_name' => $transferTo->machine_name,
                    'used' => Carbon::now()->toDateTimeString(),
                    'staff_name' => auth('api')->user()->name,
                ]);
                $transferTo->update([
                    'total_minutes' => $customerWash->minutes,
                    'remarks' => "Transfered from $transferFrom->machine_name to $transferTo->machine_name ($remarks)",
                    'time_activated' => Carbon::now(),
                    'user_name' => $customerWash->customer->name,
                    'customer_wash_id' => $customerWash->id,
                    'updated_at' => Carbon::now(),
                ]);
                $transferFrom->update([
                    'total_minutes' => 0,
                    'remarks' => "Transfered from $transferFrom->machine_name to $transferTo->machine_name ($remarks)",
                    'time_activated' => Carbon::now(),
                    'user_name' => null,
                    'customer_wash_id' => null,
                    'updated_at' => Carbon::now(),
                ]);
                $machineUsage = MachineUsage::create([
                    'machine_id' => $transferTo->id,
                    'customer_name' => $customerWash->customer->name,
                    'minutes' => $customerWash->minutes,
                    'activation_type' => 'remote',
                    'price' => $customerWash->price, //$request->serviceType == 'washing' ? $customerWash->price : $customerDry->price,
                    'remarks' => "Transfered from $transferFrom->machine_name to $transferTo->machine_name ($remarks)",
                ]);
                $rework = Rework::create([
                    'remarks' => "Transfered from $transferFrom->machine_name to $transferTo->machine_name ($remarks)",
                    'customer_name' => $customerWash->customer->name,
                    'job_order' => $customerWash->job_order,
                    'account_name' => auth('api')->user()->name,
                    'tag' => 'transfered',
                    'machine_id' => $transferTo->id,
                ]);

                $this->dispatch($transferTo->queSynch());
                $this->dispatch($transferFrom->queSynch());
                $this->dispatch($machineUsage->queSynch());
                $this->dispatch($customerWash->queSynch());
                $this->dispatch($rework->queSynch());

                return response()->json([
                    'to' => $transferTo->fresh('customer'),
                    'from' => $transferFrom->fresh('customer'),
                ]);
            }

            DB::rollback();

            return response()->json([
                'errors' => [
                    'message' => ['Cannot connect to ' . $transferTo->machine_name . ' (' . $transferTo->ip_address . ')'],
                ]
            ], 422);
        });
    }

    public function getUsedServices($transferFrom) {

        // $serviceTransactionItem = ServiceTransactionItem::whereHas('transaction', function($query) use ($jobOrder) {
        //     $query->where('job_order', $jobOrder);
        // })->whereDate('used', Carbon::now())->get();

        // return $serviceTransactionItem;
        $machine = Machine::findOrFail($transferFrom);
        $machineType = $machine->machine_type[0] == 't' ? 'TITAN' : 'REGULAR';

        if($machine->machine_type == 'rw' || $machine->machine_type == 'tw') {
            // washers
            $customerWash = CustomerWash::whereDate('used', Carbon::now())
                ->where('customer_id', $machine->customer_id)
                ->where('machine_type', $machineType)
                ->where('machine_name', $machine->machine_name);
        } else if($machine->machine_type == 'rd' || $machine->machine_type == 'td') {
            // dryers
        }


        // if($machine->machineType[1] == 'w') {
        //     // washers
        // } else if($machine->machineType[1] == 'd') {
        //     // dryers
        // }
    }
}
