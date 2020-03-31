<?php
namespace App\Repositories;

use App\Models\Sign;
use App\Models\SignMedal;
use App\Models\SignMedalWeek;
use App\Models\User;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;

class SignMedalRepository
{
    private $sign_medal;
    private $sign_medal_week;

    /**
     * @var User
     */
    private $user;

    public function __construct()
    {
//        $this->sign_medal = new SignMedal();
        $this->sign_medal_week = new SignMedalWeek();
    }

    public function __call($name, $arguments)
    {
        if (count($arguments)) {
            $argument = array_last($arguments);
            if ($argument instanceof User) {
                $this->setUser($argument);
            } else {
                $this->setUser();
            }
        } else {
            $this->setUser();

            return call_user_func([$this, $name]);
        }


        return call_user_func_array([$this,$name], $arguments);
    }

    private function setUser(User $user = null)
    {
        if (is_null($user)) {
            $this->user = Auth::user();
        } else {
            $this->user = $user;
        }
    }

    protected function setMedal()
    {
        return $this->user->signMedal ? $this->user->signMedal : $this->user->signMedal()->create([]);
    }

    protected function attainMedal($medal_name, $rank)
    {
        $medal = $this->setMedal();
        if (in_array($medal_name, ['gold', 'silver', 'bronze'])) {
            $medal->increment($medal_name);
        } else {
            return null;
        }
        $signMedalWeek = $this->user->signMedalWeeks()->orderBy('created_at', 'desc')->first();
        $medal = $this->user->signMedalWeeks()->create([
            'rank' => $rank,
            'medal' => $medal_name
        ]);
        $medal = $this->setUserMedalWeekTimeValue($medal);
        $delay = 0;
        if (!empty($signMedalWeek->time_value)) {
            $delay = (int)(($medal->time_value - $signMedalWeek->time_value)/60);
        }

        $this->generateWeekPoster($rank, $medal_name, $medal->time_value, $delay);

        return $medal;
    }

    private function timeValueToTime($time_value)
    {
        return Carbon::today()->addHours(SignRepository::TODAY_START_TIME)->addSeconds($time_value)->format('H:i');
    }

    private function delayToText($delay)
    {
        if ($delay > 0) {
            return [
                'text1' => '本周比上周晚起了'.$delay.'分钟哦！',
                'text2' => '下周继续加油呀！'
            ];
        }
        if ($delay == 0) {
            return ['text1' => '', 'text2' => ''];
        }

        return [
            'text1' => '本周比上周早起了'.abs($delay).'分钟哦！',
            'text2' => '下周再接再厉呀！'
        ];
    }

    private function generateWeekPoster($key, $type, $time, $delay)
    {
        $client = new Client();
        $time = $this->timeValueToTime($time);
        $delay_text = $this->delayToText($delay);
        $head_img = $this->resizeHeadImg($this->user->detail->head_img);
        $head_img = $client->get($head_img);
        $head_img = $head_img->getBody()->getContents();
        $image = Image::make(public_path('images/sign/week-'. $type .'.png'))->insert($head_img, null, 35, 120)
            ->text($this->user->detail->nickname, 220, 200,  function ($font) {
                $font->file(public_path('fonts/msyh.ttf'));
                $font->size(35);
                $font->color('#4d8583');
            })->text($this->rank2star($key), 140, 346, function ($font) {
                $font->file(public_path('fonts/msyh.ttf'));
                $font->size(25);
                $font->color('#4d8583');
            })->text($key, 430, 349, function ($font) {
                $font->file(public_path('fonts/msyh.ttf'));
                $font->size(25);
                $font->color('#4d8583');
            })->text($time, 640, 349, function ($font) {
                $font->file(public_path('fonts/msyh.ttf'));
                $font->size(25);
                $font->color('#4d8583');
            })->text($delay_text['text1'], 720, 480, function ($font) {
                $font->file(public_path('fonts/msyh.ttf'));
                $font->size(20);
                $font->color('#4d8583');
                $font->align('right');
            })->text($delay_text['text2'], 720, 510, function ($font) {
                $font->file(public_path('fonts/msyh.ttf'));
                $font->size(20);
                $font->color('#4d8583');
                $font->align('right');
            })->save(public_path('images/sign/week/'.$this->user->id.'.jpeg'));

        return $image;
    }

    private function resizeHeadImg($head_img, $size = 132)
    {
        $headimgArr = explode('/', $head_img);
        $headimgArr[count($headimgArr)-1] = $size;

        return implode('/', $headimgArr);
    }

    private function rank2star($rank)
    {
        if ($rank <= 1) {
            return '★★★★★';
        } else if ($rank <= 3) {
            return '★★★★☆';
        } else if ($rank <= 8) {
            return '★★★☆☆';
        } else {
            return '★★☆☆☆';
        }
    }

    protected function testWeekPoster()
    {
        $this->generateWeekPoster(1, 'gold', '300', -10);
    }

    private function setUserMedalWeekTimeValue(SignMedalWeek $medal)
    {
        $week_start = Carbon::today()->startOfWeek()->toDateTimeString();
        $week_end = Carbon::today()->endOfWeek()->toDateTimeString();
        $avg_time_value = Sign::where('user_id', $medal->user_id)->whereBetween('today_time', [$week_start, $week_end])->avg('time_value');
        $medal->time_value = $avg_time_value;
        $medal->save();

        return $medal;
    }

    public function userWeekTimeValueAvg()
    {
        $medals = SignMedalWeek::whereDate('created_at', '2018-01-07')->get();
        $week_start = Carbon::create(2018, 1, 7)->startOfWeek()->toDateTimeString();
        $week_end = Carbon::create(2018, 1, 7)->endOfWeek()->toDateTimeString();
        foreach ($medals as $medal) {
            $avg_time_value = Sign::where('user_id', $medal->user_id)->whereBetween('today_time', [$week_start, $week_end])->avg('time_value');
            $medal->time_value = $avg_time_value;
            $medal->save();
        }

    }

}