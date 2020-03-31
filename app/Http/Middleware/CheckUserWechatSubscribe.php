<?php

namespace App\Http\Middleware;

use App\Repositories\Wechat\UserRepository;
use Closure;
use EasyWeChat\Foundation\Application;
use Illuminate\Support\Facades\Auth;

class CheckUserWechatSubscribe
{
    /**
     * @var Application
     */
    private $wechat;

    public function __construct()
    {
        $this->wechat = app('wechat');
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $type = 'other')
    {
        return call_user_func_array([new self(), $type], [$request, $next]);
    }

    protected function other($request, Closure $next)
    {
        $user = Auth::user();
        $userService = $this->wechat->user;
        $wechat_user = $userService->get($user->openid);
        if ($wechat_user->subscribe == 0) {
            return redirect()->route('wechat.subscribe');
        }

        return $next($request);
    }

    protected function badge($request, Closure $next)
    {
        $user = UserRepository::self()->updateOrCreateFromWechat();
        if ($user->detail->subscribe) {
            return $next($request);
        } else {
            return redirect()->route('wechat.badge.world.subscribe');
        }
//        $userService = $this->wechat->user;
//        $wechat_user = $userService->get($user->openid);
//        if ($wechat_user->subscribe == 0) {
//            return redirect()->route('wechat.badge.subscribe');
//        } else {
//            return $next($request);
//        }
    }
}
