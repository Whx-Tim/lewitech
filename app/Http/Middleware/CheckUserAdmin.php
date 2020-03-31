<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckUserAdmin
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
        if (Auth::check()) {
            if (Auth::user()->adminset >= 5) {
                return $next($request);
            }
            else {
                return redirect()->route('admin.login');
            }
        } else {
            return redirect()->route('admin.login');
        }


    }
}
