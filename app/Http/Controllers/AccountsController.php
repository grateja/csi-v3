<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use DB;
use App\Rules\VerifyPassword;

class AccountsController extends Controller
{
    public function updateProfile($id, Request $request) {
        if($id == 'self') {
            $user = auth('api')->user();
        } else {
            $user = User::findOrFail($id);
        }

        $rules = [
            'name' => 'required',
        ];

        if($request->validate($rules)) {
            return DB::transaction(function () use ($request, $user) {

                $user->update([
                    'name' => $request->name,
                    'contact_number' => $request->contactNumber,
                    'address' => $request->address,
                ]);

                return response()->json([
                    'user' => $user,
                ], 200);
            });
        }
    }

    public function updateEmail($id, Request $request) {
        if($id == 'self') {
            $id = auth('api')->id();
        }

        $user = User::findOrFail($id);

        $rules = [
            'email' => 'required|email',
            'password' => ['required', new VerifyPassword($user->password)],
        ];

        if($request->email != $user->email) {
            // email changed
            $rules['email'] = 'required|email|unique:users';
        }

        if($request->validate($rules)) {
            $user->update([
                'email' => $request->email,
            ]);

            return response()->json([
                'user' => $user,
            ], 200);
        }
    }

    public function updatePassword($id, Request $request) {
        if($id == 'self') {
            $id = auth('api')->id();
        }

        $user = User::findOrFail($id);

        $rules = [
            'oldPassword' => ['required', new VerifyPassword($user->password)],
            'password' => 'required|min:5|confirmed',
        ];

        if($request->validate($rules)) {
            return DB::transaction(function () use ($user, $request) {
                $user->update([
                    'password' => bcrypt($request->password)
                ]);

                return response()->json([
                    'user' => $user
                ]);
            });
        }
    }

    public function getAccountInfo($id) {
        if($id == 'self') {
            $id = auth('api')->id();
        }

        $user = User::findOrFail($id);

        return response()->json([
            'accountInfo' => $user
        ], 200);
    }
}
