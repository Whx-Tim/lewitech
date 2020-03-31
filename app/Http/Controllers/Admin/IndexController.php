<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\adminLoginAndRegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Qiniu\Auth as QiniuAuth;

class IndexController extends Controller
{
    /**
     * 显示后台系统主页
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('admin.index');
    }

    /**
     * 显示后台登录页面
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showLogin()
    {
        return view('admin.login');
    }

    /**
     * 注销
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout()
    {
        Auth::logout();

        return redirect()->route('admin.login');
    }

    /**
     * 管理员登录
     *
     * @param adminLoginAndRegisterRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function adminLogin(adminLoginAndRegisterRequest $request)
    {
        if (Auth::attempt(['name' => $request->input('name'), 'password' => $request->input('password')])) {
            return $this->ajaxReturn(0, '登录成功', ['redirect' => route('admin.index')]);
        } else {
            if (User::where('name', $request->input('name'))->first()) {
                return $this->ajaxReturn(1, '登录失败,用户名与密码错误');
            } else {
                return $this->ajaxReturn(2, '找不到用户');
            }

        }
    }

    /**
     * 开通管理员权限
     *
     * @param User $user
     * @param adminLoginAndRegisterRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(User $user, adminLoginAndRegisterRequest $request)
    {
        $user->update(['name' => $request->input('name'), 'password' => bcrypt($request->input('password')), 'adminset' => 5]);

        return $this->ajaxReturn(0, '注册管理员成功');
    }

    /**
     * 获取七牛token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getQiniuToken()
    {
        $auth = new QiniuAuth('d2imjQtPHDgS_pA6ILYLHfZaXrpUz-pj6kCIV49Y', 'nphKPWgKwgcejmkgNqYcui1uIGZwkfb7ZYIl667S');
        $policy = array(
            'saveKey'=>'image/lewitech/active/$(year)$(mon)$(day)_$(hour)$(min)$(sec)_$(fname)',
        );
        $bucket='weijuan';
        $token = $auth->uploadToken($bucket, null, 1800, $policy);
        return $this->ajaxReturn(0, '获取成功', compact('token'));
    }
}
