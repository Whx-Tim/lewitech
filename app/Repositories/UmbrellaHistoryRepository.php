<?php
namespace App\Repositories;

use App\Models\UmbrellaHistory;
use App\Models\User;
use App\Traits\Repository\Auth;
use Carbon\Carbon;

class UmbrellaHistoryRepository
{
    use Auth;

    private $umbrella_history;

    public function __construct()
    {
        $this->umbrella_history = new UmbrellaHistory();
    }

    public static function formatHistory($history)
    {
        $history = $history->toArray();
        return array_merge($history, [
            'duration' => self::standing($history),
            'cost' => '免费',
            'still_at' => Carbon::parse($history['still_at'])->toDateString()
        ]);
    }

    public function getHistory($id)
    {
        return self::$user->umbrellaHistories()->where('id', $id)->first();
    }

    public static function standing($history)
    {
        $now = Carbon::now();
        $time = Carbon::parse($history['borrow_at']);
        Carbon::setLocale('zh');
        $diff_time = $time->diffInDays($now);
        if (!$diff_time) {
            $diff_time = $time->diffInHours($now);
            if (!$diff_time) {
                $diff_time = $time->diffInMinutes($now);

                return $diff_time.'分钟';
            }

            return $diff_time.'小时';
        }

        return $diff_time.'天';
    }



    public function latestHistoryDetail()
    {
        $duration = self::$user->umbrellaInfo->standing();
        $cost = '免费';
        $still_at = Carbon::parse(self::$user->umbrellaInfo->still_at)->toDateString();

        return compact('duration', 'cost', 'still_at');
    }
}