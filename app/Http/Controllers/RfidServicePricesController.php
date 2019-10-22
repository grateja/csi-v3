<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\RfidServicePrice;
use Illuminate\Support\Facades\DB;
use App\ClientAuth;

class RfidServicePricesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $servicePrices = RfidServicePrice::with('machineType')->get();

        return response()->json([
            'servicePrices' => $servicePrices,
        ], 200);
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
        //
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
    public function update(Request $request, $servicePriceId)
    {
        $rules = ['enabled' => 'required'];
        if($request->enabled) {
            $rules = [
                'name' => 'required',
                'price' => 'required',
                'minutes' => 'required',
            ];
        }

        if($request->validate($rules)) {
            return DB::transaction(function () use ($servicePriceId, $request) {
                $servicePrice = RfidServicePrice::findOrFail($servicePriceId);

                if(!$request->enabled) {
                    $servicePrice->update([
                        'enabled' => false,
                    ]);
                } else {
                    $servicePrice->update([
                        'name' => $request->name,
                        'price' => $request->price,
                        'minutes' => $request->minutes,
                        'enabled' => true,
                    ]);
                }

                return response()->json([
                    'servicePrice' => $servicePrice,
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
