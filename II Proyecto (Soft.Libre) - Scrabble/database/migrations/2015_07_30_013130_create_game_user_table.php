<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGameUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game_user', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('game_id')->unsigned();
            $table->integer('order');
            $table->integer('user_id')->unsigned();
            $table->boolean('have_turn')->default(false);
            $table->boolean('cons_turn')->default(false);
            $table->boolean('has_left')->default(false);
            $table->boolean('has_won')->default(false);
            $table->foreign('game_id')->references('id')->on('games');
            $table->foreign('user_id')->references('id')->on('users');
            $table->unique(['game_id', 'user_id']);
            $table->unique(['game_id', 'user_id', 'has_won']);
            $table->integer('points')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('game_user');
    }
}
