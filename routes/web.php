<?php

/** @var \Laravel\Lumen\Routing\Router $router */
$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->get('stocks/{id:[0-9]+}', ['uses' => 'StocksController@getStock']);
$router->get('stocks', ['uses' => 'StocksController@filterStocks']);
$router->post('stocks', ['uses' => 'StocksController@addStock']);
$router->put('stocks/{id:[0-9]+}', ['uses' => 'StocksController@updateStock']);
$router->put('stocks/{id:[0-9]+}/refresh-price', ['uses' => 'StocksController@refreshPrice']);
$router->get('ticker/{ticker:[A-Z\@0-9]+}/{driver:[A-Z]+}', ['uses' => 'StocksController@searchStock']);
$router->get('ticker/{ticker:[A-Z\@0-9]+}/price', ['uses' => 'StocksController@getPriceByTicker']);

//ToDo: Delete
/*$router->get('api/ticker/{ticker:[A-Z]+}', ['uses' => 'TinkoffController@getInfoByTicker']);
$router->get('api/figi/{figi:[A-Z0-9]+}', ['uses' => 'TinkoffController@getInfoByFigi']);
$router->get('api/orderbook/{figi:[A-Z0-9]+}', ['uses' => 'TinkoffController@getOrderBook']);
$router->get('api/price/{figi:[A-Z0-9]+}', ['uses' => 'TinkoffController@getLastPrice']);*/
