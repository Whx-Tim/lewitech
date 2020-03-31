<?php
namespace App\Repositories;

use App\Models\SignTimer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class SignTimerRepository
{
    const CLOSE_STATUS = 0;
    const OPEN_STATUS  = 1;
    const APPLY_STATUS = 2;

    private $sign_timer;

    public function __construct(SignTimer $signTimer)
    {
        $this->sign_timer = $signTimer;
    }

    private function getNewestTimer()
    {
        return $this->sign_timer->orderBy('created_at', 'desc')->first();
    }

    public function create()
    {
        return $this->sign_timer->create([
            'day' => Carbon::now()->addMonth()->daysInMonth,
            'status' => self::APPLY_STATUS,
            'start_at' => Carbon::now()->addMonth()->startOfMonth()->toDateTimeString(),
            'end_at' => Carbon::now()->addMonth()->endOfMonth()->toDateTimeString()
        ]);
    }

    public function openTimer()
    {
        $timer = $this->getApplyingTimer();

        $timer->status = self::OPEN_STATUS;
        $timer->save();

        return $timer;
    }

    public function closeTimer()
    {
        $timer = $this->getOpeningTimer();
        $timer->status = self::CLOSE_STATUS;
        $timer->save();

        return $timer;
    }

    public function getCloseTimer()
    {
        return $this->sign_timer->where('status', self::CLOSE_STATUS)->orderBy('created_at', 'desc')->first();
    }

    public function getOpeningTimer()
    {
//        return $this->sign_timer->where('status', self::OPEN_STATUS)->orderBy('created_at', 'desc')->first();
        return Cache::remember('sign_opening_timer', 5, function () {
            return $this->sign_timer->where('status', self::OPEN_STATUS)->orderBy('created_at', 'desc')->first();
        });
    }

    public function getAllTimers()
    {
        return Cache::remember('sign_all_timers', 30, function () {
            return $this->sign_timer->orderBy('created_at', 'desc')->get();
        });
    }

    public function getApplyingTimer()
    {
//        return $this->getOpeningTimer();
//        return $this->sign_timer->where('status', self::APPLY_STATUS)->orderBy('created_at', 'desc')->first();
        return Cache::remember('sign_applying_timer', 5, function () {
            return $this->sign_timer->where('status', self::APPLY_STATUS)->orderBy('created_at', 'desc')->first();
        });
    }

    public static function __callStatic($name, $arguments)
    {
        $class = new SignTimerRepository(new SignTimer());
        $name = str_replace('static_', '', $name);

        return call_user_func_array([$class, $name], $arguments);
    }

    public static function self()
    {
        return new static(new SignTimer());
    }


}