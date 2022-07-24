<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MonitorViewController extends Controller
{
    public function index() {
        return view('monitor-view/index');
    }
}
