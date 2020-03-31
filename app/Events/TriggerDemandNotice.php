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

class TriggerDemandNotice
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    public $openid;

    public $title;

    public $url;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($openid, $title, $url)
    {
        $this->openid = $openid;
        $this->title  = $title;
        $this->url    = $url;
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
