<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function ajaxReturn($code,$message,$data=[]){
        return response()->json(compact('code','message','data'));
    }

//    public function login()
//    {
//        $user = session('wechat.oauth_user');
//        $openid = $user->getId();
//        $user = User::firstOrCreate(['openid' => $openid]);
//        Auth::loginUsingId($user->id);
//    }
}
