<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PortfolioController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    private function validatePortfolio(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string',
            'invested' => 'numeric',
            'value' => 'numeric',
            'balanceRub' => 'numeric|nullable',
            'balanceUsd' => 'numeric|nullable',
            'balanceEur' => 'numeric|nullable',
           ]);
    }

    public function createPortfolio(Request $request)
    {
        $this->validatePortfolio($request);
        $user = UsersController::getUserByToken($request);
        if (!empty($user)) {
            \App\Models\Portfolio::create([
                'name' => $request->input('name'),
                'userId' => $user->id,
                'invested' => 0,
                'value' => 0,
                'balanceRub' => $request->input('balanceRub'),
                'balanceUsd' => $request->input('balanceUsd'),
                'balanceEur' => $request->input('balanceEur'),
            ]);

            return response()->json(['status' => 'success']);
        } else {
            return response()->json(['status' => 'fail', 'message' => 'Необходима авторизация!'], 401);
        }
    }

    public function updatePortfolio(Request $request)
    {
        $this->validatePortfolio($request);
        $user = UsersController::getUserByToken($request);
        if (!empty($user)) {
            \App\Models\Portfolio::where([
                'userId' => $user->id,
                'id' => $request->input('id'),
            ])->update([
                'name' => $request->input('name'),
                'balanceRub' => $request->input('balanceRub'),
                'balanceUsd' => $request->input('balanceUsd'),
                'balanceEur' => $request->input('balanceEur'),
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

    public function getPortfolio(Request $request)
    {
        $user = UsersController::getUserByToken($request);
        if (!empty($user)) {
            $portfolio = \App\Models\Portfolio::where(['userId' => $user->id, 'id' => $request->id])
                ->first();

            return ($portfolio) ? response()->json($portfolio) : response()->json(['status' => 'fail', 'message' => 'Портфель не найден'], 404);
        } else {
            return response()->json(['status' => 'fail', 'message' => 'Необходима авторизация!'], 401);
        }
    }

    public static function updatePortfolioStock(int $userId, int $portfolioId, int $stockId)
    {
        $portfolioStock = DB::select('select SUM(quantity) as quantity, SUM(cost) as cost, SUM(costRub) as costRub 
                                      from deals where portfolioId = :portfolioId and stockId = :stockId',
            [
                'portfolioId' => $portfolioId,
                'stockId' => $stockId,
            ])[0];
        DB::insert('insert into portfolio_stocks set userId=?, portfolioId=?, stockId=?, quantity=?, cost=?, costRub=? 
                    ON DUPLICATE KEY UPDATE quantity =?, cost =?, costRub=?',
            [
                $userId, $portfolioId, $stockId, $portfolioStock->quantity, $portfolioStock->cost, $portfolioStock->costRub,
                $portfolioStock->quantity, $portfolioStock->cost, $portfolioStock->costRub,
            ]);
    }
}
