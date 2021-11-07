<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockHistoryMonths extends Model
{
    protected $fillable = [
        'id',
        'ticker',
        'price',
        'datetime',
    ];
}
