<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Auth;
use App\Events;

class LetterController extends Controller
{

    public function distribute_letters($game_id)
    {
        $games = \App\Models\Game_User::where('game_id', $game_id)->where('user_id', Auth::user()->id)->first();
        if ($games['has_left']){
            return [];
        }
        $letters_user = \App\Models\Letter_User::where('game_id', '=', $game_id)->where('user_id', Auth::user()->id)->get(['letter_id'])->toArray();
        $flattened_array = array();

        array_walk_recursive($letters_user, function($a) use (&$flattened_array) { $flattened_array[] = $a; });

        $user_letters = \App\Models\Letter::whereIn('id', $flattened_array)->get(['letter', 'points']);

        if (!$user_letters) {

            $user_letters = collect();

        }

        if ($user_letters->count() <= 6) {
            return $this->first_inventory($game_id, $user_letters);
        }

        return $user_letters;
    }

    public function first_inventory($game_id, $user_letters){
        $game_inventory = \App\Models\Inventory_Letter_Game::where('quantity', '>', 0)->where('game_id',$game_id)->get();
        while ($user_letters->count() <= 6) {

            $letter = $game_inventory->random();
            $letter->quantity --;

            if ($letter->quantity >= 0) {

                $user_letters_save = new \App\Models\Letter_User;
                $user_letters_save->letter_id = $letter->letter_id;
                $user_letters_save->user_id = Auth::user()->id;
                $user_letters_save->game_id = $game_id;

                $letter->save();
                $user_letters->push('');
                $user_letters_save->save();
            }
        }
        return $this->distribute_letters($game_id);
    }

    public static function random_letter($game_id)
    {
        if (self::have_turn($game_id)) {
           return response()->json(['errors' => true, 'msg' => 'NO es su turno.']);
        }
        $user_inventory = \App\Models\Letter_User::where('game_id',$game_id)->where('user_id', Auth::user()->id)->get();
        for ($i=0; $i < 4; $i++) {
            $letter = $user_inventory->random();
            $letter_returned = \App\Models\Inventory_Letter_Game::where('letter_id', $letter->letter_id)->where('game_id',$game_id)->first();
            $letter_returned->quantity ++;
            $letter_returned->save();
            $letter->delete();
        }
        self::cleanConsTurn($game_id);
        self::turn_pass($game_id);
        self::cleanConsTurn($game_id);
        
    }

    public static function have_turn($game_id){
        $games = \App\Models\Game_User::where('game_id', $game_id)->where('user_id', Auth::user()->id)->first();
            if ($games['have_turn']){
                return false;
            }
        return true;
    }

    public static function cleanConsTurn($game_id){
        $game_users = \App\Models\Game_User::where('game_id', $game_id)->where('has_left',0)->get();
        foreach ($game_users as $key) {
            $key->cons_turn = false;
            $key->save();
        }
    }

    public static function turn_pass($game_id){
        if (self::has_winner($game_id)) {
            $list = \App\Models\Game_User::where('game_id', $game_id)->where('has_won', 1)->first();
            $user_win = \App\Models\User::where('id', '=', $list->user_id)->first();
            $msg = 'Ya existe un ganador y es ' . $user_win->first_name . '.';
           return response()->json(['errors' => true, 'msg' => $msg]);
        }
        if (self::has_ended($game_id)) {
           return response()->json(['errors' => true, 'msg' => 'El juego termino']);
        }
        if (self::have_turn($game_id)) {
           return response()->json(['errors' => true, 'msg' => 'NO es su turno.']);
        }
        \Event::fire(new \App\Events\TurnPass(Auth::user()->id, $game_id));
    }

    public static function has_ended($game_id){
        $game = \App\Models\Game::find($game_id);
        if ($game->ended){
            return true;
        }
        return false;
    }

    public static function has_winner($game_id){
        $games = \App\Models\Game_User::where('game_id', $game_id)->get();
        foreach ($games as $key) {
            if ($key['has_won']){
                return true;
            }
        }
        return false;
    }

}
