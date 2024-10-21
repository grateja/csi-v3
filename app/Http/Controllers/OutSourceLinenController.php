<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\OutSourceLinen;

class OutSourceLinenController extends Controller
{
    public function index(Request $request, $outSourceId) {
        $result = OutSourceLinen::where('out_source_id', $outSourceId)
            ->where('name', 'like', "%$request->name%");

        return response()->json([
            'result' => $result->get(),
        ]);
    }

    public function store(Request $request) {
        $rules = [
            'name' => 'required',
            'out_source_id' => 'required',
            'regular_price' => 'required',
            'with_stain_light' => 'required|numeric',
            'with_stain_medium' => 'required|numeric',
            'with_stain_heavy' => 'required|numeric',
        ];

        $request->validate($rules);

        $outSourceLinen = OutSourceLinen::create(
            $request->only([
                'out_source_id',
                'category',
                'name',
                'regular_price',
                'with_stain_light',
                'with_stain_medium',
                'with_stain_heavy',
                'dry_weight',
            ])
        );

        return response()->json([
            'linen' => $outSourceLinen
        ]);
    }

    public function update(Request $request, $outSourceLinenId) {
        $rules = [
            'name' => 'required',
            'out_source_id' => 'required',
            'regular_price' => 'required',
            'with_stain_light' => 'required|numeric',
        ];

        $request->validate($rules);

        $outSourceLinen = OutSourceLinen::findOrFail($outSourceLinenId);

        $outSourceLinen->update(
            $request->only([
                'out_source_id',
                'category',
                'name',
                'regular_price',
                'with_stain_light',
                'with_stain_medium',
                'with_stain_heavy',
                'dry_weight',
            ])
        );

        return response()->json([
            'linen' => $outSourceLinen
        ]);
    }

    public function delete($outSourceLinenId) {
        $outSourceLinen = OutSourceLinen::findOrFail($outSourceLinenId);
        $outSourceLinen->delete();

        return response()->json([
            'linen' => $outSourceLinen
        ]);
    }

    public function categories() {
        $result = OutSourceLinen::distinct('category')->pluck('category');
        return response()->json($result);
    }
}
