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

    public function getStockByTicker(string $ticker)
    {
        $tinkoffController = new TinkoffController();

        $res = json_decode($tinkoffController->getInfoByTicker($ticker));
        $info = null;

        if ($res && $res->payload && $res->payload->instruments && $res->payload->instruments[0]) {
            $instrument = $res->payload->instruments[0];
            $info = [
                'ticker' => $instrument->ticker,
                'name' => $instrument->name,
                'currency' => $instrument->currency,
                'figi' => $instrument->figi,
                'isin' => $instrument->isin,
                'type' => $instrument->type,
            ];
            $orderBook = json_decode($tinkoffController->getOrderBook($instrument->figi));
            if ($orderBook && $orderBook->payload) {
                $info['lastPrice'] = $orderBook->payload->lastPrice;
            }
        }

        return json_encode($info);
    }

    public function addStock(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string',
            'ticker' => 'required|string',
            'lastPrice' => 'required|numeric',
            'currency' => 'required|string',
            'figi' => 'required|string',
            'isin' => 'required|string',
            'type' => 'required|string',
           ]);

        $stock = $this->stock->create([
            'name' => $request->input('name'),
            'ticker' => $request->input('ticker'),
            'lastPrice' => $request->input('lastPrice'),
            'currency' => $request->input('currency'),
            'figi' => $request->input('figi'),
            'isin' => $request->input('isin'),
            'type' => $request->input('type'),
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
