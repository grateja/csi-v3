<?php

namespace App\Http\Controllers;

use App\StoreHours;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class StoreHoursController extends Controller
{
    public function index() {
        $result = StoreHours::orderBy('day_index')->get();

        if(!$result->count()) {
            Artisan::call('db:seed --class=StoreHoursSeeder');
            $result = StoreHours::orderBy('day_index')->get();
            foreach($result as $item) {
                $this->dispatch($item->queSynch());
            }
        }

        return response()->json([
            'result' => $result
        ]);
    }

    public function update(Request $request, $id) {
        $rules = [
            'opensAt' => 'required',
            'closesAt' => 'required',
        ];

        if($request->validate($rules)) {
            return DB::transaction(function () use ($id, $request) {
                $storeHour = StoreHours::findOrFail($id);
                $storeHour->update([
                    'opens_at' => $request->opensAt,
                    'closes_at' => $request->closesAt,
                ]);

                $this->dispatch($storeHour->queSynch());

                return response()->json([
                    'storeHour' => $storeHour,
                ]);
            });
        }
    }
}
