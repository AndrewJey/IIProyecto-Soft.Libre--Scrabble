<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class GiveUp extends Event implements ShouldBroadcast
{
    use SerializesModels;

    public $data;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($user_id, $game_id)
    {

        $user = \App\Models\User::find($user_id);
        $game_user = \App\Models\Game_User::where('user_id', $user_id)->where('game_id', $game_id)->first();
        $game_user['has_left'] = true;
        $game_user->save();

        $game_users = \App\Models\Game_User::where('game_id', $game_id)->get();
        $cont = 0;

        foreach ($game_users as $game_user) {
            if ($game_user['has_left']) {
                $cont ++;
            }
        }
        $user_list = \App\Models\Game_User::where('game_id', $game_id)->get();
        $msg = 'El jugador ' . $user->first_name . ' se ha rendido';
        if ($cont == count($user_list) - 1) {
            $winner = \App\Models\Game_User::where('has_left', '=',false)->where('game_id', $game_id)->first();
            $winner['has_won'] = true;
            $winner->save();
            $winner_name = \App\Models\User::find($winner['user_id']);
            $msg .= ', y El jugador ' . $winner_name->first_name . ' ha ganado';
        }

        $games = \App\Models\Game_User::where('game_id', $game_id)->where('user_id', $user_id)->first();
        if (!$games['has_left']){
            $letters_user = \App\Models\Letter_User::where('game_id', '=', $game_id)->where('user_id', $user_id)->get(['letter_id']);
            foreach ($letters_user as $key) {
                $user_letters_save = \App\Models\Inventory_Letter_Game::where('game_id', $game_id)->where('letter_id', $key->letter_id)->first();
                $user_letters_save->quantity ++;
                $user_letters_save->save();
            }
        }
        $this->data = [
            'game_id' => $game_id,
            'msg' => $msg
        ];
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [$this->data['game_id']];
    }
}
