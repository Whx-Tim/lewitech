<?php

namespace App\Http\Controllers\Wechat;

use App\Repositories\Wechat\UserRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class OAuthController extends Controller
{
    private $code_prefix = 'lewitech_badge_';

    private $code_time = 5;

    public function getCode(Request $request)
    {
        if ($request->has('callback')) {
            $user_info = UserRepository::self()->getWechatInfoBySession();
            $url = urldecode($request->get('callback'));
            $url = $url . '?code='. urlencode($this->generateCode($user_info));

            return redirect()->to($url);
        } else {
            return '参数错误';
        }
    }

    private function generateCode($user_info)
    {
        $time_code = md5(time());
        $token = sha1(md5($this->code_prefix . $time_code));
        Cache::put($token, $user_info, $this->code_time);

        return $time_code;
    }

    public function getUserInfo(Request $request)
    {
        if ($request->has('token')) {
            $token = $request->input('token');
            if (Cache::has($token)) {
                $user_info = Cache::get($token);

                return serialize($user_info);
            } else {

                return 'token已过期';
            }
        } else {
            return '参数错误';
        }
    }
}
