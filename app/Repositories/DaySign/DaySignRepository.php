<?php
namespace App\Repositories\DaySign;

use App\Library\Traits\SelfClass;
use App\Models\DaySign\DaySign;
use App\Models\DaySign\DaySignReward;
use EasyWeChat\Foundation\Application;

class DaySignRepository
{
    use SelfClass;

    const STATUS_ENABLE = 1;
    const STATUS_UNABLE = 0;

    private $model;

    /**
     * @var Application
     */
    private $wechat;

    public function __construct()
    {
        $this->model = new DaySign();
        $this->wechat = app('wechat');
    }

    public function createTimer()
    {
        $this->model->create([
            'status' => self::STATUS_ENABLE
        ]);
    }

    public function downTimer(DaySign $daySign)
    {
        $daySign->status = self::STATUS_UNABLE;
        $daySign->save();

        return $daySign;
    }

    public function getTimer()
    {
        return $this->model->where('status', self::STATUS_ENABLE)->first();
    }

    public function failNotice($openid)
    {
        $notice = $this->wechat->notice;
    }
}