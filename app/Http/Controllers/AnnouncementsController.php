<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Announcement;
use App\MonitorChecker;
use Carbon\Carbon;
use App\SysDefault;

class AnnouncementsController extends Controller
{

    public function index(Request $request) {
        $announcements = Announcement::orderBy('date_from', 'desc')
            ->where('content', 'like', "%$request->keyword%")
            ->paginate(10);

        return response()->json([
            'result' => $announcements
        ], 200);
    }

    public function getAnnouncement() {
        $announcement = Announcement::orderBy('id', 'desc')
            ->whereDate('date_from', '>=', Carbon::now())
            ->first();

        if($announcement == null) {
            $announcement = SysDefault::first()->announcement;
        }

        return response()->json([
            'announcement' => $announcement
        ], 200);
    }

    public function store(Request $request) {
        $rules = [
            'content' => 'required',
            'dateFrom' => 'required',
            'dateUntil' => 'required|date|after_or_equal:dateFrom',
        ];

        if(Announcement::whereBetween('date_from', [$request->dateFrom, $request->dateUntil])
            ->orwhereBetween('date_until',[$request->dateFrom, $request->dateUntil])->exists()) {
                return response()->json(['errors' => ['dateFrom' => ['There is already an announcement pointed to this date.']]], 422);
            // return redirect()->back()
            //     ->withErrors(['date' => 'There is already an event pointed to this date'])
            //     ->withInput($request->input());
        }

        if($request->validate($rules)) {
            // $_announcement = Announcement::whereDate('date', $request->date)
            //     ->first();

            // if($_announcement != null) {
            //     return response()->json(['errors' => ['date' => ['There is already an announcement pointed to this date.']]], 422);
            // }

            $announcement = Announcement::create([
                'content' => $request->content,
                'date_from' => $request->dateFrom,
                'date_until' => $request->dateUntil,
                'marquee_on' => $request->marqueeOn,
            ]);

            MonitorChecker::refreshToken();

            return response()->json(['announcement' => $announcement], 200);
        }
    }

    public function update($id, Request $request) {
        $announcement = Announcement::findorFail($id);

        $rules = [
            'content' => 'required',
            'dateFrom' => 'required',
            'dateUntil' => 'required|date|after_or_equal:dateFrom',
        ];

        if(Announcement::where('id', '<>', $id)->where(function($query) use ($request) {
            $query->whereBetween('date_from', [$request->dateFrom, $request->dateUntil])
                ->orwhereBetween('date_until',[$request->dateFrom, $request->dateUntil]);
        })->exists()) {
            return response()->json(['errors' => ['dateFrom' => ['There is already an announcement pointed to this date.']]], 422);
        }

        if($request->validate($rules)) {

            if($announcement->date != $request->date) {
                return response()->json(['errors' => ['date' => ['There is already an announcement pointed to this date.']]], 422);
            }

            $announcement->update([
                'content' => $request->content,
                'date_from' => $request->dateFrom,
                'date_until' => $request->dateUntil,
                'marquee_on' => $request->marqueeOn,
            ]);
            MonitorChecker::refreshToken();
            return response()->json(['announcement' => $announcement], 200);
        }
    }

    public function delete($id) {
        if(SysDefault::where('announcement_id', $id)->exists()) {
            return response()->json([
                'errors' => [
                    'message' => ['Cannot delete default event']
                ]
            ], 422);
        }

        $announcement = Announcement::findorFail($id);
        if($announcement->delete()) {
            MonitorChecker::refreshToken();
            return response()->json([
                'message' => 'Announcement deleted.'
            ], 200);
        }
    }
    
    public function setDefault($announcementId) {
        $sysDefault = SysDefault::first();
        $sysDefault->update([
            'announcement_id' => $announcementId
        ]);
        MonitorChecker::idle();
        return response()->json([
            'message' => ['Default event updated']
        ]);
    }
}
