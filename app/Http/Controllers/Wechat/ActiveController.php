<?php

namespace App\Http\Controllers\Wechat;

use App\Events\viewPage;
use App\Http\Requests\StoreActiveEnrollRequest;
use App\Http\Requests\StoreActiveRequest;
use App\Http\Requests\StoreEnrollRequest;
use App\Models\Active;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Qiniu\Auth as QiniuAuth;

class ActiveController extends Controller
{

    /**
     * 显示活动列表
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showList()
    {
        return view('wechat.active.list');
    }

    /**
     * 显示活动发布页面
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showPublish()
    {
        $token = $this->getQiniuToken();

        return view('wechat.active.publish', compact('token'));
    }

    /**
     * 显示活动详情
     *
     * @param Active $active
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showDetail(Active $active)
    {
        $is_apply = $active->enrolls()->where('user_id',Auth::id())->first() ? true : false;
        $is_overdue = Carbon::now()->toDateTimeString() > $active->end_at ? true : false;

        return view('wechat.active.detail', compact('active', 'is_apply', 'is_overdue'));
    }

    /**
     * 显示活动申请页面
     *
     * @param Active $active
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showApply(Active $active)
    {
        return view('wechat.active.apply', compact('active'));
    }

    /**
     * 获取活动列表
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getActiveList(Request $request)
    {
        $actives = Active::where('status', 1)->orderBy('created_at', 'desc')->with('view')->withCount('enrolls')->paginate(10);
//        $data = $actives->toArray();
//        $data['data'] = $actives->each(function ($active) {
//            $active->view;
//            $active->apply_count = $active->enrolls()->count();
//        });
//        $actives = $data;

        return $this->ajaxReturn(0, '获取成功', compact('actives'));
    }

    /**
     * 发布活动
     *
     * @param StoreActiveRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function publish(StoreActiveRequest $request)
    {
        $start = Carbon::parse($request->input('start_time'));
        $end   = Carbon::parse($request->input('end_time'));
        $cut   = Carbon::parse($request->input('end_at'));
        if ($start > $end) {
            return $this->ajaxReturn(1, '活动开始时间不可晚于活动结束时间');
        }
        if ($start < $cut) {
            return $this->ajaxReturn(2, '活动报名截止时间不可晚于活动开始时间');
        }
        $data = $request->except(['_token','_method']);
        $data['start_time'] = $start->toDateTimeString();
        $data['end_time']   = $end->toDateTimeString();
        $data['end_at']     = $cut->toDateTimeString();

        $active = Auth::user()->actives()->create($data);
        $active->view()->create(['count' => 0]);

        return $this->ajaxReturn(0, '发布成功，请等待审核通过', compact('active'));
    }

    /**
     * 活动申请
     *
     * @param StoreEnrollRequest $request
     * @param Active $active
     * @return \Illuminate\Http\JsonResponse
     */
    public function apply(StoreActiveEnrollRequest $request, Active $active)
    {
        if ($active->enrolls()->count() > $active->persons) {
            return $this->ajaxReturn(1, '该活动的报名人数已经满了');
        }
        if ($enroll = $active->enrolls()->where('user_id', Auth::id())->withTrashed()->first()) {
            $enroll->restore();
            $enroll->update($request->except(['_token','_method']));

            return $this->ajaxReturn(0, '报名成功，并修改了您原有的报名信息');
        }

        $enroll = $active->enrolls()->create($request->except(['_token', '_method']));

        return $this->ajaxReturn(0, '报名成功', compact('enroll'));
    }

    /**
     * 获取七牛token
     *
     * @return string
     */
    public function getQiniuToken()
    {
        $auth = new QiniuAuth('d2imjQtPHDgS_pA6ILYLHfZaXrpUz-pj6kCIV49Y', 'nphKPWgKwgcejmkgNqYcui1uIGZwkfb7ZYIl667S');
        $policy = array(
            'saveKey'=>'image/lewitech/active/$(year)$(mon)$(day)_$(hour)$(min)$(sec)_$(fname)',
        );
        $bucket='weijuan';

        return $auth->uploadToken($bucket, null, 1800, $policy);
    }

    /**
     * 搜索活动列表
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $search = $request->get('search');
        if ($search) {
            $actives = Active::where('status',1)->where(function ($query) use ($search){
                return $query->orWhere('name', 'like', '%'.$search.'%')->orWhere('sponsor', 'like', '%'.$search.'%');
            })->orderBy('created_at','desc')->with('view')->get();

            return $this->ajaxReturn(0, '搜索成功', compact('actives'));
        } else {
            return $this->ajaxReturn(1, '搜索失败，请输入搜索参数');
        }
    }

    /**
     * 取消报名
     *
     * @param Active $active
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancelApply(Active $active)
    {
        if ($enroll = $active->enrolls()->where('user_id', Auth::id())->first()) {
            $enroll->delete();

            return $this->ajaxReturn(0, '取消报名成功');
        } else {
            return $this->ajaxReturn(1, '您还没参与该活动');
        }
    }
}
