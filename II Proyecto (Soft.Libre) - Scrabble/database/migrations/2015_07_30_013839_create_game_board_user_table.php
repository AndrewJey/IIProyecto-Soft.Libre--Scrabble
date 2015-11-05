<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGameBoardUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::create('game_board_user', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('x');
            $table->integer('y');
            $table->integer('letter_id')->unsigned();
            $table->integer('game_user_id')->unsigned();
            $table->foreign('letter_id')->references('id')->on('letters');
            $table->foreign('game_user_id')->references('id')->on('game_user');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('game_board_user');
    }
}
