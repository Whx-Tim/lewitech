<?php

namespace App\Events;

use App\Models\ShareholderDemand;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class RefuseDemand
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $openid;

    public $demand;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($openid,ShareholderDemand $demand)
    {
        $this->openid = $openid;
        $this->demand = $demand;
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
