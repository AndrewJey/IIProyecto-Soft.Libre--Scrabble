<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInventoryLetterGameTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::create('inventory_letter_game', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('letter_id')->unsigned();
            $table->string('quantity');
            $table->integer('game_id')->unsigned();
            $table->unique(['game_id', 'letter_id']);
            $table->foreign('letter_id')->references('id')->on('letters');
            $table->foreign('game_id')->references('id')->on('games');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('inventory_letter_game');
    }
}
