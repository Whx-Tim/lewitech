<?php

namespace App\Listeners;

use App\Events\UserViewPage;
use Auth;
use Carbon\Carbon;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class AddUserViewRelation
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
     * @param  UserViewPage  $event
     * @return void
     */
    public function handle(UserViewPage $event)
    {
        $model = $event->model;
        if ($model->viewUsers()->where('user_id',Auth::id())->first()) {
            $model->viewUsers()->where('user_id',Auth::id())->update(['updated_at' => Carbon::now()->toDateTimeString()]);
        } else {
            if (!is_null(Auth::id())) {
                $model->viewUsers()->create(['user_id' => Auth::id()]);
            }
        }
    }
}
