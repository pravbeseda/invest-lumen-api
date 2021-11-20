<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use Log;

class Controller extends BaseController
{
    // Правильно получаем get-параметры из запроса (в т.ч. массивы из одноименных параметров)
    public function correctQuery(Request $request, $key = null)
    {
        $url = parse_url($request->getRequestUri());
        $query = explode('&', $url['query']);
        $params = [];

        foreach ($query as $param) {
            list($name, $value) = explode('=', $param);
            $params[urldecode($name)][] = urldecode($value);
        }

        if (array_key_exists($key, $params)) {
            return $params[$key];
        }
        if ($key) {
            return null;
        }

        return $params;
    }

    public function myLog(...$values)
    {
        foreach ($values as $value) {
            Log::info(print_r($value, true));
        }
    }
}
