<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Letter_User extends Model
{
    /**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'letter_user';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['letter_id', 'user_id', 'game_id'];

	public $timestamps = false;

}
