<?php

namespace App\Http\Middleware;

use App\Models\Blacklist;
use Closure;
use Illuminate\Support\Facades\Auth;

class CheckBlackList
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $type)
    {
        $user = Auth::user();
        if ($blacklist = $user->blacklists()->where('type', $type)->first()) {
            if ($request->ajax()) {
                return response()->json([
                    'code' => '100',
                    'message' => '您因'.$blacklist->description.'，已经没有权限操作'
                ]);
            } else {
                return redirect()->back()->with('error-message', '您因'.$blacklist->description.'，已经没有权限操作');
            }
        } else {
            return $next($request);
        }
    }
}
