<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\Event;

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

        // $data = [
        //     'events' => $events
        // ];

        // return view('events/index', $data);
    }

    public function show($id) {
        $event = Event::with('eventType','slides', 'video')->findorFail($id);
        return response()->json([
            'event' => $event
        ], 200);
    }

    public function create() {
        // check for draft

        $event = Event::where('published', false)->first();

        $data = [
            'eventTypes' => \App\EventType::all(),
            'event' => $event,
            'action' => $event ? "/events/$event->id/update" : "/events/store",
        ];
        return view('events/create', $data);
    }

    public function store(Request $request) {
        $rules = [
            'title' => 'required',
            'date' => 'required',
        ];

        if(Event::where('date', $request->date)->exists()) {
            return response()->json(['errors' => ['date' => ['There is already an event pointed to this date.']]], 422);
            // return redirect()->back()
            //     ->withErrors(['date' => 'There is already an event pointed to this date'])
            //     ->withInput($request->input());
        }

        if($request->validate($rules)) {
            $event = Event::create($request->only([
                'title', 'description', 'date', 'event_type_id'
            ]));
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
            'date' => 'required',
        ];

        // check if date changed
        $event = Event::findorFail($id);

        if($event->date != $request->date) {
            if(Event::where('date', $request->date)->exists()) {
                return response()->json(['errors' => ['date' => ['There is already an event pointed to this date.']]], 422);
            }
        }

        if($request->validate($rules)) {
            $event->update($request->only([
                'title', 'description', 'date', 'event_type_id'
            ]));
            return response()->json(
                $event->fresh('eventType', 'slides'), 200
            );
        }

        // return redirect("/boards/{$event->eventType->name}/$event->id/create");
    }

    public function delete($id) {
        $event = Event::with('slides')->findorFail($id);

        foreach($event->slides as $slide) {
            File::delete(public_path() . $slide->source);
        }

        if($event->delete()) {
            return response()->json([
                'message' => 'Event deleted successfully',
                'eventId' => $id
            ], 200);
            // return redirect()->back();
        }
    }
}
