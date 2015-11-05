<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class TestEvent extends Event implements ShouldBroadcast
{
    use SerializesModels;

    public $data;
    

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($value,$user_id)
    {
        $this->data = [
            'power' => $value,
            'saso' => $user_id
        ];
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return ['test-channel'];
    }
}
