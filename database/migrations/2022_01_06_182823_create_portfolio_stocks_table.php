<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePortfolioStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('portfolio_stocks', function (Blueprint $table) {
            $table->bigInteger('userId');
            $table->bigInteger('portfolioId');
            $table->bigInteger('stockId');
            $table->decimal('quantity', 20, 8)->default(0);
            $table->decimal('cost', 20, 2)->default(0);
            $table->decimal('costRub', 20, 2)->default(0);
            $table->string('comment')->nullable();
            $table->unique(['portfolioId', 'stockId']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('portfolio_stocks');
    }
}
