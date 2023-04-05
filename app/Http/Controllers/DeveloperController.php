<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DeveloperController extends Controller
{
    public function setSystemDateTime(Request $request) {
        exec("sudo date -s $request->date");
        exec("sudo hwclock -w");
        return $request->date;
    }

    public function getSystemDateTime() {
        return response()->json([
            'sysDateTime' => Carbon::now()->isoFormat('dddd MMMM D, G H:m A Z'),
            'version' => env('APP_VERSION', ''),
        ]);
    }

    public function machineActivationMethod() {
        return env('MACHINE_ACTIVATION_METHOD', 'els');
    }

    public function login($userId) {
        $user = User::find($userId);
        $token = $user->createToken('csi-2019');
        return response()->json([
            'user' => $user,
            'token' => $token,
            'machineActivationMethod' => env('MACHINE_ACTIVATION_METHOD', 'nsoft'),
            'dopuSetup' => env('DOPU_SETUP', 'basic'), // basic, slave, master
            'dopuIncludeServices' => env('DOPU_INCLUDE_SERVICES', false),
        ], 200);
    }
}
