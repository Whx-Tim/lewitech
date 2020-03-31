<?php

namespace App\Http\Controllers\Wechat;

use App\Models\Palette;
use Carbon\Carbon;
use EasyWeChat\Foundation\Application;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Qiniu\Auth as QiniuAuth;

class PaletteController extends Controller
{
    private $wechat;

    public function __construct(Application $wechat)
    {
        $this->wechat = $wechat;
    }

    public function index()
    {
        $js = $this->wechat->js;

        return view('wechat.palette.index', compact('js'));
    }

    public function save(Request $request)
    {
        $this->validate($request, [
            'source' => 'required',
            'sky_index' => 'required',
        ], [
            'source.required' => '网络不稳定，图片上传失败',
            'sky_index.required' => '请选择星空背景',
        ]);
        $user = Auth::user();
        if (is_null($user->palette)) {
            $palette = $user->palette()->create($request->only(['source', 'sky_index', 'description', 'month']));
            $id = $palette->id;
        } else {
            $user->palette()->update($request->only(['source', 'sky_index', 'description', 'month']));
            $id = $user->palette->id;
        }

        return $this->ajaxReturn(0, '保存成功', ['id' => $id]);
    }

    public function getToken($type)
    {
        $auth = new QiniuAuth('d2imjQtPHDgS_pA6ILYLHfZaXrpUz-pj6kCIV49Y', 'nphKPWgKwgcejmkgNqYcui1uIGZwkfb7ZYIl667S');
        if ($type == 'month') {
            $policy = array(
                'saveKey'=>'image/palette/month/'. Carbon::now()->format('Y-m-d_H:i:s').'_'. Auth::user()->openid,
            );
        } else {
            $policy = array(
                'saveKey'=>'image/palette/'. Carbon::now()->format('Y-m-d_H:i:s').'_'. Auth::user()->openid,
            );
        }
        $bucket='weijuan';
        $token = $auth->uploadToken($bucket, null, 3600, $policy);

        return $this->ajaxReturn(0, '获取成功', ['token' => $token]);
    }

    public function result(Palette $palette)
    {
        $js = $this->wechat->js;

        return view('wechat.palette.result', compact('palette', 'js'));
    }


}
