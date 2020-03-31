<?php

namespace App\Listeners;

use App\Events\TriggerWarning;
use Carbon\Carbon;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendWaringMail
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
     * @param  TriggerWarning  $event
     * @return void
     */
    public function handle(TriggerWarning $event)
    {
        $content = $event->message;
        Mail::raw('warning info', function ($message) use ($content) {
            $message->to('598357301@qq.com')->subject(Carbon::now()->toDateTimeString().': '.$content);
        });
    }
}
