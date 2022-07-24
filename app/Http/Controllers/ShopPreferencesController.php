<?php

namespace App\Http\Controllers;

use App\StoreHours;
use Illuminate\Http\Request;

class ShopPreferencesController extends Controller
{
    public function storeHours() {
        $result = StoreHours::orderBy('day_index')->get();

        return response()->json([
            'result' => $result
        ]);
    }
}
