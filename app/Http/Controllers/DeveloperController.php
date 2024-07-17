<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;

class DeveloperController extends Controller
{
    public function setSystemDateTime(Request $request) {
        exec("sudo date -s '{$request->date}'");
        exec("sudo hwclock -w");
        Artisan::call('cache:clear');
        if(!$this->checkInternetConnection()) {
            return $request->date;
        } else {
            return response()->json([
                'errors' => [
                    'message' => ['If you are connected to internet, this function may not work. Please reload manually!']
                ]
            ], 422);
        }
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

    private function checkInternetConnection($host = 'www.google.com', $port = 80, $timeout = 10) {
        $connected = @fsockopen($host, $port, $errno, $errstr, $timeout);

        if ($connected) {
            fclose($connected);
            return true;
        } else {
            return false;
        }
    }
}
