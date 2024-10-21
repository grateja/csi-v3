<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\OutSourceService;

class OutSourceServicesController extends Controller
{
    public function index() {
        $result = OutSourceService::all();

        return response()->json([
            'result' => $result
        ]);
    }

    public function create(Request $request) {
        $rules = [
            'name' => 'required',
            'pulse_count' => 'required|numeric',
            'minutes' => 'required|numeric',
        ];

        $request->validate($rules);

        $service = OutSourceService::create($request->only([
            'name', 'pulse_count', 'description', 'minutes'
        ]));

        return response()->json([
            'service' => $service,
        ]);
    }

    public function update(Request $request, $serviceId) {
        $rules = [
            'name' => 'required',
            'pulse_count' => 'required|numeric',
            'minutes' => 'required|numeric',
        ];

        $request->validate($rules);

        $service = OutSourceService::findOrFail($serviceId);

        $service->update($request->only([
            'name', 'pulse_count', 'description', 'minutes'
        ]));

        return response()->json([
            'service' => $service,
        ]);
    }

    public function delete($serviceId) {
        $service = OutSourceService::findOrFail($serviceId);
        $service->delete();
        return response()->json([
            'service' => $service,
        ]);
    }
}
