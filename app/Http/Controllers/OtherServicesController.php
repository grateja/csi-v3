<?php

namespace App\Http\Controllers;

use App\OtherService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class OtherServicesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $dryingServices = OtherService::where('name', 'like', "%$request->keyword%")->orderBy('name')->get();

        return response()->json([
            'result' => $dryingServices,
        ]);
    }

    public function setPicture($id, Request $request) {
        if($request->hasFile('file')) {

            $service = OtherService::findOrFail($id);
            File::delete(public_path() . $service->img_path);

            $extension = $request->file('file')->getClientOriginalExtension();
            $name = str_random() . '.' . $extension;

            $path = '/img/services/';
            $source = $path . $name;
            $request->file('file')->move(public_path() . $path, $name);

            $service->update([
                'img_path' => $source
            ]);

            return response()->json([
                'img_path' => $source
            ]);
        }
        return response()->json([
            'errors' => [
                'message' => ['No File selected']
            ]
        ], 422);
    }

    public function removePicture($id) {
        $product = OtherService::findOrFail($id);
        File::delete(public_path() . $product->img_path);
        $product->update([
            'img_path' => ''
        ]);
        return response()->json([
            'message' => ['Picture removed']
        ]);
    }

    public function deleteService($id) {
        $service = OtherService::findOrFail($id);
        if($service->delete()) {
            File::delete(public_path() . $service->img_path);
            return response()->json([
                'service' => $service,
            ]);
        }
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
            'price' => 'numeric',
        ];

        if($request->validate($rules)) {
            $service = OtherService::create([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'img_path' => null,
            ]);

            return response()->json([
                'service' => $service,
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
            'price' => 'numeric',
        ];

        if($request->validate($rules)) {
            $service = OtherService::findOrFail($id);
            $service->update([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
            ]);

            return response()->json([
                'service' => $service,
            ]);
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
