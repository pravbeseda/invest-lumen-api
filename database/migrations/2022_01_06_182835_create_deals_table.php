<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDealsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deals', function (Blueprint $table) {
            $table->id();
            $table->timestamp('datetime');
            $table->bigInteger('userId');
            $table->bigInteger('portfolioId');
            $table->bigInteger('stockId');
            $table->decimal('quantity', 20, 8);
            $table->decimal('cost', 20, 2)->default(0);
            $table->decimal('costRub', 20, 2)->default(0);
            $table->string('comment')->nullable();
            $table->timestamps();
            $table->index(['portfolioId', 'stockId', 'datetime']);
            $table->index(['datetime', 'portfolioId', 'stockId']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('deals');
    }
}
