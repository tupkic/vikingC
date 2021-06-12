<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UsersController extends Controller
{

    public function index()
    {
        $users = User::all();
        if (!$users->isEmpty()) {
            return response(['users' => $users]);
        }

        return response(['message' => "There is no any users in our database."]);
    }

    public function show($id)
    {
        $users = User::with('projects')->find($id);

        if ($users) {
            return response(['user' => $users]);
        } else {
            return response(['message' => 'User not found!'], 404);
        }
    }

    /**
     * Store new user method (admin only)
     * We dont need to check if user is admin is protected by middleware
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $validate = Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validate->fails()) {
            return response(['errors' => $validate->errors()->all()], 422);
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'remember_token' => Str::random(15),
            'password' => Hash::make($data['password']),
        ]);

        $token = $user->createToken('Laravel Personal Access Client')->accessToken;

        return response(['user' => $user, 'token' => $token], 200);
    }

    /**
     * Update user method (user it self or admin )
     *
     * @param Request $request
     */
    public function update(Request $request, $id)
    {
        $data = json_decode($request->getContent(), true);

        $user = User::find($id);

        if ($user) {

            if (\Gate::allows('isAdmin') || $user->id == $request->user()->id) {

                $validate = Validator::make($data, [
                    'name' => 'string|max:255',
                    'email' => 'string|email|max:255|unique:users',
                    'password' => 'string|min:6|confirmed',
                    'is_admin' => 'integer'
                ]);

                if ($validate->fails()) {
                    return response(['message' => $validate->errors()->all()], 422);
                }

                if (\Gate::denies('isAdmin')) {
                    unset($data['is_admin']);
                }

                if (isset($data['password'])) {
                    $data['password'] = Hash::make($data['password']);
                }

                $update_user = $user->update($data);

                if ($update_user) {
                    $user = User::find($id);

                    return response(['user' => $user], 200);
                } else {
                    return response(['message' => 'User not updated something went wrong.'], 422);
                }

            } else {
                return response(['message' => 'You have no authorization for this action.'], 406);
            }

        } else {
            return response(['message' => 'User not found!'], 404);
        }
    }

    /**
     * Delete user method (admin only)
     *
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (\Gate::allows('isAdmin')) {

            $user = User::find($id);

            if ($user) {

                if ($user->delete()) {
                    return response(['message' => 'User successfully deleted.'], 422);
                }

                    return response(['message' => 'User not deleted something went wrong.'], 422);

            } else {
                return response(['message' => 'User not found!'], 404);
            }

        } else {
            return response(['message' => 'You have no authorization for this action.'], 406);
        }
    }
}
