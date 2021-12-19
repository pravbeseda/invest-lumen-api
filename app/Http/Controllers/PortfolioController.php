<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PortfolioController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function addPortfolio(Request $request)
    {
        $user = UsersController::getUserByToken($request);
        if (!empty($user)) {
            return $user;
        }

        return response()->json(['status' => 'fail', 'message' => 'Необходима авторизация!'], 401);
    }
}
