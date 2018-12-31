<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubscribeMarketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscribe_markets', function (Blueprint $table) {
            $table->increments('id');
            $table->string('user_id');
            $table->string('exchange_name');
            $table->string('market_quoteid');
            $table->string('market_baseid');
            $table->string('market_symbol');
            $table->string('market_price');
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
        Schema::dropIfExists('subscribe_markets');
    }
}
