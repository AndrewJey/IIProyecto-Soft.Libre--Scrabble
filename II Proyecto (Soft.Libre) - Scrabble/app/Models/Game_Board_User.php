<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Game_Board_User extends Model
{
    /**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'game_board_user';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['x', 'y', 'letter_id', 'game_user_id'];

	public $timestamps = false;

}
