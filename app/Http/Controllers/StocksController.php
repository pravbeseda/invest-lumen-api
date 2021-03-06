<?php

namespace App\Http\Controllers;

use App\Models\StockHistoryDays;
use App\Models\StockItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StocksController extends Controller
{
    protected $stock;

    public function __construct(StockItem $stock)
    {
        $this->middleware('auth:api');
        $this->stock = $stock;
    }

    public function getStock(int $id)
    {
        $stock = $this->getStockById($id);

        return response()->json($stock);
    }

    public function searchStock(string $ticker, string $driver)
    {
        $stock = $this->searchStockByTickerAndDriver(\strtoupper($ticker), $driver);

        return response()->json($stock);
    }

    public function searchCurrency(string $name, string $driver)
    {
        $stock = $this->searchCurrencyByNameAndDriver(\strtoupper($name), $driver);

        return response()->json($stock);
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
            'isin' => 'string|nullable',
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

        return response()->json(['status' => 'success']);
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

        return response()->json(['status' => 'success']);
    }

    public function filterStocks(Request $request)
    {
        $stockTypes = $this->correctQuery($request, 'stockTypes');
        $stocks = $this->stock
            ->when(is_array($stockTypes), function ($query) use ($stockTypes) {
                return $query->whereIn('type', $stockTypes);
            })
            ->orderBy('ticker')
            ->take(50)
            ->get();
        $totalCount = $this->stock->count();

        return response()->json([
            'content' => $stocks,
            'totalCount' => $totalCount,
        ]);
    }

    public function refreshPrice(int $id)
    {
        $stock = $this->getStockById($id);
        $lastPrice = $this->getLastPrice($stock);
        if (is_numeric($lastPrice) && $lastPrice != $stock->lastPrice) {
            $this->setPrice($stock->id, $lastPrice);

            $date = new \DateTime();
            $date->setTime(23, 59, 59);
            DB::insert(
                'insert into stock_history_days (id, ticker, price, datetime) values (?, ?, ?, ?) ON DUPLICATE KEY UPDATE price = ?',
                [$id, $stock->ticker, $lastPrice, $date->format('Y-m-d H:i:s'), $lastPrice]
            );

            $date->modify('last day of this month');
            DB::insert(
                'insert into stock_history_months (id, ticker, price, datetime) values (?, ?, ?, ?) ON DUPLICATE KEY UPDATE price = ?',
                [$id, $stock->ticker, $lastPrice, $date->format('Y-m-d H:i:s'), $lastPrice]
            );

            $date->modify('last day of December this year');
            DB::insert(
                'insert into stock_history_years (id, ticker, price, datetime) values (?, ?, ?, ?) ON DUPLICATE KEY UPDATE price = ?',
                [$id, $stock->ticker, $lastPrice, $date->format('Y-m-d H:i:s'), $lastPrice]
            );
        }

        return response()->json($lastPrice);
    }

    public function getDiffByTicker(string $ticker)
    {
        $stat = StockHistoryDays::where('ticker', \strtoupper($ticker))->orderBy('datetime', 'desc')->take(2)->get();

        return response()->json((count($stat) > 1) ? $stat[0]->price - $stat[1]->price : 0);
    }

    public function getPriceByTicker(string $ticker)
    {
        $stock = $this->stock
            ->where('ticker', '=', \strtoupper($ticker))
            ->take(1)
            ->get()[0];

        return response()->json($stock->lastPrice);
    }

    public function getCurrencies()
    {
        $driverController = $this->getDriver('TCS'); // ???????? ???????????? ????????????????

        return response()->json($driverController->getCurrencies());
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

    public function getStockById(int $id)
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

    private function searchCurrencyByNameAndDriver(string $name, string $driver)
    {
        $driverController = $this->getDriver($driver);

        return $driverController->searchCurrency($name);
    }
}
