<?php

namespace App\Listeners;

use App\Events\UserApplySign;
use App\Services\SignService;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class GenerateApplySharePoster implements ShouldQueue
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
     * @param  UserApplySign  $event
     * @return void
     */
    public function handle(UserApplySign $event)
    {
        $sign_service = new SignService();
        $sign_service->generateApplySharePoster($event->user);
    }
}
