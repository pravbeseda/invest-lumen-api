<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DealsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    private function validateDeal(Request $request)
    {
        $this->validate($request, [
            'datetime' => 'required',
            'portfolioId' => 'required|numeric',
            'stockId' => 'required|numeric',
            'invested' => 'numeric',
            'quantity' => 'numeric',
           ]);
    }

    public function createDeal(Request $request)
    {
        $this->validateDeal($request);
        $user = UsersController::getUserByToken($request);
        if (!empty($user)) {
            $stockId = $request->input('stockId');
            $cost = $request->input('cost');
            $costRub = $request->input('costRub');
            if ($costRub == null) {
                $stock = \App\Models\StockItem::where(['id' => $stockId])->first();
                if ($stock['currency'] == 'RUB') {
                    $costRub = $cost;
                } // А если не рубли - фронт присылать должен
            }
            \App\Models\Deal::create([
                'datetime' => $request->input('datetime'),
                'userId' => $user->id,
                'portfolioId' => $request->input('portfolioId'),
                'stockId' => $stockId,
                'cost' => $cost,
                'costRub' => $costRub,
                'quantity' => $request->input('quantity'),
            ]);

            return response()->json(['status' => 'success']);
        } else {
            return response()->json(['status' => 'fail', 'message' => 'Необходима авторизация!'], 401);
        }
    }
}
