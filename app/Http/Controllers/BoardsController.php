<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Event;
use App\SysDefault;

class BoardsController extends Controller
{
    public function getBoardToday() {
        $event = Event::with('eventType', 'slides', 'video', 'audio')
            ->whereDate('date_from', '<=', Carbon::now())
            ->whereDate('date_until', '>=', Carbon::now())
            ->first();

        if($event == null) {
            $event = SysDefault::with(['event' => function($query) {
                $query->with('slides', 'video', 'audio');
            }])->first()->event;
        }
        return response()->json([
            'event' => $event
        ], 200);
    }
}
