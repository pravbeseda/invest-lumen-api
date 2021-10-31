<?php

namespace App\Http\Controllers;

use App\Models\StockItem;
use Illuminate\Http\Request;

class StocksController extends Controller
{
    protected $stock;

    public function __construct(StockItem $stock)
    {
        $this->stock = $stock;
    }

    public function getStock(int $id)
    {
        $stocks = $this->stock
            ->where('id', '=', $id)
            ->take(1)
            ->get();

        return json_encode($stocks[0]);
    }

    public function searchStock(string $ticker, string $driver)
    {
        if ($driver == 'TCS') {
            $driverController = new TinkoffController();
        } elseif ($driver == 'MCX') {
            $driverController = new McxController();
        } else {
            return null;
        }

        $info = $driverController->searchStock($ticker);

        return json_encode($info);
    }

    private function validateStock(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string',
            'ticker' => 'required|string',
            'lastPrice' => 'required|numeric',
            'currency' => 'required|string',
            'figi' => 'string|nullable',
            'boardId' => 'string|nullable',
            'isin' => 'required|string',
            'type' => 'required|string',
            'driver' => 'required|string',
           ]);
    }

    public function addStock(Request $request)
    {
        $this->validateStock($request);
        $stock = $this->stock->create([
            'name' => $request->input('name'),
            'ticker' => $request->input('ticker'),
            'lastPrice' => $request->input('lastPrice'),
            'currency' => $request->input('currency'),
            'figi' => $request->input('figi'),
            'boardId' => $request->input('boardId'),
            'isin' => $request->input('isin'),
            'type' => $request->input('type'),
            'driver' => $request->input('driver'),
        ]);

        return json_encode('OK');
    }

    public function updateStock(int $id, Request $request)
    {
        $this->validateStock($request);
        $stock = $this->stock
            ->where('id', $id)
            ->update([
                'name' => $request->input('name'),
                'ticker' => $request->input('ticker'),
                'lastPrice' => $request->input('lastPrice'),
                'currency' => $request->input('currency'),
                'figi' => $request->input('figi'),
                'boardId' => $request->input('boardId'),
                'isin' => $request->input('isin'),
                'type' => $request->input('type'),
                'driver' => $request->input('driver'),
            ]);

        return json_encode('OK');
    }

    public function filterStocks(Request $request)
    {
        $stocks = $this->stock
            ->orderBy('ticker')
            ->take(10)
            ->get();
        $totalCount = $this->stock->count();

        return json_encode([
            'content' => $stocks,
            'totalCount' => $totalCount,
        ]);
    }
}
