<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Service;

class ServicesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($userId, Request $request)
    {
        $services = Service::where(function($query) use ($request) {
            $query->where('name', 'like', "%$request->keyword%");
        })->paginate(10);

        return response()->json([
            'result' => $services,
        ], 200);
    }

    public function posIndex($userId, Request $request) {
        $userId = $userId == 'self' ? auth('api')->id() : $userId;

        $services = Service::where(function($query) use ($request) {
            $query->where('name', 'like', "%$request->keyword%");
        })->orderBy('name')->get();

        return response()->json([
            'result' => $services,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($clientId, Request $request)
    {
        $rules = [
            'serviceType' => 'required',
            'name' => 'required|unique:services',
            'pulseCount' => 'numeric|nullable',
        ];

        if($request->validate($rules)) {
            return DB::transaction(function () use ($request, $clientId) {
                $service = Service::create([
                    'name' => $request->name,
                    'description' => $request->description,
                    'barcode' => $request->barcode,
                    'img_path' => $request->imgPath,
                    'points' => $request->points,
                    'price' => $request->price,
                    'pulse_count' => $request->pulseCount,
                ]);

                return response()->json([
                    'service' => $service,
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
        $service = Service::findOrFail($id);

        return response()->json([
            'service' => $service,
        ], 200);
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
        $service = Service::findOrFail($id);

        $rules = [
            'name' => 'required',
            'serviceType' => 'required',
        ];

        if($request->name != $service->name) {
            $rules['name'] = 'required|unique:services';
        }

        if($request->validate($rules)) {
            return DB::transaction(function () use ($service, $request) {
                $service->update([
                    'name' => $request->name,
                    'description' => $request->description,
                    'barcode' => $request->barcode,
                    'img_path' => $request->imgPath,
                    'points' => $request->points,
                    'price' => $request->price,
                    'pulse_count' => $request->pulseCount,
                ]);

                return response()->json([
                    'service' => $service,
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
