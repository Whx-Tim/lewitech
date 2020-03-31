<?php
namespace App\Repositories;

use App\Models\NoticeHistory;
use App\Models\User;

class NoticeRepository
{
    private $notice;

    public function __construct(NoticeHistory $noticeHistory)
    {
        $this->notice = $noticeHistory;
    }

    public function sign_today(User $user)
    {
        return $this->notice->create([
            'type' => 'sign_today',
            'status' => 0,
            'data' => '每日签到打卡未签用户，9点半进行提醒',
            'user_id' => $user->id
        ]);
    }

    public function had_sign(User $user)
    {
        return $this->notice->create([
            'type' => 'had_sign',
            'status' => 0,
            'data' => '给一段时间没有打卡的用户进行提醒',
            'user_id' => $user->id
        ]);
    }

    public function lost_sign(User $user)
    {
        return $this->notice->create([
            'type' => 'lost_sign',
            'status' => 0,
            'data' => '用户漏签之后给用户进行通知补签提醒',
            'user_id' => $user->id
        ]);
    }

    public function sign_week(User $user)
    {
        return $this->notice->create([
            'type' => 'sign_week',
            'status' => 0,
            'data' => '用户生成周报后给用户进行周报提醒',
            'user_id' => $user->id
        ]);
    }

    public static function signDonateResponse(User $user)
    {
        return NoticeHistory::create([
            'type' => 'signDonateResponse',
            'status' => 0,
            'data' => '捐赠响应提醒',
            'user_id' => $user->id
        ]);
    }
}