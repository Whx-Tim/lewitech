<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use QRcode;

include app_path('vendor/phpqrcode.php');

class TempController extends Controller
{
    public function showWeijuanActRedirect()
    {
        return view('temp.redirect', ['redirect' => 'http://weijuan.szu.edu.cn/n/act']);
    }

    public function localQRcode()
    {
        $value = 'http://wx.lewitech.cn/wechat/umbrella/bind/1';
        $errorCorrectionLevel = 'L';//容错级别
        $matrixPointSize = 16;//生成图片大小

        //生成二维码图片
        $tmp = QRcode::png($value, public_path('images/temp/umbrella_1.png'), $errorCorrectionLevel, $matrixPointSize);

//        file_put_contents(public_path('images/temp/umbrella_1.png'), $tmp);
        exit;
    }
}
