<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Event;
use App\SysDefault;

class BoardsController extends Controller
{
    public function getBoardToday() {
        $event = Event::with('eventType', 'slides', 'video')
            ->whereDate('date', Carbon::now())
            ->first();

        if($event == null) {
            $event = SysDefault::with(['event' => function($query) {
                $query->with('slides');
            }])->find(1)->event;
        }
        return response()->json([
            'event' => $event
        ], 200);
    }
}
