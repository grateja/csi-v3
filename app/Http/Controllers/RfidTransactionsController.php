<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\RfidTransaction;
use App\RfidCard;
use App\BranchAuth;
use App\ClientAuth;
use Illuminate\Support\Facades\DB;
use App\RfidLoadTransaction;

class RfidTransactionsController extends Controller
{
    public function rfidServiceIndex($branchId, Request $request) {
        $branchId = $branchId == 'self' ? auth('api')->user()->active_branch_id : $branchId;

        $result = RfidTransaction::with('machine', 'rfidCard.customer', 'user', 'rfidServicePrice')->where(function($query) use ($request) {
            $query->whereHas('rfidCard', function($query) use ($request) {
                $query->whereHas('customer', function($query) use ($request) {
                    $query->where('name', 'like', "%$request->keyword%");
                });
            })->orWhereHas('user', function($query) use ($request) {
                $query->where('firstname', 'like', "%$request->keyword%")
                    ->orWhere('lastname', 'like', "%$request->keyword%");
            })->orWhereHas('machine', function($query) use ($request) {
                $query->where('name', 'like', "%$request->keyword%");
            })->orWhereHas('rfidCard', function($query) use ($request) {
                $query->where('rfid', 'like', "%$request->keyword%");
            });
        })->where('branch_id', $branchId)->orderByDesc('created_at');

        //if(auth('api')->user()->hasAnyRole(['oic', 'staff'])) {
        //    $result = $result->whereHas('rfidCard', function($query) {
        //        $query->where('user_id', auth('api')->id());
        //    });
        //}

        if($request->dateRange) {
            $dateRange = json_decode($request->dateRange);
            $result = $result->whereBetween(DB::raw('DATE(created_at)'), [
                $dateRange->from,
                $dateRange->to,
            ]);
        }

        $summary = [
            'totalPrice' => collect($result->get())->sum('rfidServicePrice.price')
        ];

        return response()->json([
            'result' => $result->paginate(10),
            'summary' => $summary
        ], 200);
    }

    public function topUpIndex(Request $request) {
        $result = RfidLoadTransaction::with('user', 'rfidCard')
            ->where(function($query) use ($request) {
                $query->whereHas('rfidCard.customer', function($query) use ($request) {
                    $query->where('name', 'like', "%$request->keyword%");
                })->orWhereHas('rfidCard', function($query) use ($request) {
                    $query->where('rfid', 'like', "%$request->keyword%");
                });
            })->whereHas('rfidCard', function($query) use ($request) {
                $query->where('branch_id', auth('api')->user()->active_branch_id)->orderByDesc('created_at');
            });

        if(auth('api')->user()->hasAnyRole(['oic', 'staff'])) {
            $result = $result->where(function($query) {
                $query->where('user_id', auth('api')->id());
            });
        }


        if($request->dateRange) {
            $dateRange = json_decode($request->dateRange);
            $result = $result->whereBetween(DB::raw('DATE(created_at)'), [
                $dateRange->from,
                $dateRange->to,
            ]);
        }

        $summary = [
            'totalPrice' => $result->sum('amount')
        ];

        return response()->json([
            'result' => $result->paginate(10),
            'summary' => $summary,
        ], 200);
    }

    public function topUp($rfidCardId, Request $request) {
        $rfidCard = RfidCard::findOrFail($rfidCardId);

        BranchAuth::check($rfidCard->branch_id);
        ClientAuth::check($rfidCard->client_id);

        $rules = [
            'cash' => 'required|numeric|min:' . $request->amount,
            'amount' => 'required|numeric|min:1',
        ];

        if($request->validate($rules)) {
            return DB::transaction(function () use ($request, $rfidCard) {

                RfidLoadTransaction::create([
                    'rfid_card_id' => $rfidCard->id,
                    'amount' => $request->amount,
                    'remaining_balance' => $rfidCard->balance,
                    'current_balance' => $rfidCard->balance + $request->amount,
                    'cash' => $request->cash,
                    'change' => $request->cash - $request->amount,
                    'remarks' => $request->remarks,
                    'user_id' => auth('api')->id(),
                ]);

                $rfidCard->update([
                    'balance' => DB::raw('balance+'.$request->amount)
                ]);

                return response()->json([
                    'rfidCard' => $rfidCard->fresh(),
                ], 200);
            });
        }
    }
}
