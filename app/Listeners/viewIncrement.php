<?php

namespace App\Listeners;

use App\Events\viewPage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class viewIncrement implements ShouldQueue
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
     * @param  viewPage  $event
     * @return void
     */
    public function handle(viewPage $event)
    {
        $model = $event->model;
        if ($model->view) {
            $model->view()->increment('count');
        } else {
            $model->view()->create(['count' => 0]);
        }
    }
}
