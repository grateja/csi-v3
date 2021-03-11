<?php

namespace App\Http\Controllers;

use App\Machine;
use App\MachineUsage;
use App\RfidCard;
use App\RfidCardTransaction;
use App\UnregisteredCard;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TapCardController extends Controller
{
    public function tap($ip, $rfid, $macAddress = null, Request $request) {
        $token = $request->token;
        $machine = Machine::where('ip_address', $ip)->first();
        if($machine == null) {
            Machine::create([
                'ip_address' => $ip,
                'mac_address' => $macAddress,
                'machine_name' => $ip,
                'initial_price' => 0,
                'additional_price' => 0,
                'initial_time' => 0,
                'additional_time' => 0,
                'machine_type' => 'undefined',
            ]);
            return response()->json([
                'message' => 'Machine IP ' . $ip . ' not registered. Autoregistered.'
            ], 422);
        } else if($machine->machine_type == 'undefined') {
            return response()->json([
                'message' => 'Machine IP ' . $ip . ' undefined machine type'
            ], 422);
        } else {
            $machine->update([
                'mac_address' => $macAddress,
            ]);
        }

        $rfidCard = RfidCard::where(function($query) {
            $query->whereHas('customer')->orWhereHas('user');
        })->where('rfid', $rfid)->first();
        if($rfidCard == null) {

            $unregisteredCard = UnregisteredCard::where('rfid', $rfid)->first();
            if($unregisteredCard) {
                $unregisteredCard->update([
                    'rfid' => $rfid,
                    'machine_name' => $machine->machine_name,
                    'updated_at' => Carbon::now(),
                ]);
            } else {
                $unregisteredCard = UnregisteredCard::create([
                    'rfid' => $rfid,
                    'machine_name' => $machine->machine_name,
                ]);
            }

            return response()->json([
                'message' => 'RFID Card ' . $rfid . ' not registered'
            ], 422);
        } else if($rfidCard->card_type == 'c' && $rfidCard->balance <= 0) {
            return response()->json([
                'message' => 'RFID Card ' . $rfid . ' has 0 balance'
            ], 422);
        }

        if($machine->is_running && $machine->user_name != $rfidCard->owner_name) {
            return response()->json([
                'message' => 'Machine is already in use by a different customer: ' . $machine->user_name,
            ], 422);
        }

        $machineUsage = MachineUsage::find($token);
        if($machineUsage) {
            if($machineUsage->created_at->isToday()) {
                // Connection lost from ESP8266
                // Reactivation
                return response()->json([
                    'pulse' => 1,
                    'token' => $token,
                    'message' => 'Reactivation',
                ]);
            }
            $token = null;
        }

        return DB::transaction(function () use ($rfidCard, $machine, $token) {
            $machine = $machine->tapActivate($rfidCard, $token);

            if($machine) {
                if($rfidCard->card_type == 'c' && $rfidCard->balance - $machine->price < 0) {
                    DB::rollback();
                    return response()->json([
                        'message' => 'RFID Card ' . $rfidCard->rfid . ' has ' . $rfidCard->balance . ' balance'
                    ], 422);
                }

                $rfidCard->decrement('balance', $rfidCard->card_type == 'c' ? $machine->price : 0);

                $this->dispatch($machine->queSynch());
                $this->dispatch($rfidCard->queSynch());
                $this->dispatch($machine->machineUsage->queSynch());
                $this->dispatch($machine->rfidCardTransaction->queSynch());

                //sleep(50);

                return response()->json([
                   'rfid' => $rfidCard->rfid,
                   'balance' => $rfidCard->balance,
                   'price' => $machine->price,
                   'pulse' => 1,
                ]);
            }

            DB::rollback();

            return response()->json([
                'message' => 'Cannot activate machine',
            ], 422);
        });
    }
}
