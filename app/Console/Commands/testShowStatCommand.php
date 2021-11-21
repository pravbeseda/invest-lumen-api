<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class testShowStatCommand extends Command
{
    protected $signature = 'test:stat';
    protected $description = 'Test show statistics';

    public function handle()
    {
        $stats = DB::table('stock_history_days')
        ->where('ticker', '=', 'FXUS')
        ->get();

        foreach ($stats as $stat) {
            echo date_format(date_create($stat->datetime), 'd.m.Y')." - $stat->price\n";
        }
    }
}
