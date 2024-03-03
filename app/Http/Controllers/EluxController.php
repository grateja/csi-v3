<?php

namespace App\Http\Controllers;

use App\EluxService;
use Illuminate\Http\Request;

class EluxController extends Controller
{
    public function index($serviceType) {
        $result = EluxService::where('service_type', $serviceType)->get();
        return response()->json([
            'result' => $result,
        ]);
    }

    public function store(Request $request) {
        $rules = [
            'name' => 'required|unique:elux_services,name,NULL,id,deleted_at,NULL',
            'price' => 'numeric',
            'serviceType' => 'required|in:washer,dryer',
            'model' => 'required',
        ];

        if($request->validate($rules)) {
            $service = EluxService::withTrashed()->where([
                'name' => $request->name,
            ])->first();

            if($service) {
                $service->update([
                    'name' => $request->name,
                    'service_type' => $request->serviceType,
                    'model' => $request->model,
                    'pulse_count' => $request->pulseCount,
                    'minutes' => $request->minutes,
                    'price' => $request->price,
                    'deleted_at' => null,
                ]);
            } else {
                $service = EluxService::create([
                    'name' => $request->name,
                    'service_type' => $request->serviceType,
                    'model' => $request->model,
                    'pulse_count' => $request->pulseCount,
                    'minutes' => $request->minutes,
                    'price' => $request->price,
                ]);
            }

            $this->dispatch($service->queSynch());

            return response()->json([
                'service' => $service,
                'success' => 'Service saved successfully',
            ]);
        }
    }

    public function update(Request $request, $id) {
        $rules = [
            'name' => 'required',
            'price' => 'numeric',
            'serviceType' => 'required|in:washer,dryer',
            'model' => 'required',
        ];

        $service = EluxService::findOrFail($id);

        if($service->name != $request->name) {
            $rules['name'] = 'required|unique:elux_services,name,NULL,id,deleted_at,NULL';
        }

        if($request->validate($rules)) {
            if($service) {
                $service->update([
                    'name' => $request->name,
                    'service_type' => $request->serviceType,
                    'model' => $request->model,
                    'pulse_count' => $request->pulseCount,
                    'minutes' => $request->minutes,
                    'price' => $request->price,
                    'deleted_at' => null,
                ]);
            }

            $this->dispatch($service->queSynch());

            return response()->json([
                'service' => $service,
                'success' => 'Service saved successfully',
            ]);
        }
    }

    public function deleteService($id) {
        $eluxService = EluxService::findOrFail($id);
        if($eluxService->delete()) {
            $eluxService->eluxServiceTransactionItems()->where('saved', false)->delete();

            $this->dispatch($eluxService->queSynch());

            return response()->json([
                'service' => $eluxService,
            ]);
        }
    }
}
