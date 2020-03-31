<?php

namespace App\Http\Controllers\Admin;

use App\Events\TriggerDemandNotice;
use App\Http\Requests\StoreDemandRequest;
use App\Models\ShareholderDemand;
use App\Models\WechatUserDetail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DemandController extends Controller
{
    public function index()
    {
        $demands = ShareholderDemand::orderBy('created_at')->with('user.detail')->paginate();
        $count   = $demands->total();

        return view('admin.demand.index', compact('demands', 'count'));
    }

    public function edit(ShareholderDemand $demand)
    {
        return view('admin.demand.edit', compact('demand'));
    }

    public function add()
    {
        return view('admin.demand.add');
    }

    public function detail(ShareholderDemand $demand)
    {
        return view('admin.demand.detail', compact('demand'));
    }

    public function delete(ShareholderDemand $demand)
    {
        $demand->delete();

        return $this->ajaxReturn(0, '删除成功');
    }

    public function update(ShareholderDemand $demand, StoreDemandRequest $request)
    {
        return $demand->update($request->except(['_token'])) ? $this->ajaxReturn(0, '更新成功') : $this->ajaxReturn(1, '更新失败');
    }

    public function store(StoreDemandRequest $request)
    {
        $demand = ShareholderDemand::create($request->except(['_token']));

        return $this->ajaxReturn(0, '添加成功', ['redirect' => $demand->adminHomeUrl()]);
    }

    public function check(ShareholderDemand $demand)
    {
        if ($demand->check->status) {
            return $this->ajaxReturn(1, '该需求已经审核过了');
        } else {
            $demand->check()->update(['status' => 1]);
            $users = WechatUserDetail::where('subscribe', 1)->with('user')->get();
            foreach ($users as $item) {
                event(new TriggerDemandNotice($item->user->openid, $demand->title, $demand->detailUrl()));
            }

            return $this->ajaxReturn(0, '审核成功，正在发送模板消息给所有股东');
        }
    }

    public function enrollsUsers(ShareholderDemand $demand)
    {
        $users = $demand->enrolls()->orderBy('created_at', 'desc')->with('user.detail')->paginate();
        $count = $users->total();
        $page_title = '需求报名用户列表';

        return view('admin.users', compact('users', 'count', 'page_title'));
    }

    public function viewUsers(ShareholderDemand $demand)
    {
        $users = $demand->viewUsers()->orderBy('created_at', 'desc')->with('user.detail')->paginate();
        $count = $users->total();
        $page_title = '浏览需求用户列表';

        return view('admin.users', compact('users', 'count', 'page_title'));
    }
}
