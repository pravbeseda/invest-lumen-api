<?php

namespace App\Console\Commands;

use App\Http\Controllers\StocksController;
use App\Models\StockItem;
use Illuminate\Console\Command;

class updatePricesCommand extends Command
{
    protected $signature = 'update:prices';
    protected $description = 'Update prices of all stocks';

    public function handle()
    {
        foreach (StockItem::all() as $stock) {
            $stocksController = new StocksController($stock);
            $price = json_decode($stocksController->refreshPrice($stock->id));
            echo $stock->ticker." - $price\n";
            sleep(1);
        }
    }
}
