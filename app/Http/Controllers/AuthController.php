<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function __construct()
    {
    }

    public function authenticate(Request $request)
    {
        $this->validate($request, [
            'email' => 'required',
            'password' => 'required',
        ]);
        $user = User::where('email', $request->input('email'))->first();
        if (isset($user) && Hash::check($request->input('password'), $user->password)) {
            $apikey = base64_encode(Str::random(40));
            User::where('email', $request->input('email'))->update(['token' => "$apikey"]);

            return response()->json(['status' => 'success', 'token' => $apikey]);
        } else {
            return response()->json(['status' => 'fail', 'message' => 'Пользователь не найден!'], 401);
        }
    }

    public function register(Request $request)
    {
        $this->validate($request, [
            'username' => 'required|string',
            'email' => 'required|string',
            'password' => 'required|string',
           ]);

        $user = User::create([
        'username' => $request->input('username'),
        'email' => $request->input('email'),
        'password' => Hash::make($request->input('password')),
        ]);

        return response()->json(['status' => 'success', 'message' => 'Пользователь создан']);
    }
}
