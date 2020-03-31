<?php
namespace App\Repositories\DaySign;

use App\Library\Traits\SelfClass;
use App\Models\DaySign\DaySign;
use App\Models\DaySign\DaySignReward;
use Carbon\Carbon;

class DaySignRewardRepository
{
    use SelfClass;

    const IS_SHARE = 1;
    const NOT_SHARE = 0;

    private $model;

    public function __construct()
    {
        $this->model = new DaySignReward();
    }

    public function getCanShare()
    {
        return $this->model->where('status', self::NOT_SHARE)->get();
    }
}