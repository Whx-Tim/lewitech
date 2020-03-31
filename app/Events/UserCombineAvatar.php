<?php

namespace App\Events;

use App\Models\School;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class UserCombineAvatar implements ShouldQueue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;

    public $school;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user, School $school)
    {
        $this->user = $user;
        $this->school = $school;
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
