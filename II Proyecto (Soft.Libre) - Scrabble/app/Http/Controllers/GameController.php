<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Auth;
use App\Events;


class GameController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $all_games =  \App\Models\Game::getAll();
        $user_games = \App\Models\Game::getGamesByUser(Auth::user()->id);
        return view('games.index', compact('all_games','user_games'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
     return view('games.new');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        try{
            $game = \App\Models\Game::create(\Input::all());
            \Event::fire(new \App\Events\GamesRefresh($game));
            return redirect('gamejoin/'.$game->id);
        } catch (\Illuminate\Database\QueryException $ABError) {
            return redirect('games')->withWarning('Error, try again.');
        } catch (Exception $a) {
            return redirect('games')->withWarning('Error, try again.');
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {

    }

    public function game_join($id){

        $order = \App\Models\Game_User::where('game_id', '=', $id)->count();
        $game = \App\Models\Game::find($id);
        $user_id = Auth::user()->id;
        $need_join = true;
        $game_users = \App\Models\Game_User::where('game_id', '=', $id)->get();

        foreach ($game_users as $game_user) {
            if ($game_user->user_id == $user_id) {
                $need_join = false;
            }
        }

        if (!$need_join) {
            try {
                \Event::fire(new \App\Events\GameJoin($id, $order, $user_id, $need_join));
            } catch (\Illuminate\Database\QueryException $ABError) {
                return redirect('games')->withWarning('Error, you already joined this game.');
            } catch (Exception $a) {
                return redirect('games')->withWarning('Error, try again.');
            }
            $game_channel = $id;
            $status = true;
            if ($game->started) {
                $status = false;
            }
            return view('games.play', compact('game_channel', 'status'));
        }
        elseif($order >= 4){
            return redirect('games')->withWarning('Error, this game is full.');
        }
        elseif ($game->started) {

             return redirect('games')->withWarning('Error, this game is already started.');
        }
        else {
            try {
                \Event::fire(new \App\Events\GameJoin($id, $order, $user_id, $need_join));
            } catch (\Illuminate\Database\QueryException $ABError) {
                return redirect('games')->withWarning('Error, you already joined this game.');
            } catch (Exception $a) {
                return redirect('games')->withWarning('Error, try again.');
            }
            $game_channel = $id;
            $status = true;
            if ($game->started) {
                $status = false;
            }
            return view('games.play', compact('game_channel', 'status'));
        }
    }

    public function game_start($id){

        $game = \App\Models\Game::find($id);
        $players = \App\Models\Game_User::where('game_id', '=', $id)->get();
        $can_start = false;

        if ($game->min_players > $players->count()) {
            return redirect('games')->withWarning('Error, this game need more players to start.');
        }

        if ($game->started) {
            return redirect('games')->withWarning('Error, this game is already started.');
        }

        foreach ($players as $player) {

            if ($player->user_id == Auth::user()->id) {
                $can_start = true;
            }
        }



        $letters = \App\Models\Letter::all();
        \DB::transaction(function () use ($letters, $id){
            foreach ($letters as $letter) {
                $inventory = new \App\Models\Inventory_Letter_Game;
                $inventory->letter_id = $letter->id;
                $inventory->quantity = $letter->quantity;
                $inventory->game_id = $id;
                $inventory->save();
            }
        });

        $game->started = true;
        $game->save();
        \Event::fire(new \App\Events\GameStart($id));
        return redirect('gamejoin/'.$id);
    }

    public static function board_generate($game_id){

        $game_board_letters = \App\Models\Game_User::join('game_board_user', 'game_user.id', '=', 'game_board_user.game_user_id')
        ->where('game_user.game_id', '=', $game_id)
        ->select('game_board_user.x', 'game_board_user.y', 'game_board_user.letter_id');

        $board = [];
        $cont = 1;
        for ($i=1; $i < 16; $i++) {

            for ($j=1; $j < 16; $j++) {

                $type = self::types($cont);
                $board[$i][$j] = ['letter' => null, 'type' => $type->type, 'multiplier' => $type->multiplier];
                $cont ++;
            }
        }

        $game_users = \App\Models\Game_User::where('game_id', '=', $game_id)->get();
        $game_board = collect();

        foreach ($game_users as $game_user) {
            $game_board->push(\App\Models\Game_Board_User::where('game_user_id', '=', $game_user->id)->get());
        }

        foreach ($game_board as $letters) {
            foreach ($letters as $cell) {
                $complete_letter = \App\Models\Letter::find($cell->letter_id);
                $board[$cell->x][$cell->y]['letter']= $complete_letter;
            }
        }
        return $board;
    }

    public static function types($id){

        return \App\Models\Types::find($id);

    }

    public function users_list($game_id){

        $game_users = \App\Models\Game_User::where('game_id', '=', $game_id)->get();
        $users = collect();

        foreach ($game_users as $game_user) {
            $user = \App\Models\User::where('id', '=', $game_user->user_id)->first();
            $turno = $game_user['have_turn'] ? 'Si' : 'No';
            $users->push(['first_name' => $user['first_name'], 'points' => $game_user['points'], 'turno' => $turno ]);
        }
        return $users;
    }

    public static function validate_word($game_id){
        $letters = \Input::get('letters');

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

        if (self::have_give_up($game_id)) {
           return response()->json(['errors' => true, 'msg' => 'Usted ya se rindió.']);
        }

        $game_users = \App\Models\Game_User::where('game_id', '=', $game_id)->get();
        $turn = 0;
        foreach ($game_users as $game_user) {

            $turn += \App\Models\Game_Board_User::where('game_user_id', '=', $game_user->id)->count();

        }

        $board = self::board_generate($game_id);

        foreach ($letters as $letter) {
            $board[$letter['x']][$letter['y']]['letter'] = $letter['letter'];
        }


        $word = '';

        foreach ($letters as $letter) {
            $word .=  $letter['letter'];
        }

        if ($turn == 0) {
            if ($letters[0]['x'] != 8 || $letters[0]['y'] != 8) {

                return response()->json(['errors' => true, 'msg' => 'La posición es incorrecta']);

            }

            elseif (self::check_word($word)) {
                $points = 0;
                $word_mult = 0;
                foreach ($letters as $letter) {
                    $letter_id = \App\Models\Letter::where('letter', $letter['letter'])->get();
                    $game_user_id = \App\Models\Game_User::where('game_id', $game_id)->where('user_id', Auth::user()->id)->first();
                    \App\Models\Game_Board_User::create(['x' => $letter['x'], 'y' => $letter['y'], 'letter_id' => $letter_id[0]['id'], 'game_user_id' => $game_user_id['id']]);
                    if ($board[$letter['x']][$letter['y']]['type'] == 1) {
                        $points += $letter_id[0]['points'] * $board[$letter['x']][$letter['y']]['multiplier'];
                    }elseif($board[$letter['x']][$letter['y']]['type'] == 2){
                        $word_mult += $board[$letter['x']][$letter['y']]['multiplier'];
                        $points += $letter_id[0]['points'];
                    }else{
                        $points += $letter_id[0]['points'];
                    }
                    $inventory = \App\Models\Letter_User::where('game_id', $game_id)->where('letter_id',$letter_id[0]['id'])->first();
                    $inventory->delete();
                }
                $game_user_id = \App\Models\Game_User::where('game_id', $game_id)->where('user_id', Auth::user()->id)->first();
                if ($word_mult > 0) {
                    $points = $points * $word_mult;
                }
                $game_user_id['points'] += $points;
                $game_user_id->save();
                self::cleanConsTurn($game_id);
                self::turn_pass($game_id);
                self::cleanConsTurn($game_id);
                return response()->json(['errors' => false, 'msg' => 'La palabra es correcta']);
            }

            else {

                return response()->json(['errors' => true, 'msg' => 'La palabra no se encuentra en el diccionario']);

            }
        }
        else{

            $is_ok = true;

            for ($i=1; $i < 16; $i++) {
                $word = '';

                for ($j=1; $j < 16; $j++) {

                    if (is_string($board[$i][$j]['letter'])) {
                        $word .= $board[$i][$j]['letter'];
                    }
                    elseif(is_string($board[$i][$j]['letter']['letter'])){
                        $word .= $board[$i][$j]['letter']['letter'];
                    }
                    elseif (strlen($word) > 1) {

                        if (!self::check_word($word)) {
                            $is_ok = false;
                        }
                    }else{
                        $word = '';
                    }

                }

                if (is_string($board[$i][$j-1]['letter'])) {
                    if (strlen($word) > 1) {
                     
                        if (!self::check_word($word)) {
                            $is_ok = false;
                        }

                    }    
                }elseif(is_string($board[$i][$j-1]['letter']['letter'])){
                    if (strlen($word) > 1) {
                     
                        if (!self::check_word($word)) {
                            $is_ok = false;
                        }

                    }
                }
            }
            for ($i=1; $i < 16; $i++) {
                $word = '';

                for ($j=1; $j < 16; $j++) {

                    if (is_string($board[$j][$i]['letter'])) {
                        $word .= $board[$j][$i]['letter'];
                    }
                    elseif(is_string($board[$j][$i]['letter']['letter'])){
                        $word .= $board[$j][$i]['letter']['letter'];
                    }
                    elseif (strlen($word) > 1){

                        if (!self::check_word($word)) {
                            $is_ok = false;
                        }

                    }else{
                        $word = '';
                    }
                }
                if (is_string($board[$j-1][$i]['letter'])) {
                    if (strlen($word) > 1) {
                     
                        if (!self::check_word($word)) {
                            $is_ok = false;
                        }

                    }    
                }elseif(is_string($board[$j-1][$i]['letter']['letter'])){
                    if (strlen($word) > 1) {
                     
                        if (!self::check_word($word)) {
                            $is_ok = false;
                        }

                    }
                }
            }
            if ($is_ok) {
                $points = 0;
                $word_mult = 0;
                foreach ($letters as $letter) {
                    $letter_id = \App\Models\Letter::where('letter', $letter['letter'])->get();
                    $game_user_id = \App\Models\Game_User::where('game_id', $game_id)->where('user_id', Auth::user()->id)->first();
                    \App\Models\Game_Board_User::create(['x' => $letter['x'], 'y' => $letter['y'], 'letter_id' => $letter_id[0]['id'], 'game_user_id' => $game_user_id['id']]);
                    if ($board[$letter['x']][$letter['y']]['type'] == 1) {
                        $points += $letter_id[0]['points'] * $board[$letter['x']][$letter['y']]['multiplier'];
                    }elseif($board[$letter['x']][$letter['y']]['type'] == 2){
                        $word_mult += $board[$letter['x']][$letter['y']]['multiplier'];
                        $points += $letter_id[0]['points'];
                    }else{
                        $points += $letter_id[0]['points'];
                    }
                    $inventory = \App\Models\Letter_User::where('game_id', $game_id)->where('letter_id',$letter_id[0]['id'])->first();
                    $inventory->delete();
                }
                $game_user_id = \App\Models\Game_User::where('game_id', $game_id)->where('user_id', Auth::user()->id)->first();
                if ($word_mult > 0) {
                    $points = $points * $word_mult;
                }
                $game_user_id['points'] += $points;
                $game_user_id->save();
                self::cleanConsTurn($game_id);
                self::turn_pass($game_id);
                self::cleanConsTurn($game_id);
                return response()->json(['errors' => false, 'msg' => 'La palabra es correcta']);
            }
            else{

                return response()->json(['errors' => true, 'msg' => 'La palabra no se encuentra en el diccionario']);

            }

        }


    }

    public static function check_word($word)
    {

        $count = \App\Models\Dictionary::where('word', '=', strtolower($word))->count();
        if ($count == 1) {
            return true;
        }
        else {
            return false;
        }

    }

    public function give_up($game_id){
        if (self::has_winner($game_id)) {
            $list = \App\Models\Game_User::where('game_id', $game_id)->where('has_won', 1)->first();
            $user_win = \App\Models\User::where('id', '=', $list->user_id)->first();
            $msg = 'Ya existe un ganador y es ' . $user_win->first_name . '.';
           return response()->json(['errors' => true, 'msg' => $msg]);
        }
        if (self::has_ended($game_id)) {
           return response()->json(['errors' => true, 'msg' => 'El juego termino']);
        }
        self::turn_pass($game_id);
        \Event::fire(new \App\Events\GiveUp(Auth::user()->id, $game_id));
        return redirect('games');
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

    public static function has_winner($game_id){
        $games = \App\Models\Game_User::where('game_id', $game_id)->get();
        foreach ($games as $key) {
            if ($key['has_won']){
                return true;
            }
        }
        return false;
    }

    public static function has_ended($game_id){
        $game = \App\Models\Game::find($game_id);
        if ($game->ended){
            return true;
        }
        return false;
    }

    public static function have_turn($game_id){
        $games = \App\Models\Game_User::where('game_id', $game_id)->where('user_id', Auth::user()->id)->first();
            if ($games['have_turn']){
                return false;
            }
        return true;
    }

    public static function have_give_up($game_id){
        $games = \App\Models\Game_User::where('game_id', $game_id)->where('user_id', Auth::user()->id)->first();
            if ($games['has_left']){
                return true;
            }
        return false;
    }

    public static function cleanConsTurn($game_id){
        $game_users = \App\Models\Game_User::where('game_id', $game_id)->where('has_left',0)->get();
        foreach ($game_users as $key) {
            $key->cons_turn = false;
            $key->save();
        }
    }
}
