<?php

namespace App\Http\Controllers;

use App\LagoonPerKilo;
use Illuminate\Http\Request;

class LagoonPerKiloController extends Controller
{
    public function index(Request $request) {
        $result = LagoonPerKilo::where('name', 'like', "%$request->keyword%")
            ->orderBy('name')
            ->get();

        return response()->json([
            'result' => $result,
        ]);
    }

    public function deleteService($id) {
        $service = LagoonPerKilo::findOrFail($id);
        if($service->delete()) {
            return response()->json([
                'service' => $service,
            ]);
        }
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|unique:lagoons,name,NULL,id,deleted_at,NULL',
            'price_per_kilo' => 'numeric|min:0',
        ];

        if($request->validate($rules)) {
            $service = LagoonPerKilo::withTrashed()->where([
                'name' => $request->name,
            ])->first();

            if($service) {
                $service->update([
                    'name' => $request->name,
                    'price_per_kilo' => $request->price_per_kilo,
                    'img_path' => null,
                    'deleted_at' => null,
                ]);
            } else {
                $service = LagoonPerKilo::create([
                    'name' => $request->name,
                    'price_per_kilo' => $request->price_per_kilo,
                    'img_path' => null,
                ]);
            }

            return response()->json([
                'service' => $service,
                'success' => 'Service saved successfully',
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'name' => 'required',
            'price_per_kilo' => 'numeric|min:0',
        ];
        $service = LagoonPerKilo::findOrFail($id);

        if($service->name != $request->name) {
            $rules['name'] = 'required|unique:lagoons,name,NULL,id,deleted_at,NULL';
        }

        if($request->validate($rules)) {
            $service->update([
                'name' => $request->name,
                'price_per_kilo' => $request->price_per_kilo,
                'price_per_kilo' => $request->price_per_kilo,
            ]);

            return response()->json([
                'service' => $service,
                'success' => 'Service saved successfully',
            ]);
        }
    }
}
