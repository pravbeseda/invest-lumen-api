<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Portfolio;

class PortfolioController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function addPortfolio(Request $request)
    {        
        $this->validate($request, ['name' => 'required|string']);
        $user = UsersController::getUserByToken($request);
        if (!empty($user)) {
            \App\Models\Portfolio::create([
                'name' => $request->input('name'),
            ]);
            return response()->json(['status' => 'success']);
        }

        return response()->json(['status' => 'fail', 'message' => 'Необходима авторизация!'], 401);
    }
}
