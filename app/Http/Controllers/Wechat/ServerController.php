<?php

namespace App\Http\Controllers\Wechat;

use App\Models\User;
use App\Models\WechatDeal;
use App\Models\WechatMessage;
use App\Models\WechatUserDetail;
use App\Repositories\WechatDealRepository;
use Carbon\Carbon;
use EasyWeChat\Foundation\Application;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class ServerController extends Controller
{
    private $wechat;

    /**
     * 初始化easywechat基类
     *
     * ServerController constructor.
     */
    public function __construct(Application $wechat)
    {
        $this->wechat = $wechat;
    }

    /**
     * 微信交互服务
     *
     * @return mixed
     * @throws \EasyWeChat\Core\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Server\BadRequestException
     */
    public function server()
    {
        $server = $this->wechat->server;
        $server->setMessageHandler(function ($message) {
            return WechatMessage::messageHandle($message);
        });

        $response = $server->serve();

        return $response;
    }

    public function payResponse(Request $request)
    {
        $wechat_deal = new WechatDealRepository(new WechatDeal());
        return $wechat_deal->response();
    }

    /**
     * 获取用户信息并插入数据库
     */
    public function getUsersAndInsert()
    {
        $userService = $this->wechat->user;
        $users = $userService->lists();
        $userArray = [];
        $detailArray = [];
        foreach ($users['data']['openid'] as $key => $openid) {
            $userInfo = $userService->get($openid);
            array_push($userArray, ['openid' => $openid,'created_at' => Carbon::now()->toDateTimeString(), 'updated_at' => Carbon::now()->toDateTimeString()]);
            array_push($detailArray, [
                'head_img' => $userInfo->headimgurl,
                'nickname' => $userInfo->nickname,
                'sex'      => $userInfo->sex,
                'city'     => $userInfo->city,
                'country'  => $userInfo->country,
                'language' => $userInfo->language,
                'subscribe'=> $userInfo->subscribe,
                'subscribe_time' => $userInfo->subscribe_time,
                'user_id'  => $key+1
            ]);
            }
        User::insert($userArray);
        WechatUserDetail::insert($detailArray);

        dd('successful');
    }


    public function redirectToCallback(Request $request)
    {
        $url = $request->input('callback_url');
        if (!$url) {
            return $this->ajaxReturn(1, 'callback_url参数错误');
        }

        $user = session('wechat.oauth_user');
        $user = [
            'openid'   => $user->getId(),
            'nickname' => $user->getNickname(),
            'head_img' => $user->getAvatar(),
        ];
        $code = str_random(32);
        Cache::put($code, $user, 20);

        return Redirect::to($url.'?code='.$code);
    }

    public function acceptAuthCode(Request $request)
    {
        $code = $request->input('code');
        Log::warning('测试code：'.$code);


        if (Cache::has($code)) {
            $user = Cache::get($code);
            if (empty($user)) {
                return $this->ajaxReturn(2, '用户信息不存在');
            } else {
                return $this->ajaxReturn(0, '获取成功', $user);
            }
        } else {
            return $this->ajaxReturn(1, 'code已过期');
        }
    }

    public function callbackTest(Request $request)
    {
        $code = $request->input('code');

        $data = $this->send_post('http://wx.lewitech.cn/wechat/oauth/userinfo', compact('code'));
        dd($data);
    }

    public function send_post($url, $data)
    {
        $postData = http_build_query($data);
        $options  = [
            'http' => [
                'method'  => 'POST',
                'header'  => 'Content-type:application/x-www-form-urlencoded',
                'content' => $postData,
                'timeout' => 15*60
            ]
        ];
        $context = stream_context_create($options);
        return file_get_contents($url, false, $context);
    }

    public function getJSSDK()
    {
        $js = $this->wechat->js;
        header("Access-Control-Allow-Origin:*");
        return $this->ajaxReturn(0, '获取成功', compact('js'));
    }

    public function showHeightExam()
    {
        $js = $this->wechat->js;
        return view('heightExam', compact('js'));
    }

    public function showSubscribe()
    {
        return view('wechat.subscribe');
    }


}
