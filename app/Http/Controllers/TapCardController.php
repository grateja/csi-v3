<?php

namespace App\Http\Controllers;

use App\Machine;
use App\RfidCard;
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
            return response()->json([
                'message' => 'RFID Card ' . $rfid . ' not registered'
            ], 422);
        } else if($rfidCard->card_type == 'c' && $rfidCard->balance <= 0) {
            return response()->json([
                'message' => 'RFID Card ' . $rfid . ' has 0 balance'
            ], 422);
        }


        return DB::transaction(function () use ($rfidCard, $machine) {
            $machine = $machine->tapActivate($rfidCard->owner_name);

            if($machine) {
                if($rfidCard->balance - $machine->price < 0) {
                    DB::rollback();
                    return response()->json([
                        'message' => 'RFID Card ' . $rfidCard->rfid . ' has ' . $rfidCard->balance . ' balance'
                    ], 422);
                }

                $rfidCard->decrement('balance', $rfidCard->card_type == 'c' ? $machine->price : 0);

                return response()->json([
                    'rfid' => $rfidCard->rfid,
                    'balance' => $rfidCard->balance,
                ]);
            }

            return response()->json([
                'message' => 'Cannot activate machine',
            ], 422);
        });
    }
}
