<?php

namespace App\Http\Controllers;

use App\RfidCard;
use App\RfidLoadTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RfidLoadController extends Controller
{
    public function index(Request $request) {
        $sortBy = $request->sortBy ? $request->sortBy : 'created_at';
        $order = $request->orderBy ? $request->orderBy : 'desc';

        $result = RfidLoadTransaction::where(function($query) use ($request) {
            $query->where('rfid', 'like', "%$request->keyword%")
                ->orWhere('customer_name', 'like', "%$request->keyword%")
                ->orWhere('remarks', 'like', "%$request->keyword%");
        });

        if($request->date) {
            $result = $result->whereDate('created_at', $request->date);
        }

        $result = $result->orderBy($sortBy, $order);


        return response()->json([
            'result' => $result->paginate(10),
        ]);
    }

    public function topUp($rfidCardId, Request $request) {
        $rules = [
            'amount' => 'required|numeric|min:1',
            'cash' => 'required|numeric',
        ];

        if($request->validate($rules)) {
            return DB::transaction(function () use ($request, $rfidCardId) {
                $rfidCard = RfidCard::findOrFail($rfidCardId);

                $rfidLoadTransaction = RfidLoadTransaction::create([
                    'rfid_card_id' => $rfidCard->id,
                    'customer_name' => $rfidCard->owner_name,
                    'rfid' => $rfidCard->rfid,
                    'amount' => $request->amount,
                    'remaining_balance' => $rfidCard->balance,
                    'current_balance' => $rfidCard->balance + $request->amount,
                    'cash' => $request->cash,
                    'change' => $request->cash - $request->amount,
                    'user_id' => auth('api')->id(),
                    'remarks' => $request->remarks,
                ]);

                $rfidCard->increment('balance', $request->amount);

                return response()->json([
                    'rfidLoadTransaction' => $rfidLoadTransaction,
                    'rfidCard' => $rfidCard,
                ]);
            });
        }
    }

    public function deleteTransaction($rfidLoadId) {
        return DB::transaction(function () use ($rfidLoadId) {
            $rfidLoadTransaction = RfidLoadTransaction::findOrfail($rfidLoadId);

            $rfidCard = RfidCard::findOrFail($rfidLoadTransaction->rfid_card_id);

            $amount = $rfidCard->balance - $rfidLoadTransaction->amount < 0 ? $rfidCard->balance : $rfidLoadTransaction->amount;

            if($rfidLoadTransaction->delete()) {
                RfidCard::find($rfidLoadTransaction->rfid_card_id)->decrement('balance', $amount);
                return response()->json([
                    'message' => 'Transaction deleted successfuly',
                ]);
            }
        });
    }
}
