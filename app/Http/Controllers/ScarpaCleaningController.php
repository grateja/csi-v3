<?php

namespace App\Http\Controllers;

use App\ScarpaCategory;
use App\ScarpaVariation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ScarpaCleaningController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $result = ScarpaCategory::where('name', 'like', "%$request->keyword%")
            ->orderBy('name')->get();

        return response()->json([
            'result' => $result
        ], 200);
    }

    public function setPicture($id, Request $request) {
        if($request->hasFile('file')) {

            $service = ScarpaCategory::findOrFail($id);
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
        $product = ScarpaCategory::findOrFail($id);
        File::delete(public_path() . $product->img_path);
        $product->update([
            'img_path' => ''
        ]);
        return response()->json([
            'message' => ['Picture removed']
        ]);
    }

    public function variations($serviceId) {
        $variations = ScarpaVariation::where('scarpa_category_id', $serviceId)
            ->orderBy('action')
            ->get();

        return response()->json([
            'variations' => $variations,
        ]);
    }

    public function addVariation($serviceId, Request $request) {
        $rules = [
            'action' => 'required',
            'sellingPrice' => 'required|numeric',
        ];

        if($request->validate($rules)) {
            $variation = ScarpaVariation::where([
                'action' => $request->action,
                'scarpa_category_id' => $serviceId
            ])->first();
            if($variation) {
                return response()->json([
                    'errors' => [
                        'action' => ['"' . $variation->action . '" already exists']
                    ]
                ], 422);
            }
            return DB::transaction(function () use ($serviceId, $request) {
                $variation = ScarpaVariation::create([
                    'scarpa_category_id' => $serviceId,
                    'action' => $request->action,
                    'selling_price' => $request->sellingPrice,
                ]);

                return response()->json([
                    'variation' => $variation,
                    'success' => 'Variation added'
                ]);
            });
        }
    }

    public function updateVariation($serviceId, $variationId, Request $request) {
        $rules = [
            'action' => 'required',
            'sellingPrice' => 'required|numeric',
        ];

        if($request->validate($rules)) {
            $variation = ScarpaVariation::findOrFail($variationId);
            if($request->color != $variation->color || $request->action != $variation->action) {
                $dup = ScarpaVariation::where([
                    'action' => $request->action,
                    'scarpa_category_id' => $serviceId
                ])->first();

                if($dup) {
                    return response()->json([
                        'errors' => [
                            'action' => ['"'.$request->action.'" already exists']
                        ]
                    ], 422);
                }
            }
            return DB::transaction(function () use ($request, $variation) {
                $variation->update([
                    'action' => $request->action,
                    'selling_price' => $request->sellingPrice,
                ]);

                return response()->json([
                    'variation' => $variation,
                    'success' => 'Variation added'
                ]);
            });
        }
    }

    public function deleteVariation($variationId) {
        $variation = ScarpaVariation::findOrFail($variationId);
        if($variation->delete()) {
            return response()->json([
                'variation' => $variation,
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
            'name' => 'required|unique:other_services,name,NULL,id,deleted_at,NULL',
            'price' => 'numeric',
        ];

        if($request->validate($rules)) {

            return DB::transaction(function () use ($request) {
                $service = ScarpaCategory::withTrashed()->where([
                    'name' => $request->name,
                ])->first();

                if($service) {
                    $service->update([
                        'name' => $request->name,
                        'description' => $request->description,
                        'img_path' => null,
                        'deleted_at' => null,
                    ]);
                } else {
                    $service = ScarpaCategory::create([
                        'name' => $request->name,
                        'description' => $request->description,
                        'img_path' => null,
                    ]);
                }

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
        $rules = [
            'name' => 'required',
            'price' => 'numeric',
        ];
        $service = ScarpaCategory::findOrFail($id);

        if($service->name != $request->name) {
            $rules['name'] = 'required|unique:other_services,name,NULL,id,deleted_at,NULL';
        }

        if($request->validate($rules)) {
            return DB::transaction(function () use ($service, $request) {

                $service->update([
                    'name' => $request->name,
                    'description' => $request->description,
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
