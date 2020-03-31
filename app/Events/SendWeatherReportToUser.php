<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class SendWeatherReportToUser implements ShouldQueue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;

    public $sign;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user, $sign)
    {
        $this->user = $user;
        $this->sign = $sign;
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
