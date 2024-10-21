<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Machine;
use App\EluxMachine;
use App\OutSourceService;
use App\OutSourceMachineUsage;
use App\OutSourceMachineUsageLinen;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class OutSourceRemoteController extends Controller
{
    public function activate(Request $request) {
        $rules = [
            'machine_id' => 'required',
            'out_source_id' => 'required',
            'linens' => 'required',
            'serviceId' => 'required',
        ];

        $messages = [
            'linens.required' => 'Please add at least 1 linen'
        ];

        $request->validate($rules, $messages);

        $commit = DB::transaction(function () use ($request) {
            \Log::debug('no errors');
            $service = OutSourceService::findOrFail($request->serviceId);
            $token = Str::uuid();
            $eluxId = null;
            $lgId = null;
            if($request->serviceType == 'elux') {
                $pushDelay = 500;
                $eluxId = $request->machine_id;
                $machine = EluxMachine::findOrFail($eluxId);
                $machine->update([
                    'total_minutes' => $service->minutes,
                    'time_activated' => Carbon::now(),
                ]);
            } else {
                $pushDelay = 60;
                $lgId = $request->machine_id;
                $machine = Machine::findOrFail($lgId);
                $machine->update([
                    'total_minutes' => $service->minutes,
                    'time_activated' => Carbon::now(),
                    'customer_id' => null,
                ]);
            }

            $url = "$machine->ip_address/activate?pulse=$service->pulse_count&token=$token&pushDelay=$pushDelay";

            \Log::debug($url);
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_TIMEOUT, 30);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            $output = curl_exec($curl);
            $curl_error = curl_error($curl);
            curl_close($curl);

            if(!$curl_error) {
                $machineUsage = OutSourceMachineUsage::create([
                    'machine_id' => $lgId,
                    'elux_machine_id' => $eluxId,
                    'user_id' => auth()->user()->id,
                    'out_source_id' => $request->out_source_id,
                    'out_source_service_id' => $service->id,
                    'minutes' => $service->minutes,
                ]);

                foreach ($request->linens as $linen) {
                    OutSourceMachineUsageLinen::create([
                        'out_source_machine_usage_id' => $machineUsage->id,
                        'name' => $linen['name'],
                        'quantity' => $linen['quantity'],
                    ]);
                }

                return [
                    'machine' => $machine->fresh('customer'),
                    // 'customerWash' => $customerWash,
                    // 'customerDry' => $customerDry,
                    // 'pulse' => $pulse,
                    // 'output' => $output,
                ];
            } else {
                DB::rollback();

                return [
                    'errors' => [
                        'message' => ['Cannot connect to ' . $machine->machine_name . ' (' . $machine->ip_address . ')'],
                        // 'machine' => $machine,
                        // 'output' => $output,
                    ],
                    'machine' => $machine,
                ];
            }

            return $machine;
        });

        if(array_key_exists('errors', $commit)) {
            return response()->json($commit, 422);
        }
        return response()->json($commit);
    }
}
