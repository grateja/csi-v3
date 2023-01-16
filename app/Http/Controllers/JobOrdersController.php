<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\JobOrderFormat;

class JobOrdersController extends Controller
{
    public function show() {
        $jobOrder = JobOrderFormat::first();

        return response()->json([
            'jobOrder' => $jobOrder,
            'example' => sprintf($jobOrder->format, $jobOrder->start_number),
        ], 200);
    }

    public function update(Request $request) {
        $jobOrder = JobOrderFormat::first();

        $rules = [
            'nextNumber' => 'required|numeric',
            'characterCount' => 'required|numeric|min:3|max:9',
        ];

        if($request->validate($rules)) {
            $jobOrder->update([
                'start_number' => $request->nextNumber,
                'prefix' => $request->prefix,
                'char_count' => $request->characterCount,
                'format' => $request->prefix . '%0' . $request->characterCount . 'd'
            ]);

            return response()->json([
                'jobOrder' => $jobOrder,
                'example' => sprintf($jobOrder->format, $jobOrder->start_number),
            ], 200);
        }
    }
}
