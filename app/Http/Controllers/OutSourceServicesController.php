<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\OutSourceService;

class OutSourceServicesController extends Controller
{
    public function index(Request $request) {
        $result = OutSourceService::where(function(){});

        if($request->serviceType) {
            $result = $result->where('service_type', $request->serviceType);
        }

        return response()->json([
            'result' => $result->get()
        ]);
    }

    public function create(Request $request) {
        $rules = [
            'name' => 'required',
            'pulse_count' => 'required|numeric',
            'minutes' => 'required|numeric',
            'service_type' => 'required',
        ];

        $request->validate($rules);

        $service = OutSourceService::create($request->only([
            'name', 'pulse_count', 'description', 'minutes', 'service_type',
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
            'service_type' => 'required',
        ];

        $request->validate($rules);

        $service = OutSourceService::findOrFail($serviceId);

        $service->update($request->only([
            'name', 'pulse_count', 'description', 'minutes', 'service_type'
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
