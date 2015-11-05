<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Game extends Model
{
    /**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'games';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['active', 'started', 'min_players', 'name'];

	public $timestamps = false;

	public function game_user()
    {
        return $this->hasMany('App\Models\Game_User');
    }

	public static function getAll(){
		return self::where('started', '=', 'false')->get();
	}

	public static function getGamesByUser($user_id){
		return self::select()
			->join('game_user', 'games.id', '=', 'game_user.game_id')
			->where('game_user.user_id', $user_id)->get();
	}

}