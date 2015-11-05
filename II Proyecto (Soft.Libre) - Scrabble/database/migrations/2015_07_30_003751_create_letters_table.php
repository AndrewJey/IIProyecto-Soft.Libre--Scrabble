<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLettersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('letters', function(Blueprint $table)
        {
            $table->increments('id');
            $table->string('letter');
            $table->integer('points');
            $table->integer('quantity');
        });

        $inventory = [
            ['quantity' => ['quantity' => 12, 'letters' => [['letter' => 'A','points' =>  1],['letter' => 'E','points' => 1]]]],
            ['quantity' => ['quantity' => 9, 'letters' => [['letter' => 'O','points' =>  1]]]],
            ['quantity' => ['quantity' => 6, 'letters' => [['letter' => 'I','points' =>  1],['letter' => 'S','points' => 1]]]],
            ['quantity' => ['quantity' => 5, 'letters' => [['letter' => 'N','points' =>  1],['letter' => 'R','points' => 1],['letter' => 'U','points' => 1],['letter' => 'D','points' => 2]]]],
            ['quantity' => ['quantity' => 4, 'letters' => [['letter' => 'L','points' =>  1],['letter' => 'T','points' => 1],['letter' => 'C','points' => 3]]]],
            ['quantity' => ['quantity' => 2, 'letters' => [['letter' => 'G','points' =>  2],['letter' => 'B','points' => 3],['letter' => 'M','points' => 3],['letter' => 'P','points' => 3],['letter' => 'H','points' => 4]]]],
            ['quantity' => ['quantity' => 1, 'letters' => [['letter' => 'F','points' =>  4],['letter' => 'V','points' => 4],
            ['letter' => 'Y','points' => 4],['letter' => 'Q','points' => 5],
            ['letter' => 'J','points' => 8],['letter' => 'Ñ','points' => 8],
            ['letter' => 'X','points' => 8],['letter' => 'Z','points' => 10]]]],
        ];

        foreach ($inventory as $item) {
            $letters = $item['quantity']['letters'];
            foreach ($letters as $letter) {
                $this->point($letter['letter'], $letter['points'], $item['quantity']['quantity']);
            }

        }

    }


    public function point($letter, $points, $quantity){
        DB::table('letters')->insert(array(
            'letter' => $letter,
            'points' => $points,
            'quantity'=> $quantity
        ));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('letters');
    }
}
