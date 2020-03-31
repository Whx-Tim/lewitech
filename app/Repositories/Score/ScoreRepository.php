<?php
namespace App\Repositories\Score;

use App\Library\Traits\CacheSwitch;
use App\Models\Score\Score;

class ScoreRepository
{
    use CacheSwitch;

    const TYPE_TITLE = 1;
    const TYPE_SIGN = 2;
    const TYPE_FOOD = 3;
    const TYPE_FRIEND = 4;
    const TYPE_UMBRELLA = 5;

    private $model;

    public function __construct()
    {
        $this->model = new Score();
    }
}