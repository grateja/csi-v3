<?php

namespace App\Http\Controllers;

use App\DryingService;
use App\OtherService;
use App\WashingService;
use Illuminate\Http\Request;

class ServicesController extends Controller
{
    public function index(Request $request) {

        $result = [];

        if($request->keyword) {
            $washingServices = WashingService::where('name', 'like', "%$request->keyword%")->orderBy('name')->get();
            $dryingServices = DryingService::where('name', 'like', "%$request->keyword%")->orderBy('name')->get();
            $otherServices = OtherService::where('name', 'like', "%$request->keyword%")->orderBy('name')->get();

            $result = array_merge($washingServices->transform(function($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'description' => $item->description,
                    'price' => $item->price,
                    'category' => 'washing',
                    'points' => $item->points,
                ];
            })->toArray(), $dryingServices->transform(function($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'description' => $item->description,
                    'price' => $item->price,
                    'category' => 'drying',
                    'points' => $item->points,
                ];
            })->toArray(), $otherServices->transform(function($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'description' => $item->description,
                    'price' => $item->price,
                    'category' => 'other',
                    'points' => $item->points,
                ];
            })->toArray());
        }

        return response()->json([
            'result' => $result
        ]);
    }
}
