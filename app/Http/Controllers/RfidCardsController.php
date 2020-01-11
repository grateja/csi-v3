<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\RfidCard;
use Illuminate\Support\Facades\DB;

class RfidCardsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($cardType, Request $request)
    {
        $result = RfidCard::with('customer')->where(function($query) use ($request) {
            $query->where('rfid', 'like', "%$request->keyword%")
                ->orWhereHas('customer', function($query) use ($request) {
                    $query->where('name', 'like', "%$request->keyword%");
                });
        });

        if($request->customerId) {
            $result = $result->where('customer_id', $request->customerId);
        }

        if($cardType != 'all') {
            $result = $result->where('card_type', $cardType);
        }

        return response()->json([
            'result' => $result->paginate(10),
        ], 200);
    }

    public function customerCards(Request $request) {
        $result = RfidCard::where('customer_id', $request->customerId)->get();
        return response()->json([
            'result' => $result
        ]);
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
        ];

        if($request->customerId == null && $request->cardType == 'c') {
            $rules = array_merge($rules, [
                'customerId' => 'required',
            ]);
        } else if($request->userId == null && $request->cardType == 'u') {
            $rules = array_merge($rules, [
                'userId' => 'required',
            ]);
        }

        if($request->cardType == 'u' && !auth('api')->user()->hasAnyRole(['admin'])) {
            return response()->json([
                'errors' => [
                    'rfid' => ['Only owners can register a master card']
                ]
            ], 422);
        }

        if($request->validate($rules)) {

            return DB::transaction(function () use ($request) {
                $rfidCard = RfidCard::create([
                    'rfid' => $request->rfid,
                    'customer_id' => $request->customerId,
                    'user_id' => $request->userId,
                    'card_type' => $request->cardType,
                ]);

                return response()->json([
                    'rfidCard' => $rfidCard->fresh('customer', 'user')
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
