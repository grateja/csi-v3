<?php

namespace App\Http\Controllers;

use App\TiTo;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Jenssegers\Agent\Agent;

class OAuthController extends Controller
{
    public function login(Request $request) {
        $err = null;
        if(auth()->attempt([
            'email' => $request->email,
            'password' => $request->password
        ])) {
            $user = User::findOrFail(auth()->id());

            if($user->hasAnyRole(['customer'])) {
                return response()->json([
                    'errors' => [
                        'email' => ['You do not have permission to access this panel']
                    ]
                ], 422);
            }

            $token = $user->createToken('csi-2019');

            if($user->hasAnyRole(['staff'])) {
                $tito = TiTo::where('user_id', $user->id)
                    ->whereDate('created_at', Carbon::today())
                    ->latest('created_at')->first();

                if($tito) {
                    if($tito->time_out != null) {
                        $err = 'You have already timed-in and timed-out';
                        // return response()->json([
                        //     'errors' => [
                        //         'message' => ['You are al']
                        //     ]
                        // ], 422);
                    }
                } else {
                    $agent = new Agent();

                    $platform = $agent->platform();
                    $browser = $agent->browser();
                    $device = $agent->device();
                    $platformVersion = $agent->version($platform);
                    $browserVersion = $agent->version($browser);
                    $deviceVersion = $agent->version($device);

                    $tito = TiTo::create([
                        'user_id' => $user->id,
                        'user_name' => $user->name,
                        'device' => "$platform-$platformVersion/$browser-$browserVersion/$device-$deviceVersion",
                    ]);
                    $this->dispatch($tito->queSynch());
                }

            }


            return response()->json([
                'user' => $user,
                'token' => $token,
                'activeBranch' => $user->activeBranch,
                'retainToken' => $request->rememberMe,
                'machineActivationMethod' => env('MACHINE_ACTIVATION_METHOD', 'nsoft'),
                'dopuSetup' => env('DOPU_SETUP', 'basic') // basic, slave, master
            ]);
        }
        return response()->json([
            'errors' => ['email' => ['Invalid log in credentials']]
        ], 401);
    }

    public function logout() {
        $user = auth('api')->user()->logout();
        return 1;
    }

    public function check() {
        $user = auth('api')->user();
        $token = $user->createToken('csi-2019');

        return response()->json([
            'user' => $user,
            'activeBranch' => $user->activeBranch,
            'token' => $token,
            'machineActivationMethod' => env('MACHINE_ACTIVATION_METHOD', 'nsoft'),
            'dopuSetup' => env('DOPU_SETUP', 'basic') // basic, slave, master
        ], 200);
    }
}
