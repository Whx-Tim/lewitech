<?php

namespace App\Listeners;

use App\Events\SendWeatherReportToUser;
use App\Models\Config;
use App\Services\SignService;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class GenerateAndSendImage implements ShouldQueue
{
    private $text1 = '取本份之财，戒无名之酒;';
    private $text2 = '怀克己之心，闭是非之口。';

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        $textArr = Cache::remember('sign_everyday_text', 300, function () {
            $text = (Config::where('type', 'signText')->first())->content;
            $text = json_decode($text);
            $text = $text[0];
            $textArr = explode('，',$text);
            return $textArr;
        });

        $this->text1 = $textArr[0].'，';
        $this->text2 = $textArr[1];
    }

    /**
     * Handle the event.
     *
     * @param  SendWeatherReportToUser  $event
     * @return void
     */
    public function handle(SendWeatherReportToUser $event)
    {
        $sign_service = new SignService();
//        $image_path = $sign_service->generateWeatherReportNewYear($event->sign, $this->text1, $this->text2, $event->user);
        $image_path = $sign_service->generateWeatherReport($event->sign, $this->text1, $this->text2, $event->user);
//        $image_path = $sign_service->specialReport($event->sign, $this->text1, $this->text2, $event->user);
        $sign_service->sendImageWithDelete($image_path, $event->user);
    }
}
