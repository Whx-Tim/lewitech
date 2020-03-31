<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('created_at', 'desc')->with('detail')->paginate();
        $count = $users->total();

        return view('admin.user.index', compact('users', 'count'));
    }

    public function showDetail(User $user, Request $request)
    {
        if (!is_null($from = $request->get('from'))) {
            if ($from == 'active') {
                $path = [
                    'url' => route('admin.active.enroll.index', ['active' => $request->get('id')]),
                    'name' => '活动详情'
                ];
            }
        } else {
            $path = [
                'url' => route('admin.users.index'),
                'name' => '用户列表'
            ];
        }

        return view('admin.user.detail', compact('path', 'user'));
    }
}
