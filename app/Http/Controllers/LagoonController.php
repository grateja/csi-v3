<?php

namespace App\Http\Controllers;

use App\Lagoon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class LagoonController extends Controller
{
    public function index(Request $request) {
        $result = Lagoon::where('name', 'like', "%$request->keyword%")
            ->orderBy('name')
            ->get();

        return response()->json([
            'result' => $result->groupBy('category'),
        ]);
    }

    public function setPicture($id, Request $request) {
        if($request->hasFile('file')) {

            $service = Lagoon::findOrFail($id);
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
        $product = Lagoon::findOrFail($id);
        File::delete(public_path() . $product->img_path);
        $product->update([
            'img_path' => ''
        ]);
        return response()->json([
            'message' => ['Picture removed']
        ]);
    }

    public function deleteService($id) {
        $service = Lagoon::findOrFail($id);
        if($service->delete()) {
            File::delete(public_path() . $service->img_path);
            return response()->json([
                'service' => $service,
            ]);
        }
    }
    public function store(Request $request)
    {
        $rules = [
            // 'name' => 'required|unique:lagoons,name,NULL,id,deleted_at,NULL',
            'price' => 'numeric',
        ];

        $service = Lagoon::withTrashed()->where([
            'name' => $request->name,
            'category' => $request->category,
        ])->whereNull('deleted_at')->first();

        if($service != null && $service->deleted_at == null) {
            return response()->json([
                'errors' => [
                    'name' => ['Service with the same name and category already exists']
                ]
            ], 422);
        }

        if($request->validate($rules)) {
            $service = Lagoon::withTrashed()->where([
                'name' => $request->name,
            ])->first();

            if($service) {
                $service->update([
                    'name' => $request->name,
                    'category' => $request->category,
                    'price' => $request->price,
                    'img_path' => null,
                    'deleted_at' => null,
                ]);
            } else {
                $service = Lagoon::create([
                    'name' => $request->name,
                    'category' => $request->category,
                    'price' => $request->price,
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
            'price' => 'numeric',
        ];

        $service = Lagoon::withTrashed()->where([
            'name' => $request->name,
            'category' => $request->category,
        ])->first();

        if($service != null && ($service->name != $request->name || $service->category != $request->category) && $service->deleted_at == null) {
            return response()->json([
                'errors' => [
                    'name' => ['Service with the same name and category already exists']
                ]
            ], 422);
        }

        $service = Lagoon::withTrashed()->findOrFail($id);

        // if($service->name != $request->name) {
            // $rules['name'] = 'required|unique:lagoons,name,NULL,id,deleted_at,NULL';
        // }

        if($request->validate($rules)) {
            $service->update([
                'name' => $request->name,
                'category' => $request->category,
                'price' => $request->price,
            ]);

            return response()->json([
                'service' => $service,
                'success' => 'Service saved successfully',
            ]);
        }
    }
}
