<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockHistoryYears extends Model
{
    protected $fillable = [
        'id',
        'ticker',
        'price',
        'datetime',
    ];
}
