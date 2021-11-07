<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class HistoryTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $historyTable = function (Blueprint $table) {
            $table->bigInteger('id');
            $table->string('ticker');
            $table->decimal('price', 16, 8, true);
            $table->timestamp('datetime')->nullable();
            $table->unique(['id', 'datetime']);
            $table->index(['ticker', 'datetime']);
            $table->timestamps();
        };
        Schema::create('stock_history_days', $historyTable);
        Schema::create('stock_history_months', $historyTable);
        Schema::create('stock_history_years', $historyTable);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
