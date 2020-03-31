<?php

namespace App\Events;

use App\Models\GetUp;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class TriggerGetUpNotice
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $openid;

    public $getUp;

    public $type;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($openid, GetUp $getUp, $type)
    {
        $this->openid = $openid;
        $this->getUp = $getUp;
        $this->type = $type;
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
