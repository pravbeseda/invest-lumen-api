<?php

/** @var \Laravel\Lumen\Routing\Router $router */
$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->get('api/stock/ticker/{ticker:[A-Z]+}', ['uses' => 'StocksController@getStockByTicker']);

//ToDo: Delete
$router->get('api/ticker/{ticker:[A-Z]+}', ['uses' => 'TinkoffController@getInfoByTicker']);
$router->get('api/figi/{figi:[A-Z0-9]+}', ['uses' => 'TinkoffController@getInfoByFigi']);
$router->get('api/orderbook/{figi:[A-Z0-9]+}', ['uses' => 'TinkoffController@getOrderBook']);
$router->get('api/price/{figi:[A-Z0-9]+}', ['uses' => 'TinkoffController@getLastPrice']);
