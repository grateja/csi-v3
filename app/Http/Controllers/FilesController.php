<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;
use App\Slide;
use App\Event;
use App\MonitorChecker;
use App\Video;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class FilesController extends Controller
{
    public function uploadSlides(Request $request, $eventId) {
        if($request->hasFile('files')) {

            $data = [];

            $event = Event::findorFail($eventId);

            for ($i=0; $i < count($request->file('files')); $i++) {
                $extension = $request->file('files')[$i]->getClientOriginalExtension();
                $name = str_random() . '.' . $extension;

                $path = '/img/' . date('Y-m-d') . '/';
                $source = $path . $name;

                // $source = '/img/' . date('Y-m-d') . '/' . $name;
                // $path = public_path() . $source;
                $request->file('files')[$i]->move(public_path() . $path, $name);
                $data[] = [
                    'id' => Str::uuid(),
                    'event_id' => $eventId,
                    'source' => $source,
                    'order' => $request->order[$i]
                ];
            }

            DB::transaction(function () use ($data, $event) {
                $slides = Slide::insert($data);
                $event->update([
                    'updated_at' => Carbon::now(),
                    'event_type_id' => 1,
                ]);
            });


            MonitorChecker::idle();

            return response()->json([
                'event' => $event->fresh('slides')
            ], 200);
        }

        return response()->json(['no files'], 422);
    }

    public function changePicture(Request $request, $slideId) {
        if($request->hasFile('file')) {
            $slide = Slide::findOrFail($slideId);

            $extension = $request->file('file')->getClientOriginalExtension();
            $name = str_random() . '.' . $extension;
            $path = '/img/' . date('Y-m-d') . '/';
            $source = $path . $name;

            $request->file('file')->move(public_path() . $path, $name);

            File::delete(public_path() . $slide->source);
            $slide->update([
                'source' => $source,
            ]);

            MonitorChecker::idle();

            return response()->json([
                'slide' => $slide,
            ]);
        } else {
            return response()->json([
                'errors' => [
                    'message' => ['No file selected']
                ]
            ], 422);
        }
    }

    public function uploadVideo(Request $request, $eventId) {
        if($request->hasFile('file')) {
            $event = Event::findorFail($eventId);
            $extension = $request->file('file')->getClientOriginalExtension();
            $name = str_random() . '.' . $extension;

            $path = '/vid/' . date('Y-m-d') . '/';
            $source = $path . $name;

            $request->file('file')->move(public_path() . $path, $name);

            $data = [
                'name' => $name,
                'path' => $path,
                'source' => $source
            ];

            return DB::transaction(function () use ($eventId, $source, $event) {
                $video = Video::create([
                    'event_id' => $eventId,
                    'source' => $source
                ]);

                $event->update([
                    'updated_at' => Carbon::now(),
                    'event_type_id' => 2,
                ]);

                MonitorChecker::idle();

                return response()->json([
                    'video' => $video
                ], 200);
            });
        }

        return response()->json([
            'message' => 'No file'
        ], 422);
    }
}
