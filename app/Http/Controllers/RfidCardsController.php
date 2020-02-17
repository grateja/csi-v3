<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\RfidCard;
use App\RfidLoadTransaction;
use App\UnregisteredCard;
use Illuminate\Support\Facades\DB;

class RfidCardsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function index($cardType, Request $request)
    // {
    //     $result = RfidCard::with('customer')->where(function($query) use ($request) {
    //         $query->where('rfid', 'like', "%$request->keyword%")
    //             ->orWhereHas('customer', function($query) use ($request) {
    //                 $query->where('name', 'like', "%$request->keyword%");
    //             });
    //     });

    //     if($request->customerId) {
    //         $result = $result->where('customer_id', $request->customerId);
    //     }

    //     if($cardType != 'all') {
    //         $result = $result->where('card_type', $cardType);
    //     }

    //     return response()->json([
    //         'result' => $result->paginate(10),
    //     ], 200);
    // }

    public function index(Request $request) {
        $sortBy = $request->sortBy ? $request->sortBy : 'fullname';
        $order = $request->orderBy ? $request->orderBy : 'asc';

        $result = DB::table('rfid_cards')
            ->leftjoin('customers', 'customers.id', '=', 'rfid_cards.customer_id')
            ->leftjoin('users', 'users.id', '=', 'rfid_cards.user_id')
            ->selectRaw('rfid_cards.id as rfid_card_id, coalesce(users.name, customers.name) as fullname, rfid, balance, card_type, customers.id as customer_id, users.id as user_id, rfid_cards.id as rfid_card_id, rfid_cards.created_at as enrolled')
            ->where(function($query) use ($request) {
                $query->where('users.name', 'like', "%$request->keyword%")
                    ->orWhere('customers.name', 'like', "%$request->keyword%")
                    ->orWhere('rfid', 'like', "%$request->keyword%");
            })->whereNull('rfid_cards.deleted_at')
            ->whereNull('users.deleted_at')
            ->whereNull('customers.deleted_at');

        if($request->date) {
            $result = $result->whereDate('rfid_cards.created_at', $request->date);
        }

        $result = $result->orderBy($sortBy, $order);


        return response()->json([
            'result' => $result->paginate(10),
        ]);
    }

    public function customerCards(Request $request) {
        $result = RfidCard::where('customer_id', $request->customerId)->get();
        return response()->json([
            'result' => $result
        ]);
    }

    public function unregisteredCards() {
        $result = UnregisteredCard::orderByDesc('updated_at')->limit(10)->get();
        return response()->json([
            'result' => $result,
        ]);
    }

    public function clearUnregisteredCards() {
        $result = UnregisteredCard::truncate();
        return response()->json([
            'result' => $result,
        ]);
    }

    public function topUp($rfidCardId, Request $request) {
        $rules = [
            'amount' => 'required|numeric',
            'cash' => 'required|numeric',
        ];

        if($request->validate($rules)) {
            return DB::transaction(function () use ($request, $rfidCardId) {
                $rfidCard = RfidCard::findOrFail($rfidCardId);

                $rfidLoadTransaction = RfidLoadTransaction::create([
                    'rfid_card_id' => $rfidCard->id,
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
                    'rfidCard' => $rfidCard,
                    'rfidLoadTransaction' => $rfidLoadTransaction,
                ]);
            });
        }
    }

    public function deleteCard($rfidCardId) {
        $rfidCard = RfidCard::findOrFail($rfidCardId);
        if($rfidCard->delete()) {
            return response()->json([
                'rfidCard' => $rfidCard,
            ]);
        }
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'rfid' => 'required|unique:rfid_cards',
            'cardType' => 'required',
            'ownerId' => 'required',
        ];

        if($request->validate($rules)) {

            return DB::transaction(function () use ($request) {
                $rfidCard = RfidCard::create([
                    'rfid' => $request->rfid,
                    'customer_id' => $request->cardType == 'c' ? $request->ownerId : null,
                    'user_id' => $request->cardType == 'u' ? $request->ownerId : null,
                    'card_type' => $request->cardType,
                ]);

                UnregisteredCard::where('rfid', $request->rfid)->delete();

                $rfidCard = [
                    'fullname' => $rfidCard->ownerName,
                    'rfid' => $rfidCard->rfid,
                    'rfid_card_id' => $rfidCard->id,
                    'balance' => 0,
                    'card_type' => $rfidCard->card_type,
                    'customer_id' => $rfidCard->customer_id,
                    'user_id' => $rfidCard->user_id,
                    'enrolled' => $rfidCard->created_at,
                ];

                return response()->json([
                    'rfidCard' => $rfidCard
                ], 200);
            });
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $rfidCard = RfidCard::findOrFail($id);

        $rules = [
            'rfid' => 'required'
        ];

        if($request->rfid != $rfidCard->rfid) {
            // rfid changed
            $rules = [
                'rfid' => 'required|unique:rfid_cards',
            ];
        }

        if($request->cardType == 'u' && !auth('api')->user()->hasAnyRole(['admin'])) {
            return response()->json([
                'errors' => [
                    'rfid' => ['Only owners can register/edit a master card']
                ]
            ], 422);
        }

        if($request->validate($rules)) {
            return DB::transaction(function () use ($request, $rfidCard) {
                $rfidCard->update([
                    'rfid' => $request->rfid,
                ]);

                return response()->json([
                    'rfidCard' => $rfidCard->fresh('customer', 'user')
                ], 200);
            });
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
