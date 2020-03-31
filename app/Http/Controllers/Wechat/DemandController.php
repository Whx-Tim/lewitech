<?php

namespace App\Http\Controllers\Wechat;

use App\Events\RefuseDemand;
use App\Events\TriggerCheckDemandNotice;
use App\Events\TriggerDemandNotice;
use App\Events\TriggerHelpDemandNotice;
use App\Http\Requests\StoreDemandRequest;
use App\Http\Requests\StoreEnrollRequest;
use App\Models\ShareholderDemand;
use App\Models\User;
use App\Models\WechatUserDetail;
use EasyWeChat\Foundation\Application;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DemandController extends Controller
{

    /**
     * 显示发布页面
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showPublish()
    {
        return view('wechat.demand.publish');
    }

    /**
     * 发布需求
     *
     * @param StoreDemandRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function publish(StoreDemandRequest $request)
    {
        $user = Auth::user();
        $dataString = json_encode($request->except(['_token', '_method', 'title', 'content']));
        $demand = $user->demands()->create([
            'title'   => $request->input('title'),
            'content' => $request->input('content'),
            'phone'   => $request->input('phone'),
            'name'    => $request->input('name'),
            'data'    => $dataString
        ]);

        $demand->check()->create(['status' => 0]);

        event(new TriggerCheckDemandNotice('oSiVJ0s1VNlyopzRrJZL4oCHbVVQ', $user->openid, $demand));
        event(new TriggerCheckDemandNotice('oSiVJ0noK8UuIFGPf5Mxirgd1RmY', $user->detail->nickname, $demand));

        return $this->ajaxReturn(0, '发布成功,请等待管理员进行审核', ['demand' => $demand, 'url' => $demand->detailUrl()]);
    }

    /**
     * 审核通过需求
     *
     * @param ShareholderDemand $demand
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkDemand(ShareholderDemand $demand)
    {
        if ($demand->check->status) {
            return $this->ajaxReturn(1, '该需求已经审核过了');
        } else {
            $demand->check()->update(['status' => 1]);
            $users = WechatUserDetail::where('subscribe', 1)->orderBy('id', 'asc')->take(291)->with('user')->get();
            foreach ($users as $item) {
                event(new TriggerDemandNotice($item->user->openid, $demand->title, $demand->detailUrl()));
            }
//            foreach($this->getUsers() as $openid) {
//                event(new TriggerDemandNotice($openid, $demand->title, $demand->detailUrl()));
//            }
//            event(new TriggerDemandNotice('oSiVJ0s1VNlyopzRrJZL4oCHbVVQ', $demand));

            return $this->ajaxReturn(0, '审核成功，正在发送模板消息给所有股东');
        }
    }

    /**
     * 显示需求详情页面
     *
     * @param ShareholderDemand $demand
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function detail(ShareholderDemand $demand)
    {
//        if ($demand->check->status != 1) {
//            return view('wechat.demand.publish');
//        }
        $is_help = $demand->enrolls()->where('user_id',Auth::id())->first();
        $is_self = $demand->user_id==Auth::id() ? true : false;
        $is_check = $demand->check->status;
        $is_refuse = $demand->check->status == 2 ? true: false;
        $demand->data = json_decode($demand->data);

        return view('wechat.demand.detail', compact('demand', 'is_help', 'is_self', 'is_check', 'is_refuse'));
    }

    /**
     * 保存帮助信息
     *
     * @param StoreEnrollRequest $request
     * @param ShareholderDemand $demand
     * @return \Illuminate\Http\JsonResponse
     */
    public function help(StoreEnrollRequest $request, ShareholderDemand $demand)
    {
        $enroll = $demand->enrolls()->where('user_id',Auth::id())->first();
        if ($enroll) {
            return $this->ajaxReturn(1, '你已经帮助过该需求了，请不要重复提交帮助');
        } else {
            $dataString = json_encode($request->except(['_token','_method']));

            $demand->enrolls()->create([
                'name'    => $request->input('name'),
                'phone'   => $request->input('phone'),
                'data'    => $dataString,
                'user_id' => Auth::id()
            ]);
            event(new TriggerHelpDemandNotice($demand));

            return $this->ajaxReturn(0, '帮助成功，需求发布者会收到您的帮助提醒喔~');
        }
    }

    /**
     * 显示帮助页面
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showHelp(ShareholderDemand $demand)
    {
        return view('wechat.demand.help', compact('demand'));
    }

    public function refuse(ShareholderDemand $demand)
    {
        if ($demand->check->status == 2) {
            return $this->ajaxReturn(1, '已经拒绝过，请不要拒绝第二次那么残忍！');
        }

        if ($demand->check->status == 1) {
            return $this->ajaxReturn(2, '请不要搞事情，已经审核过的需求别想拒绝了');
        }

        $demand->check()->update(['status' => 2]);

        event(new RefuseDemand($demand->user->openid, $demand));

        return $this->ajaxReturn(0, '拒绝成功，已经发送了一段狠话给对方');
    }

    /**
     * 获取特定的用户openid
     *
     * @return array
     */
    public function getUsers()
    {
        return [
            'oSiVJ0ob_KzdDPEnWL6bavgMTa9g', //浩衍
            'oSiVJ0u8GXWnzsUJCOmua580NsqM', //伟丁
            'oSiVJ0noK8UuIFGPf5Mxirgd1RmY', //昆华
            'oSiVJ0s1VNlyopzRrJZL4oCHbVVQ', //海鑫
            'oSiVJ0m3TRqc6krP49XjClc8AqBQ', //丹霞
        ];
    }
}
