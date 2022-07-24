<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\LoyaltyPoint;

class LoyaltyPointsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $loyaltyPoint = LoyaltyPoint::first();

        if($loyaltyPoint != null) {
            return response()->json([
                'errors' => [
                    'amount' => ['Loyalty point is already set up. Modify the existing one.']
                ]
            ], 422);
        }

        $loyaltyPoint = LoyaltyPoint::create([
            'amount_in_peso' => $request->amount,
        ]);

        return response()->json([
            'loyaltyPoint' => $loyaltyPoint,
            'success' => 'Loyalty points saved'
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        $loyaltyPoint = LoyaltyPoint::first();

        if($loyaltyPoint) {
            return response()->json([
                'loyaltyPoint' => $loyaltyPoint,
            ], 200);
        } else {
            return response()->json([
                'message' => 'Loyalty points not set up'
            ], 404);
        }
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
    public function update(Request $request)
    {
        $loyaltyPoint = LoyaltyPoint::first();

        $rules = [
            'amount' => 'required',
        ];

        if($loyaltyPoint && $request->validate($rules)) {
            $loyaltyPoint->update([
                'amount_in_peso' => $request->amount,
            ]);

            return response()->json([
                'loyaltyPoint' => $loyaltyPoint,
                'success' => 'Loyalty points saved'
            ], 200);
        } else {
            return response()->json([
                'amount' => 'Loyalty points not set up'
            ], 404);
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
