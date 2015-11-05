<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::group(['middleware' => ['\App\Http\Middleware\ValidateUser']], function()
{
	Route::get('/letters/{letter_id}/{game_id}', 'LetterController@return_letter');

	Route::get('/games', 'GameController@index');

	Route::get('/', 'GameController@index');
	Route::get('/home', 'GameController@index');
	Route::get('/games', 'GameController@index');
	Route::get('/board/{game_id}', 'GameController@board_generate');
	Route::get('/games/new', 'GameController@create');
	Route::post('/games', 'GameController@store');
	Route::get('/gamejoin/{id}', 'GameController@game_join');
	Route::get('/gamestart/{id}', 'GameController@game_start');

	Route::get('/gameBoard/{game_id}', 'GameController@board_generate');
	Route::get('/playersList/{game_id}', 'GameController@users_list');
	Route::get('/letterList/{game_id}', 'LetterController@distribute_letters');
	Route::post('/validateword/{game_id}', 'GameController@validate_word');
	Route::post('/giveup/{game_id}', 'GameController@give_up');
	Route::post('/turnpass/{game_id}', 'GameController@turn_pass');
	Route::post('/random_letter/{game_id}', 'LetterController@random_letter');
});

Route::get('/register', 'Auth\AuthController@getRegister');
Route::post('/register', 'Auth\AuthController@postRegister');
Route::get('/auth/login', 'Auth\AuthController@getLogin');
Route::post('/auth/login', 'Auth\AuthController@postLogin');
Route::get('/auth/logout', 'Auth\AuthController@getLogout');
Route::get('/password/email', 'Auth\PasswordController@getEmail');
Route::post('/password/email', 'Auth\PasswordController@postEmail');
Route::get('/reset/{code}', 'Auth\PasswordController@getReset');
Route::post('/reset', 'Auth\PasswordController@postReset');
