<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\Event;
use App\MonitorChecker;
use App\Slide;
use App\SysDefault;
use Illuminate\Support\Facades\DB;

class EventsController extends Controller
{
    public function index(Request $request) {
        $events = Event::with(['eventType', 'slides' => function($query) {
            $query->where('order', 1);
        }])
            ->orderBy('date_from', 'desc')
            ->where('title', 'like', "%$request->keyword%")
            ->paginate();

        return response()->json($events, 200);
    }

    public function show($id) {
        $event = Event::with('eventType','slides', 'video')->findorFail($id);
        return response()->json([
            'event' => $event
        ], 200);
    }

    public function store(Request $request) {
        $rules = [
            'title' => 'required',
            'dateFrom' => 'required',
            'dateUntil' => 'required|date|after_or_equal:dateFrom',
        ];

        // $eventConflict = Event::where(function($query) use ($request) {
        //     $query->whereDate('date_from', '<=', $request->dateFrom)
        //         ->whereDate('date_until', '>=', $request->dateFrom);
        // })->where(function($query) use ($request) {
        //     $query->whereDate('date_from', '<=', $request->dateUntil)
        //         ->whereDate('date_until', '>=', $request->dateUntil);
        // })->get();
        // return response()->json([$eventConflict], 422);

        if(Event::whereBetween('date_from', [$request->dateFrom, $request->dateUntil])
            ->orwhereBetween('date_until',[$request->dateFrom, $request->dateUntil])->exists()) {
                return response()->json(['errors' => ['dateFrom' => ['There is already an event pointed to this date.']]], 422);
            // return redirect()->back()
            //     ->withErrors(['date' => 'There is already an event pointed to this date'])
            //     ->withInput($request->input());
        }

        if($request->validate($rules)) {
            // $event = Event::create($request->only([
            //     'title', 'description', 'date_from', 'date_until', 'event_type_id'
            // ]));
            $event = Event::create([
                'title' => $request->title,
                'description' => $request->description,
                'date_from' => $request->dateFrom,
                'date_until' => $request->dateUntil,
                'event_type_id' => $request->eventTypeId,
            ]);
        }

        return response()->json($event, 200);
        // return redirect("/boards/{$event->eventType->name}/$event->id/create");
    }

    public function edit($id) {
        $event = Event::findorFail($id);

        return view('events/create', $event);
    }

    public function update(Request $request, $id = null) {
        $rules = [
            'title' => 'required',
            'dateFrom' => 'required',
            'dateUntil' => 'required|date|after_or_equal:dateFrom',
        ];

        // check if date changed
        $event = Event::findorFail($id);

        if(Event::where('id', '<>', $id)->where(function($query) use ($request) {
            $query->whereBetween('date_from', [$request->dateFrom, $request->dateUntil])
                ->orwhereBetween('date_until',[$request->dateFrom, $request->dateUntil]);
        })->exists()) {
            return response()->json(['errors' => ['dateFrom' => ['There is already an event pointed to this date.']]], 422);
        }

        if($request->validate($rules)) {
            $event->update([
                'title' => $request->title,
                'description' => $request->description,
                'date_from' => $request->dateFrom,
                'date_until' => $request->dateUntil,
                'event_type_id' => $request->eventTypeId,
            ]);

            MonitorChecker::idle();

            return response()->json(
                $event->fresh('eventType', 'slides'), 200
            );
        }
    }

    public function delete($id) {
        if(SysDefault::where('event_id', $id)->exists()) {
            return response()->json([
                'errors' => [
                    'message' => ['Cannot delete default event']
                ]
            ], 422);
        }

        $event = Event::with('slides')->findorFail($id);

        foreach($event->slides as $slide) {
            File::delete(public_path() . $slide->source);
        }

        MonitorChecker::refreshToken();

        if($event->delete()) {
            return response()->json([
                'message' => 'Event deleted successfully',
                'eventId' => $id
            ], 200);
            // return redirect()->back();
        }
    }

    public function removeSlide($slideId) {
        $slide = Slide::findOrFail($slideId);
        if($slide->delete()) {
            File::delete(public_path() . $slide->source);
        }
        MonitorChecker::idle();
        return response()->json([
            'message' => ['Slide removed']
        ]);
    }

    public function setDefault($eventId) {
        $sysDefault = SysDefault::first();
        $sysDefault->update([
            'event_id' => $eventId
        ]);
        MonitorChecker::idle();
        return response()->json([
            'message' => ['Default event updated']
        ]);
    }
}