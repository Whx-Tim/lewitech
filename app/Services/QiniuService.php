<?php
namespace App\Services;

use EasyWeChat\Foundation\Application;

class QiniuService
{
    private $wechat;

    public function __construct(Application $wechat)
    {
        $this->wechat = $wechat;
    }

}