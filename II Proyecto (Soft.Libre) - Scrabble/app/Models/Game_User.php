<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Game_User extends Model
{
    /**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'game_user';

	public function game()
    {
        return $this->belongsTo('App\Models\Game');
    }

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['game_id', 'order', 'user_id', 'have_turn', 
	'has_left', 'has_won', 'points'];

	public $timestamps = false;

}
