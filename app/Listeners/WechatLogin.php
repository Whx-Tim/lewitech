<?php

namespace App\Listeners;

use App\Events\TriggerWarning;
use App\Events\WechatUserLogin;
use App\Models\User;
use App\Repositories\Wechat\UserRepository;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

class WechatLogin
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
     * @param  WechatUserLogin  $event
     * @throws \Exception
     * @return void
     */
    public function handle(WechatUserLogin $event)
    {
        if (empty($event->type)) {
            $user = UserRepository::self()->createOrUpdateFromSession();

            Auth::loginUsingId($user->id);
        } else {
            $this->{$event->type}($event);
        }
    }

    /**
     * 校徽生成器登录处理
     *
     * @param WechatUserLogin $event
     * @throws \Exception
     */
    private function badge(WechatUserLogin $event)
    {
        $user = UserRepository::self()->updateOrCreateFromWechat();

        Auth::loginUsingId($user->id);
    }
}
