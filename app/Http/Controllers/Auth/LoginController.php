<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{

    /**
     * API login method
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $validate = Validator::make($data, [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6'
        ]);

        if ($validate->fails()) {
            return response(['errors' => $validate->errors()->all()], 422);
        }

        $user = User::where('email', $data['email'])->first();

        if ($user) {
            if (Hash::check($data['password'], $user->password)) {
                $token = $user->createToken('Laravel Personal Access Client')->accessToken;
                return response(['user' => $user, 'token' => $token], 200);
            } else {
                return response(['message' => "Wrong password"], 422);
            }
        } else {
            return response(['message' => 'User not found!'], 422);
        }
    }

    /**
     * API logout method
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function logout()
    {
        if (auth()->user()->tokens()->delete()) {
            return response(['message' => 'You haven been successfully logged out'], 200);
        }
    }
}
