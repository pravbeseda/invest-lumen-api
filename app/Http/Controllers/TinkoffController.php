<?php

namespace App\Http\Controllers;

class TinkoffController extends Controller
{
    public function __construct()
    {
    }

    public function getInfoByTicker(string $ticker)
    {
        return $this->get('/openapi/market/search/by-ticker', ['ticker' => $ticker]);
    }

    public function getInfoByFigi(string $figi)
    {
        return $this->get('/openapi/market/search/by-figi', ['figi' => $figi]);
    }

    public function getOrderBook(string $figi, int $depth = 1)
    {
        return $this->get('/openapi/market/orderbook', ['figi' => $figi, 'depth' => $depth]);
    }

    public function getLastPrice(string $figi)
    {
        $orderBook = json_decode($this->getOrderBook($figi));

        return $orderBook->payload->lastPrice;
    }

    private function get(string $url, $query)
    {
        $client = new \GuzzleHttp\Client(['base_uri' => 'https://api-invest.tinkoff.ru']);
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
