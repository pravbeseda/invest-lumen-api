<?php

namespace App\Http\Controllers;

class TinkoffController extends Controller
{
    public function __construct()
    {
    }

    public function searchStock(string $ticker)
    {
        $info = json_decode($this->getInfoByTicker($ticker));
        if ($info && $info->payload && $info->payload->instruments && $info->payload->instruments[0]) {
            $instrument = $info->payload->instruments[0];
            $orderBook = json_decode($this->getOrderBook($instrument->figi));
            $lastPrice = ($orderBook && $orderBook->payload) ? $orderBook->payload->lastPrice : '';

            return [
                'ticker' => $instrument->ticker,
                'name' => $instrument->name,
                'currency' => $instrument->currency,
                'figi' => $instrument->figi,
                'isin' => $instrument->isin,
                'type' => $instrument->type,
                'lastPrice' => $lastPrice,
                'driver' => 'TCS',
            ];
        } else {
            return null;
        }
    }

    public function searchCurrency(string $name)
    {
        $currencies = $this->getCurrencies();
        if (isset($currencies[$name])) {
            $currency = $currencies[$name];
            $orderBook = json_decode($this->getOrderBook($currency['figi']));
            $lastPrice = ($orderBook && $orderBook->payload) ? $orderBook->payload->lastPrice : '';
            $currency['lastPrice'] = $lastPrice;

            return $currency;
        } else {
            return null;
        }
    }

    public function getLastPrice($stock)
    {
        $orderBook = json_decode($this->getOrderBook($stock->figi));

        return $orderBook->payload->lastPrice;
    }

    public function getCurrencies()
    {
        $info = json_decode($this->get('market/currencies'));
        if (count($info->payload->instruments) > 0) {
            $currencies = [];
            foreach ($info->payload->instruments as $instrument) {
                // Пока ключем используем псевдотикер - первые три символа от тикера (USD, EUR)
                // При появлении других валют нужно будет что-то придумать
                $ticker = substr($instrument->ticker, 0, 3);
                $currencies[$ticker] = [
                    'ticker' => $ticker,
                    'name' => $instrument->name,
                    'currency' => $instrument->currency,
                    'figi' => $instrument->figi,
                    'type' => $instrument->type,
                    'driver' => 'TCS',
                ];
            }

            return $currencies;
        } else {
            return null;
        }
    }

    private function getInfoByTicker(string $ticker)
    {
        return $this->get('market/search/by-ticker', ['ticker' => $ticker]);
    }

    private function getInfoByFigi(string $figi)
    {
        return $this->get('market/search/by-figi', ['figi' => $figi]);
    }

    private function getOrderBook(string $figi, int $depth = 1)
    {
        return $this->get('market/orderbook', ['figi' => $figi, 'depth' => $depth]);
    }

    private function get(string $url, $query = [])
    {
        $client = new \GuzzleHttp\Client(['base_uri' => 'https://api-invest.tinkoff.ru/openapi/']);
        $headers = [
            'Authorization' => 'Bearer '.env('TOKEN_TINKOFF'),
            'Accept' => 'application/json',
        ];
        $response = $client->request(
            'GET',
            $url,
            [
                'headers' => $headers,
                'query' => $query,
            ],
        );

        return $response->getBody();
    }
}
