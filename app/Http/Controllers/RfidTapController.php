<?php

namespace App\Http\Controllers;

use App\RfidCardTransaction;
use Illuminate\Http\Request;

class RfidTapController extends Controller
{
    public function index(Request $request) {
        $sortBy = $request->sortBy ? $request->sortBy : 'created_at';
        $order = $request->orderBy ? $request->orderBy : 'desc';

        $result = RfidCardTransaction::where(function($query) use ($request) {
            $query->where('owner_name', 'like', "%$request->keyword%")
                ->orWhere('rfid', 'like', "%$request->keyword%")
                ->orWhere('machine_name', 'like', "%$request->keyword%");
        });

        if($request->date) {
            $result = $result->whereDate('created_at', $request->date);
        }

        if($request->cardType == 'customer') {
            $result = $result->where('card_type', 'c');
        } else if($request->cardType == 'user') {
            $result = $result->where('card_type', 'u');
        }

        $result = $result->orderBy($sortBy, $order);


        return response()->json([
            'result' => $result->paginate(10),
            'summary' => $this->rfidSummary($request)
        ]);
    }

    private function rfidSummary($request) {
        $result = RfidCardTransaction::where(function($query) use ($request) {
            $query->where('owner_name', 'like', "%$request->keyword%")
                ->orWhere('rfid', 'like', "%$request->keyword%")
                ->orWhere('machine_name', 'like', "%$request->keyword%");
        });

        if($request->date) {
            $result = $result->whereDate('created_at', $request->date);
        }

        if($request->cardType == 'customer') {
            $result = $result->where('card_type', 'c');
        } else if($request->cardType == 'user') {
            $result = $result->where('card_type', 'u');
        }

        return [
            'total_items' => $result->count(),
            'total_price' => $result->sum('price'),
            'total_minutes' => $result->sum('minutes'),
        ];
    }

    public function deleteTransaction($transactionId) {
        $rfidCardTransaction =RfidCardTransaction::findOrFail($transactionId);

        if($rfidCardTransaction->delete()) {
            $this->dispatch($rfidCardTransaction->queSynch());
            return response()->json([
                'rfidCardTransaction' => $rfidCardTransaction,
            ]);
        }
    }
}
