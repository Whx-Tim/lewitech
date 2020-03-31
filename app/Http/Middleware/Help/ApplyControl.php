<?php

namespace App\Http\Middleware\Help;

use App\Services\HelpService;
use Closure;

class ApplyControl
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
        $helpService = new HelpService();
        if (!$request->user()->detail->is_shareholder) {
            return redirect()->route('wechat.help.index')->with(['error_message' => '现阶段只有乐微股东可以参与互助']);
        }
        if ($helpService->is_apply($request->user())) {
            return redirect()->route('wechat.help.index')->with(['error_message' => '您已经参与过互助了']);
        }


        return $next($request);
    }
}
