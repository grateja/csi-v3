<?php

namespace App\Http\Controllers;

use App\DryingService;
use App\FullServiceItem;
use App\OtherService;
use App\WashingService;
use Illuminate\Http\Request;

class FullServiceItemsController extends Controller
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
        $rules = [
            'name' => 'required',
            'quantity' => 'required|numeric',
            'price' => 'numeric',
            'points' => 'numeric',
        ];

        if($request->validate($rules)) {

            if($service = DryingService::where('name', $request->name)->first()) {
                $serviceItem = FullServiceItem::create([
                    'full_service_id' => $request->fullServiceId,
                    'category' => 'drying',
                    'drying_service_id' => $service->id,
                    'name' => $request->name,
                    'quantity' => $request->quantity,
                    'price' => $request->price,
                    'points' => $request->points,
                ]);
                $serviceItem = $serviceItem->fresh('dryingService');
            } else if($service = WashingService::where('name', $request->name)->first()) {
                $serviceItem = FullServiceItem::create([
                    'full_service_id' => $request->fullServiceId,
                    'category' => 'washing',
                    'washing_service_id' => $service->id,
                    'name' => $request->name,
                    'quantity' => $request->quantity,
                    'price' => $request->price,
                    'points' => $request->points,
                ]);
                $serviceItem = $serviceItem->fresh('washingService');
            } else if($service = OtherService::where('name', $request->name)->first()) {
                $serviceItem = FullServiceItem::create([
                    'full_service_id' => $request->fullServiceId,
                    'category' => 'other',
                    'other_service_id' => $service->id,
                    'name' => $request->name,
                    'quantity' => $request->quantity,
                    'price' => $request->price,
                    'points' => $request->points,
                ]);
                $serviceItem = $serviceItem->fresh('otherService');
            } else {
                return response()->json([
                    'errors' => [
                        'name' => ['Service name was not found']
                    ]
                ], 422);
            }

            return response()->json([
                'serviceItem' => $serviceItem,
            ]);
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
        $rules = [
            'name' => 'required',
            'quantity' => 'required|numeric',
            'price' => 'numeric',
            'points' => 'numeric',
        ];

        if($request->validate($rules)) {

            $serviceItem = FullServiceItem::findOrFail($id);

            if($service = DryingService::where('name', $request->name)->first()) {
                $serviceItem->update([
                    'category' => 'drying',
                    'washing_service_id' => null,
                    'drying_service_id' => $service->id,
                    'other_service_id' => null,
                    'name' => $request->name,
                    'quantity' => $request->quantity,
                    'price' => $request->price,
                    'points' => $request->points,
                ]);
            } else if($service = WashingService::where('name', $request->name)->first()) {
                $serviceItem->update([
                    'category' => 'washing',
                    'washing_service_id' => $service->id,
                    'drying_service_id' => null,
                    'other_service_id' => null,
                    'name' => $request->name,
                    'quantity' => $request->quantity,
                    'price' => $request->price,
                    'points' => $request->points,
                ]);
            } else if($service = OtherService::where('name', $request->name)->first()) {
                $serviceItem->update([
                    'category' => 'other',
                    'washing_service_id' => null,
                    'drying_service_id' => null,
                    'other_service_id' => $service->id,
                    'name' => $request->name,
                    'quantity' => $request->quantity,
                    'price' => $request->price,
                    'points' => $request->points,
                ]);
            } else {
                return response()->json([
                    'errors' => [
                        'name' => ['Service name was not found']
                    ]
                ], 422);
            }

            return response()->json([
                'serviceItem' => $serviceItem,
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteService($id)
    {
        $fullServiceItem = FullServiceItem::findOrFail($id);
        if($fullServiceItem->forceDelete()) {
            return response()->json([
                'fullServiceItem' => $fullServiceItem,
            ]);
        }
    }
}
