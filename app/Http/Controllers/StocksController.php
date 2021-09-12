<?php

namespace App\Http\Controllers;

class StocksController extends Controller
{
    public function __construct()
    {
    }

    public function getStockByTicker(string $ticker)
    {
        $tinkoffController = new TinkoffController();

        $res = json_decode($tinkoffController->getInfoByTicker($ticker));
        $info = [];

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
        // return json_encode($res);
    }
}
