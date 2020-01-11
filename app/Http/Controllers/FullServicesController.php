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
            return response()->json([
                'service' => $service,
            ]);
        }
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required',
            'additionalCharge' => 'numeric',
            'discount' => 'numeric',
        ];

        if($request->validate($rules)) {
            $service = FullService::create([
                'name' => $request->name,
                'additional_charge' => $request->additionalCharge,
                'discount' => $request->discount,
            ]);

            return response()->json([
                'service' => $service->fresh('fullServiceItems', 'fullServiceProducts'),
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

        if($request->validate($rules)) {
            $service = FullService::findOrFail($id);
            $service->update([
                'name' => $request->name,
                'additional_charge' => $request->additionalCharge,
                'discount' => $request->discount,
            ]);

            return response()->json([
                'service' => $service->fresh('fullServiceItems'),
            ]);
        }
    }
}
