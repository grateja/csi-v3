<?php

namespace App\Http\Controllers;

use App\FullService;
use Illuminate\Http\Request;

class FullServicesController extends Controller
{
    public function index(Request $request) {
        $services = FullService::with('fullServiceItems', 'fullServiceProducts')
            ->where('name', 'like', "%$request->keyword%")->orderBy('name')->get();
        return response()->json([
            'result' => $services
        ]);
    }

    public function deleteService($id) {
        $service = FullService::findOrFail($id);
        if($service->delete()) {
            $this->dispatch($service->queSynch());
            return response()->json([
                'service' => $service,
            ]);
        }
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|unique:full_services,name,NULL,id,deleted_at,NULL',
            'additionalCharge' => 'numeric',
            'discount' => 'numeric',
        ];

        if($request->validate($rules)) {
            $service = FullService::withTrashed()->where([
                'name' => $request->name,
            ])->first();
            if($service) {
                $service->update([
                    'name' => $request->name,
                    'additional_charge' => $request->additionalCharge,
                    'discount' => $request->discount,
                    'deleted_at' => null,
                ]);
                $service->fullServiceItems()->forceDelete();
                $service->fullServiceProducts()->forceDelete();
            } else {
                $service = FullService::create([
                    'name' => $request->name,
                    'additional_charge' => $request->additionalCharge,
                    'discount' => $request->discount,
                ]);
            }

            $this->dispatch($service->queSynch());

            return response()->json([
                'service' => $service->fresh('fullServiceItems', 'fullServiceProducts'),
                'success' => 'Service saved successfully',
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'name' => 'required',
            'additionalCharge' => 'numeric',
            'discount' => 'numeric',
        ];
        $service = FullService::findOrFail($id);
        if($service->name != $request->name) {
            $rules['name'] = 'required|unique:full_services,name,NULL,id,deleted_at,NULL';
        }

        if($request->validate($rules)) {
            $service->update([
                'name' => $request->name,
                'additional_charge' => $request->additionalCharge,
                'discount' => $request->discount,
            ]);

            $this->dispatch($service->queSynch());

            return response()->json([
                'service' => $service->fresh('fullServiceItems'),
            ]);
        }
    }
}
