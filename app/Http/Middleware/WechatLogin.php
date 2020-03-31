<?php

namespace App\Http\Middleware;

use App\Repositories\Wechat\UserRepository;
use Closure;
use Illuminate\Support\Facades\Auth;

class WechatLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @throws \Exception
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = UserRepository::self()->createOrUpdateFromSession();

        Auth::loginUsingId($user->id);

        return $next($request);
    }
}
