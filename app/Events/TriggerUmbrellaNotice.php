<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class TriggerUmbrellaNotice implements ShouldQueue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $openid;

    public $result;

    public $type;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($openid, $result, $type)
    {
        $this->openid = $openid;
        $this->result = $result;
        $this->type   = $type;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
