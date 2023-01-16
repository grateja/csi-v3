<?php

namespace App\Http\Controllers;

use App\PerKiloWashDry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PerKiloServicesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $result = PerKiloWashDry::where('name', 'like', "%$request->keyword%")->orderBy('name')->get();

        return response()->json([
            'result' => $result,
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
            'name' => 'required|unique:other_services,name,NULL,id,deleted_at,NULL',
            'delicate_price' => 'numeric|nullable',
            'warm_price' => 'numeric|nullable',
            'hot_price' => 'numeric|nullable',
            'superwash_price' => 'numeric|nullable',
        ];
        if($request->validate($rules)) {
            return 
            DB::transaction(function () use ($request) {
                $service = PerKiloWashDry::create([
                    'name' => $request->name,
                    'delicate_price' => $request->delicatePrice,
                    'warm_price' => $request->warmPrice,
                    'hot_price' => $request->hotPrice,
                    'superwash_price' => $request->superwashPrice,
                ]);

                $this->dispatch($service->queSynch());

                return response()->json([
                    'service' => $service,
                    'success' => 'Service saved successfully',
                ]);
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
        //
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
