<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Machine;
use App\Customer;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\CompletedServiceTransaction;

class RemotesController extends Controller
{
    public function activate($machineId, Request $request) {
        $machine = Machine::find($machineId);

        if($machine == null) {
            return response()->json([
                'errors' => [
                    'machineId' => ['Machine not set up']
                ]
            ], 422);
        }

        $completedServiceTransaction = CompletedServiceTransaction::with('service', 'branchService')->where([
            'customer_id' => $request->customerId,
            'service_id' => $request->serviceId,
            'available' => 1,
        ])->first();

        if($completedServiceTransaction == null) {
            return response()->json([
                'errors' => [
                    'serviceId' . $request->serviceId => ['Service not available']
                ]
            ], 422);
        }

        if($completedServiceTransaction->available <= 0) {
            return response()->json([
                'errors' => [
                    'serviceId' . $request->serviceId => ['Customer do not have purchased ' . $completedServiceTransaction->service->name]
                ]
            ], 422);
        }

        if(!$machine->is_running && $completedServiceTransaction->add_super_wash) {
            return response()->json([
                'errors' => [
                    'machineId' => ['Cannot activate add super wash. Activate regular wash first']
                ]
            ], 422);
        }

        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        // guillotine?pulse=1
        ///  url = addSuperWash
        ///  url = regularWash
        ///  url = dryPulse1

        $url = "{$machine->ip_address}/activate?pulse={$completedServiceTransaction->branchService->pulse_count}";
        // $url = $url = "{$machine->ip_address}/";

        // if($machine->machine_type_id == 1 || $machine->machine_type_id == 3) {
        //     $url .= "guillotine";
        //     // if($completedServiceTransaction->add_super_wash) {
        //     //     // $url = $url . "&addSuperWash=1";
        //     //     $url .= "SuperWash";
        //     // } else {
        //     //     $url .= "regularWash";
        //     // }
        // } else if($machine->machine_type_id == 2 || $machine->machine_type_id == 4) {
        //     $url .= "dryPulse{$completedServiceTransaction->branchService->pulse_count}";
        // }

        // $url = "{$machine->ip_address}/guillotine?pulse={$completedServiceTransaction->branchService->pulse_count}";
        // if($completedServiceTransaction->add_super_wash) {
        //     // $url = $url . "&addSuperWash=1";
        //     $url = "&addSuperWash=1";
        // }
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        if($output) {
            return DB::transaction(function () use ($url, $machine, $completedServiceTransaction, $output) {

                // deduct pulse count
                $completedServiceTransaction->update([
                    'available' => DB::raw('available-1'),
                    'machine_name' => $machine->name,
                    'machine_id' => $machine->id,
                    'time_activated' => Carbon::now(),
                ]);

                // increment total minutes
                // check if machine already stopped
                // if($machine->time_ends_in < Carbon::now()) {
                if($machine->is_running) {
                    // leave activation time
                    $machine->update([
                        'total_minutes' => DB::raw('total_minutes+' . $completedServiceTransaction->branchService->minutes_per_pulse * $completedServiceTransaction->branchService->pulse_count),
                    ]);
                } else {
                    // machine already stopped
                    // activate machine
                    $machine->update([
                        'total_minutes' => $completedServiceTransaction->branchService->minutes_per_pulse * $completedServiceTransaction->branchService->pulse_count,
                        'time_activated' => Carbon::now(),
                        'customer_id' => $completedServiceTransaction->customer_id,
                    ]);
                }

                return response()->json([
                    'message' => 'Done',
                    'url' => $url,
                    'output' => $output
                ], 200);
            });
        } else {
            return response()->json([
                'errors' => [
                    'machineId' => ['Cannot connect to ' . $machine->name . ' (' . $machine->ip_address . ')']
                ]
            ], 422);
        }
    }
}
