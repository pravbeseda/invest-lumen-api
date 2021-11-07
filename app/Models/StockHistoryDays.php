<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockHistoryDays extends Model
{
    protected $fillable = [
        'id',
        'ticker',
        'price',
        'datetime',
    ];
}
