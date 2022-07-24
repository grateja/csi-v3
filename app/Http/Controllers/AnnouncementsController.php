<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Announcement;
use Carbon\Carbon;
use App\SysDefault;

class AnnouncementsController extends Controller
{

    public function index(Request $request) {
        $announcements = Announcement::orderBy('date', 'desc')
            ->where('content', 'like', "%$request->keyword%")
            ->paginate(10);

        return response()->json([
            'announcements' => $announcements
        ], 200);
    }

    public function getAnnouncement() {
        $announcement = Announcement::orderBy('id', 'desc')
            ->whereDate('date', Carbon::now())
            ->first();

        if($announcement == null) {
            $announcement = SysDefault::find(1)->announcement;
        }

        return response()->json([
            'announcement' => $announcement
        ], 200);
    }

    public function store(Request $request) {
        $rules = [
            'content' => 'required',
            'date' => 'required|date'
        ];

        if($request->validate($rules)) {
            $_announcement = Announcement::whereDate('date', $request->date)
                ->first();

            if($_announcement != null) {
                return response()->json(['errors' => ['date' => ['There is already an announcement pointed to this date.']]], 422);
            }

            $announcement = Announcement::create($request->only([
                'content', 'date'
            ]));
            return response()->json(['announcement' => $announcement], 200);
        }
    }

    public function update($id, Request $request) {
        $announcement = Announcement::findorFail($id);

        $rules = [
            'content' => 'required',
            'date' => 'required|date'
        ];

        if($request->validate($rules)) {

            if($announcement->date != $request->date) {
                return response()->json(['errors' => ['date' => ['There is already an announcement pointed to this date.']]], 422);
            }

            $announcement->update($request->only([
                'content', 'date'
            ]));
            return response()->json(['announcement' => $announcement], 200);
        }
    }

    public function delete($id) {
        $announcement = Announcement::findorFail($id);
        if($announcement->delete()) {
            return response()->json([
                'message' => 'Announcement deleted.'
            ], 200);
        }
    }
}
