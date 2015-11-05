<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class GameJoin extends Event implements ShouldBroadcast
{
    use SerializesModels;

    public $data;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($game_id, $order, $user_id, $need_join)
    {

        if ($need_join) {
            
            $values = ['game_id' => $game_id, 'order' => $order, 'user_id' => $user_id, 'have_turn' => 0];
            if ($order == 0) {
                $values['have_turn'] = 1;
            }
            \App\Models\Game_User::create($values);
            
            $user = \App\Models\User::find($user_id);
            
            $this->data = [
                'game_id' => $game_id,
                'user' => $user
            ];

        }
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
