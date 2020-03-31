<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\StoreActiveRequest;
use App\Models\Active;
use App\Models\Config;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;


class ActiveController extends Controller
{
    /**
     * 显示活动管理页面
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $actives = Active::orderBy('created_at', 'desc')->paginate();
        $count   = $actives->total();

        return view('admin.active.index', compact('actives', 'count'));
    }

    /**
     * 显示活动编辑页面
     *
     * @param Active $active
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Active $active)
    {
        return view('admin.active.edit', compact('active'));
    }

    /**
     * 显示活动添加页面
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function add()
    {
        return view('admin.active.add');
    }

    public function showEnroll(Active $active)
    {
        $users = $active->enrolls()->paginate();

        return view('admin.active.enroll.index', compact('users', 'active'));
    }

    /**
     * 显示活动详情页面
     *
     * @param Active $active
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function detail(Active $active)
    {
        return view('admin.active.detail', compact('active'));
    }

    /**
     * 删除活动
     *
     * @param Active $active
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Active $active)
    {
        $active->view()->delete();
        return $active->delete() ? $this->ajaxReturn(0, '删除成功') : $this->ajaxReturn(1, '删除失败');
    }

    /**
     * 添加活动
     *
     * @param StoreActiveRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreActiveRequest $request)
    {
        $active = Active::create($request->except(['_token']));
        $active->view()->create(['count' => 0]);

        return $this->ajaxReturn(0 , '添加成功');
    }

    /**
     * 更新活动
     *
     * @param Active $active
     * @param StoreActiveRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Active $active, StoreActiveRequest $request)
    {
        Cache::forget('active_'. $active->id);
        $active->update($request->except(['_token']));

        return $this->ajaxReturn(0, '更新成功');
    }

    /**
     * 审核通过活动
     *
     * @param Active $active
     * @return \Illuminate\Http\JsonResponse
     */
    public function check(Active $active)
    {
        $active->update(['status' => 1]);

        return $this->ajaxReturn(0, '审核成功');
    }

    public function bannerIndex()
    {
        $banners = Config::activeBanner()->get()->transform(function ($item, $key) {
            $item->content = json_decode($item->content);
            return $item;
        });
        $count = count($banners);

        return view('admin.active.banner.index', compact('count', 'banners'));
    }

    public function addBanner()
    {
        return view('admin.active.banner.add');
    }

    public function editBanner($id)
    {
        $banner = Config::activeBanner()->where('id', $id)->first();
        $banner->content = json_decode($banner->content);

        return view('admin.active.banner.edit', compact('banner'));
    }

    public function storeBanner(Request $request)
    {
        $this->validate($request, [
            'address' => 'required',
            'path'    => 'required',
            'order'   => 'required'
        ], [
            'address.required' => '请输入banner的网址',
            'path.required'    => '请上传banner的图片',
            'order.required'   => '请输入banner的次序'
        ]);
        $content['address'] = $request->input('address');
        $content['path']    = $request->input('path');
        $content['order']   = $request->input('order');
        $content = json_encode($content);
        Config::create(['type' => 'activeBanner', 'content' => $content]);

        return $this->ajaxReturn(0, '添加成功', ['redirect' => route('admin.active.banner.index')]);
    }

    public function updateBanner($id, Request $request)
    {
        $this->validate($request, [
            'address' => 'required',
            'path'    => 'required',
            'order'   => 'required'
        ], [
            'address.required' => '请输入banner的网址',
            'path.required'    => '请上传banner的图片',
            'order.required'   => '请输入banner的次序'
        ]);
        $content['address'] = $request->input('address');
        $content['path']    = $request->input('path');
        $content['order']   = $request->input('order');
        $content = json_encode($content);
        $banner = Config::activeBanner()->where('id', $id)->first();
        $banner->update(['content' => $content]);

        return $this->ajaxReturn(0, '更新成功', ['redirect' => route('admin.active.banner.index')]);
    }
}
