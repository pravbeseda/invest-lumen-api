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
        $this->validate($request, ['name' => 'required|string']);
        $user = UsersController::getUserByToken($request);
        if (!empty($user)) {
            \App\Models\Portfolio::create([
                'name' => $request->input('name'),
                'userId' => $user->id,
                'invested' => 0,
                'value' => 0,
            ]);

            return response()->json(['status' => 'success']);
        } else {
            return response()->json(['status' => 'fail', 'message' => 'Необходима авторизация!'], 401);
        }
    }

    public function getPortfolios(Request $request)
    {
        $user = UsersController::getUserByToken($request);
        if (!empty($user)) {
            $count = \App\Models\Portfolio::where(['userId' => $user->id])->count();
            $portfolios = \App\Models\Portfolio::where(['userId' => $user->id])
                ->orderBy('name')
                ->take(50)
                ->get();

            return response()->json([
                'content' => $portfolios,
                'totalCount' => $count,
            ]);
        } else {
            return response()->json(['status' => 'fail', 'message' => 'Необходима авторизация!'], 401);
        }
    }
}
