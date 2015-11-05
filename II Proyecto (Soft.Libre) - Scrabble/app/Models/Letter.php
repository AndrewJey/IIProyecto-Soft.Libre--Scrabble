<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Letter extends Model
{
    /**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'letters';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['letter', 'points', 'quantity'];

	public $timestamps = false;

}
