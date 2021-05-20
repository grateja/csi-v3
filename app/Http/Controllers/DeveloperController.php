<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DeveloperController extends Controller
{
    public function setSystemDateTime(Request $request) {
        exec("sudo date -s $request->date");
        exec("sudo hwclock -w");
        return $request->date;
    }
}
