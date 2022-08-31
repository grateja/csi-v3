<?php

namespace App\Http\Controllers;

use App\LagoonPartner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LagoonPartnersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $result = LagoonPartner::withCount('transactions', 'customers')->where(function($query) use ($request) {

        })->get();
 
        return response()->json([
            'result' => $result,
        ], 200);
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
            'id' => 'required|unique:lagoon_partners',
            'shopName' => 'required',
            'address' => 'required',
        ];

        if($request->validate($rules)) {
            return DB::transaction(function () use ($request) {
                $lagoonPartner = LagoonPartner::create([
                    'id' => $request->id,
                    'shop_name' => $request->shopName,
                    'address' => $request->address,
                ]);

                return response()->json([
                    'lagoonPartner' => $lagoonPartner,
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $lagoonPartner = LagoonPartner::findOrFail($id);

        $rules = [
            'id' => 'required',
            'shopName' => 'required',
            'address' => 'required',
        ];

        if($request->id != $id) {
            $rules['id'] = 'required|unique:lagoon_partners';
        }

        if($request->validate($rules)) {
            return DB::transaction(function () use ($request, $lagoonPartner) {
                $lagoonPartner->update([
                    'id' => $request->id,
                    'shop_name' => $request->shopName,
                    'address' => $request->address,
                ]);

                return response()->json([
                    'lagoonPartner' => $lagoonPartner,
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
    public function delete($id)
    {
        $discount = LagoonPartner::findOrFail($id);
        if($discount->delete()) {
            return response()->json([
                'message' => 'Lagoon Partner deleted successfully',
                'id' => $id,
            ], 200);
        }
    }
}
