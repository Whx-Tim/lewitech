<?php
namespace App\Services;

use App\Events\TriggerUmbrellaNotice;
use App\Models\SignDonate;
use App\Models\UmbrellaHistory;

class UmbrellaService
{
    public static function donateResponse()
    {
        $donates = SignDonate::where('remind', '>', 0)->where('wechat_deal_id', '<>', NULL)->where('name', '<>', NULL)->with('user.detail')->get();
        if ($donates) {
            $umbrella = UmbrellaHistory::count();
            foreach ($donates as $donate) {
                event(new TriggerUmbrellaNotice($donate->user->openid, compact('umbrella', 'donate'), 'donateResponse'));
                $donate->decrement('remind');
            }
        }
    }

}