<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckUmbrellaRegister
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = Auth::user();
        if (is_null($user->phone) & is_null($user->ID_number) & is_null($user->real_name)) {
            return redirect()->route('wechat.umbrella.register');
        }

        return $next($request);
    }
}
