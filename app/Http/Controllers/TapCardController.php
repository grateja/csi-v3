<?php

namespace App\Http\Controllers;

use App\Machine;
use App\RfidCard;
use App\RfidCardTransaction;
use App\UnregisteredCard;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TapCardController extends Controller
{
    public function tap($ip, $rfid) {
        $machine = Machine::where('ip_address', $ip)->first();
        if($machine == null) {
            return response()->json([
                'message' => 'Machine IP ' . $ip . ' not registered'
            ], 422);
        }

        $rfidCard = RfidCard::where('rfid', $rfid)->first();
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

        if($machine->is_running && $machine->customer_name != $rfidCard->owner_name) {
            return response()->json([
                'message' => 'Machine is already in use by a different customer: ' . $machine->customer_name,
            ], 422);
        }

        return DB::transaction(function () use ($rfidCard, $machine) {
            $machine = $machine->tapActivate($rfidCard->owner_name, $rfidCard);

            if($machine) {
                if($rfidCard->card_type == 'c' && $rfidCard->balance - $machine->price < 0) {
                    DB::rollback();
                    return response()->json([
                        'message' => 'RFID Card ' . $rfidCard->rfid . ' has ' . $rfidCard->balance . ' balance'
                    ], 422);
                }

                $rfidCard->decrement('balance', $rfidCard->card_type == 'c' ? $machine->price : 0);
                return response()->json([
                    'rfid' => $rfidCard->rfid,
                    'balance' => $rfidCard->balance,
                    'price' => $machine->price,
                ]);
            }

            return response()->json([
                'message' => 'Cannot activate machine',
            ], 422);
        });
    }
}
