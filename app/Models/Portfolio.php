<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Portfolio extends Model
{
    protected $fillable = [
        'name',
        'userId',
        'invested',
        'value',
    ];
}
