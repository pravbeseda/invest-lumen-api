<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deal extends Model
{
    protected $fillable = [
        'datetime',
        'userId',
        'portfolioId',
        'stockId',
        'quantity',
        'cost',
        'costRub',
    ];
}
