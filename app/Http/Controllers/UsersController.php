<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function getUser(Request $request)
    {
        $user = $this->getUserByToken($request);

        return ($user != false) ? $user : response()->json(['status' => 'fail', 'message' => 'Необходима авторизация!'], 401);
    }

    public function updateUser(Request $request)
    {
        $this->validate($request, [
            'username' => 'required|string',
            'email' => 'required|string',
        ]);
        $key = explode(' ', $request->header('Authorization'));
        $user = User::where('token', $key[1])->first();
        if (!empty($user)) {
            $user->update([
                'username' => $request->input('username'),
                'email' => $request->input('email'),
            ]);

            return response()->json(['status' => 'success']);
        }

        return response()->json(['status' => 'fail', 'message' => 'Пользователь не найден!'], 401);
    }

    public static function getUserByToken(Request $request)
    {
        $key = explode(' ', $request->header('Authorization'));
        $user = User::where('token', $key[1])->first();
        if (!empty($user)) {
            return $user;
        } else {
            return false;
        }
    }
}
