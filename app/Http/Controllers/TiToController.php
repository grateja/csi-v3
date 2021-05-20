<?php

namespace App\Http\Controllers;

use App\TiTo;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TiToController extends Controller
{
    public function index(Request $request) {
        $order = $request->orderBy ? $request->orderBy : 'desc';
        $sortBy = TiTo::filterKeys($request->sortBy);

        $result = TiTo::where(function($query) use ($request) {
            $query->where('user_name', 'like', "%$request->keyword%");
        });

        if($request->date) {
            $result = $result->whereDate('created_at', $request->date);
        }

        $result = $result->orderBy($sortBy, $order);

        return response()->json([
            'result' => $result->paginate(10),
        ]);
    }

    public function getTimeIn() {
        $tito = TiTo::where('user_id', auth('api')->id())
            ->whereDate('created_at', Carbon::today())
            ->first();

        return response()->json([
            'tito' => $tito,
        ]);
    }

    public function timeOut() {
        $tito = TiTo::where('user_id', auth('api')->id())
            ->whereDate('created_at', Carbon::today())
            ->latest('created_at')
            ->first();

        if($tito) {
            $tito->update([
                'time_out' => Carbon::now()->toDateTimeString()
            ]);
            $this->dispatch($tito->queSynch());
            return response()->json([
                'tito' => $tito
            ]);
        }
    }
}
