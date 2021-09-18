<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockItem extends Model
{
    protected $fillable = [
        'ticker',
        'name',
        'currency',
        'figi',
        'isin',
        'type',
        'lastPrice',
    ];
}
