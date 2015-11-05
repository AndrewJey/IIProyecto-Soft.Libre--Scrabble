<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory_Letter_Game extends Model
{
    /**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'inventory_letter_game';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['letter_id', 'quantity', 'game_id'];

	public $timestamps = false;

}
