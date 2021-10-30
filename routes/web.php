<?php

/** @var \Laravel\Lumen\Routing\Router $router */
$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->get('api/stock/{id:[0-9]+}', ['uses' => 'StocksController@getStock']);
$router->get('api/stock/ticker/{ticker:[A-Z0-9]+}/{driver:[A-Z]+}', ['uses' => 'StocksController@searchStock']);
$router->get('api/stocks', ['uses' => 'StocksController@filterStocks']);
$router->post('api/stock', ['uses' => 'StocksController@addStock']);
$router->put('api/stock/{id:[0-9]+}', ['uses' => 'StocksController@updateStock']);

//ToDo: Delete
$router->get('api/ticker/{ticker:[A-Z]+}', ['uses' => 'TinkoffController@getInfoByTicker']);
$router->get('api/figi/{figi:[A-Z0-9]+}', ['uses' => 'TinkoffController@getInfoByFigi']);
$router->get('api/orderbook/{figi:[A-Z0-9]+}', ['uses' => 'TinkoffController@getOrderBook']);
$router->get('api/price/{figi:[A-Z0-9]+}', ['uses' => 'TinkoffController@getLastPrice']);
