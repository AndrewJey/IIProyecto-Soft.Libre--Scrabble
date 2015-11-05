<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class TurnPass extends Event implements ShouldBroadcast
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
        $game_users = \App\Models\Game_User::where('game_id', $game_id)->where('has_left',0)->get();
        for ($i=0; $i < $game_users->count(); $i++) {
            if ($game_users[$i]['user_id'] == $user_id) {
                    $game_users[$i]['have_turn'] = false;
                    $game_users[$i]['cons_turn'] = true;
                    $game_users[$i]->save();
                    if ($i+1 == $game_users->count()) {
                        $game_users[0]['have_turn'] = true;
                        $game_users[0]->save();
                    }
                    else{
                        $game_users[$i+1]['have_turn'] = true;
                        $game_users[$i+1]->save();
                    }
            }
        }
        $msg = 'El jugador ' . $user->first_name . ' paso turno.';
        $cont = 0;
        foreach ($game_users as $key) {
            if ($key->cons_turn) {
                $cont ++;
            }
        }

        if ($cont == $game_users->count()) {
           $game = \App\Models\Game::find($game_id);
           $game->ended = true;
           $game->save();
           $msg = 'El juego a terminado.';
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
