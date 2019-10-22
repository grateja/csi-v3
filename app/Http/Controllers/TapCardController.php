<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\RfidCard;
use App\Machine;
use App\RfidServicePrice;
use App\RfidTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class TapCardController extends Controller
{
    // gratch
    public function tap($machineIp, $rfid) {
        $rfidCard = RfidCard::where('rfid', $rfid)->first();
        if($rfidCard == null) {
            return response()->json([
                'message' => 'RFID not registered',
            ], 422);
        }

        if($rfidCard->balance == 0 && $rfidCard->card_type == 'c') {
            return response()->json([
                'message' => 'Card doesn`t have load',
            ], 422);
        }

        $machine = Machine::where('ip_address', $machineIp)->first();
        if($machine == null) {
            return response()->json([
                'message' => 'Machine IP not registered',
            ], 422);
        }

        if($machine->machine_type_id == 1 || $machine->machine_type_id == 3) {
            if($machine->is_running && $machine->can_add_superwash) {
                $servicePrice = RfidServicePrice::where(['machine_type_id' => $machine->machine_type_id, 'add_super_wash' => 1, 'enabled' => true])->first();
            } else if($machine->is_running && !$machine->can_add_superwash) {
                return response()->json([
                    'message' => 'Machine is already running',
                ], 422);
            } else {
                $servicePrice = RfidServicePrice::where(['machine_type_id' => $machine->machine_type_id, 'add_super_wash' => 0, 'enabled' => true])->first();
            }
        } else if($machine->machine_type_id == 2 || $machine->machine_type_id == 4) {
            $servicePrice = RfidServicePrice::where(['machine_type_id' => $machine->machine_type_id])->first();
        }

        if($servicePrice == null) {
            return response()->json([
                'message' => 'Service price not available',
            ], 422);
        }

        // $url = $machineIp . '/guillotine';

        // $curl = curl_init();
        // curl_setopt($curl, CURLOPT_URL, $url);
        // curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        // curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        // $output = curl_exec($curl);
        // curl_close($curl);


        if(1 /*$output*/) {
            DB::transaction(function () use ($machine, $servicePrice, $rfidCard) {
                // increment total minutes
                // check if machine already stopped
                // if($machine->time_ends_in < Carbon::now()) {
                if($machine->is_running) {
                    // leave activation time
                    $machine->update([
                        'total_minutes' => DB::raw('total_minutes+' . $servicePrice->minutes_per_pulse * $servicePrice->pulse_count),
                        'is_add_superwash' => true,
                    ]);
                } else {
                    // machine already stopped
                    // activate machine
                    $machine->update([
                        'total_minutes' => $servicePrice->minutes_per_pulse * $servicePrice->pulse_count,
                        'time_activated' => Carbon::now(),
                        'customer_id' => $rfidCard->customer_id,
                        'is_add_superwash' => false,
                    ]);
                }
                if(!$rfidCard->unlimited) {
                    $rfidCard->update([
                        'balance' => DB::raw('balance-' . $servicePrice->price)
                    ]);
                }
                RfidTransaction::create([
                    'client_id' => $servicePrice->client_id,
                    'branch_id' => $servicePrice->branch_id,
                    'machine_id' => $machine->id,
                    'rfid_card_id' => $rfidCard->id,
                    'rfid_service_price_id' => $servicePrice->id,
                    'price' => $servicePrice->price,
                ]);
            });
            return $servicePrice->pulse_count;
        }


        return response()->json([
            'message' => 'Something went wrong',
            //'rfidCard' => $rfidCard,
        ], 200);
    }
}
