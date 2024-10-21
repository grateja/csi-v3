<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\OutSource;

class OutSourceController extends Controller
{
    public function index(Request $request) {
        $result = OutSource::where('company_name', 'like', "%$request->name%");

        return response()->json([
            'result' => $result->get(),
        ]);
    }

    public function store(Request $request) {
        $rules = [
            'company_name' => 'required',
        ];

        $request->validate($rules);

        $outSource = OutSource::create(
            $request->only([
                'abbr',
                'company_name',
                'address',
            ])
        );

        return response()->json([
            'outSource' => $outSource
        ]);
    }

    public function update(Request $request, $outSourceId) {
        $rules = [
            'company_name' => 'required',
        ];

        $request->validate($rules);

        $outSource = OutSource::findOrFail($outSourceId);

        $outSource->update(
            $request->only([
                'abbr',
                'company_name',
                'address',
            ])
        );

        return response()->json([
            'outSource' => $outSource
        ]);
    }

    public function delete($outSourceId) {
        $outSource = OutSource::findOrFail($outSourceId);
        $outSource->delete();

        return response()->json([
            'outSource' => $outSource,
        ]);
    }
}
