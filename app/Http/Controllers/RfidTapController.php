<?php

namespace App\Http\Controllers;

use App\RfidCardTransaction;
use Illuminate\Http\Request;

class RfidTapController extends Controller
{
    public function index(Request $request) {
        $sortBy = $request->sortBy ? $request->sortBy : 'fullname';
        $order = $request->orderBy ? $request->orderBy : 'asc';

        $result = RfidCardTransaction::where(function($query) {

        });

        if($request->date) {
            $result = $result->whereDate('rfid_cards.created_at', $request->date);
        }

        $result = $result->orderBy($sortBy, $order);


        return response()->json([
            'result' => $result->paginate(10),
        ]);
    }

    public function deleteTransaction($transactionId) {
        $rfidCardTransaction =RfidCardTransaction::findOrFail($transactionId);

        if($rfidCardTransaction->delete()) {
            return response()->json([
                'rfidCardTransaction' => $rfidCardTransaction,
            ]);
        }
    }
}
