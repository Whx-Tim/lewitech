<?php

namespace App\Listeners;

use App\Events\UserCombineAvatar;
use App\Repositories\SchoolBadgeRepository;
use App\Repositories\SchoolRepository;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class GenerateBadgeUserShareImage
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  UserCombineAvatar  $event
     * @throws \Exception
     * @return void
     */
    public function handle(UserCombineAvatar $event)
    {
        SchoolRepository::self()->combineShare($event->user, $event->school, true);
    }
}
