<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLetterUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('letter_user', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('letter_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->integer('game_id')->unsigned();
            $table->foreign('letter_id')->references('id')->on('letters');
            $table->foreign('user_id')->references('id')->on('users');
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
        Schema::drop('letter_user');
    }
}
