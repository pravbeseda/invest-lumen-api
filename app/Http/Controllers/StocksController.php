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
        $stock = $this->getStockById($id);

        return json_encode($stock);
    }

    public function searchStock(string $ticker, string $driver)
    {
        $stock = $this->searchStockByTickerAndDriver($ticker, $driver);

        return json_encode($stock);
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
            'priceTime' => date('Y-m-d H:i:s'),
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
                'priceTime' => date('Y-m-d H:i:s'),
            ]);

        return json_encode('OK');
    }

    public function filterStocks(Request $request)
    {
        $stocks = $this->stock
            ->orderBy('ticker')
            ->take(50)
            ->get();
        $totalCount = $this->stock->count();

        return json_encode([
            'content' => $stocks,
            'totalCount' => $totalCount,
        ]);
    }

    public function refreshPrice(int $id)
    {
        $stock = $this->getStockById($id);
        $lastPrice = $this->getLastPrice($stock);
        if ($lastPrice != $stock->lastPrice) {
            $this->setPrice($stock->id, $lastPrice);
        }

        return json_encode($lastPrice);
    }

    public function getPriceByTicker(string $ticker)
    {
        $stock = $this->stock
            ->where('ticker', '=', \strtoupper($ticker))
            ->take(1)
            ->get()[0];

        return json_encode($stock->lastPrice);
    }

    private function setPrice(int $id, $price)
    {
        $this->stock
            ->where('id', '=', $id)
            ->update([
                'lastPrice' => $price,
                'priceTime' => date('Y-m-d H:i:s'),
            ]);
    }

    private function getLastPrice($stock)
    {
        $driverController = $this->getDriver($stock->driver);

        return $driverController->getLastPrice($stock);
    }

    private function getStockById(int $id)
    {
        return $this->stock
            ->where('id', '=', $id)
            ->take(1)
            ->get()[0];
    }

    private function getDriver(string $driverName)
    {
        if ($driverName == 'TCS') {
            return new TinkoffController();
        } elseif ($driverName == 'MCX') {
            return new McxController();
        } else {
            return null;
        }
    }

    private function searchStockByTickerAndDriver(string $ticker, string $driver)
    {
        $driverController = $this->getDriver($driver);

        return $driverController->searchStock($ticker);
    }
}
