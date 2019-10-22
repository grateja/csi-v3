<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use DB;
use App\Barangay;
use App\CityMunicipality;
use App\SelfAuth;
use App\Rules\VerifyPassword;
use App\Role;

class UsersController extends Controller
{
    public function index(Request $request) {
        $users = User::where(function($query) use ($request) {
            $query->where('name', 'like', "%$request->keyword%");
        });

        return response()->json([
            'result' => $users->paginate(10)
        ], 200);
    }

    public function autocomplete(Request $request) {
        $data = User::where(function($query) use ($request) {
            $query->where('name', 'like', "%$request->keyword%");
        })->limit(10)->get();

        return response()->json([
            'data' => $data
        ], 200);
    }

    public function create(Request $request) {
        $rules = [
            'name' => 'required',
            'contactNumber' => 'required',
            'email' => 'required|email|unique:users|confirmed',
            'password' => 'required|min:5|confirmed',
        ];

        if($request->validate($rules)) {
            return DB::transaction(function () use ($request) {
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'address' => $request->address,
                    'contact_number' => $request->contactNumber,
                    'password' => bcrypt($request->password),
                ]);

                $user->assignRole(Role::where('name', 'staff')->first()->id);

                return response()->json([
                    'user' => $user
                ], 200);
            });
        }
    }

    public function assignRole($userId, Request $request) {
        $client = auth()->user();

        $rules = [
            'roleId' => 'required',
            'password' => ['required', new VerifyPassword($client->password)],
            'branchesId' => 'required'
        ];

        $user = User::findOrFail($userId);

        if($userId == $client->id) {
            return response()->json([
                'errors' => [
                    'message' => ['Cannot assign role to self.']
                ]
            ], 422);
        }

        if($request->validate($rules)) {
            return DB::transaction(function () use ($request, $user) {
                $user->assignRole($request->roleId);

                return response()->json([
                    'user' => $user->fresh('branches'),
                ], 200);
            });
        }
    }
}
