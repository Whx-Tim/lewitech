<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class SendSMS
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $phone;

    public $data;

    public $type;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($phone, $data, $type)
    {
        $this->phone = $phone;
        $this->data = $data;
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
