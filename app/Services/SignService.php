<?php
namespace App\Services;

use App\Models\Sign;
use App\Models\SignInfo;
use App\Models\SignMonth;
use App\Models\SignTimer;
use App\Models\User;
use App\Repositories\SignDealRepository;
use App\Repositories\SignInfoRepository;
use App\Repositories\SignMedalRepository;
use App\Repositories\SignRepository;
use App\Repositories\SignShareRepository;
use App\Repositories\SignTimerRepository;
use Carbon\Carbon;
use EasyWeChat\Message\Image as WechatImage;
use EasyWeChat\Message\Text;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;

class SignService extends Service
{
    /**
     * @var User
     */
    private $user;

    public function __construct()
    {

    }

    private function setUser(User $user = null)
    {
        if (is_null($user)) {
            $this->user = Auth::user();
        } else {
            $this->user = $user;
        }
    }


    public function saveSharePoster(User $user = null)
    {
        $this->setUser($user);

        $user = $this->user;
        $path = 'images/sign/share/'.$user->id.'.png';
        if (!file_exists($path)) {
            $qrcode_service = new QrcodeService(public_path('images/sign/share_qrcode/sign_share_'.$user->id.'.png'));
            $qrcode = $qrcode_service->generateWechat()->addLogo(public_path('images/logo_white.png'))->getPath();
            $image_service = new ImageService(public_path('images/sign/share.png'));
            $image_service->initSrcImage($qrcode)->resizeSrc(112)->addSrcImage(0.18, 0.60)->save(public_path('images/sign/share/'.$user->id.'.png'));
            unset($image_service);
        }

        return asset($path);
    }

    private function resizeHeadImg($head_img, $size = 132)
    {
        $headimgArr = explode('/', $head_img);
        $headimgArr[count($headimgArr)-1] = $size;

        return implode('/', $headimgArr);
    }

    public function generateWeatherReportNewYear($sign, $text1, $text2, User $user = null)
    {
        $this->setUser($user);
        $sign_repository = new SignRepository(new Sign());
        $today_time = Carbon::parse($sign->today_time)->format('H:i');
        $rank = $sign_repository->getTodaySignRank($sign);
        $weather_service = new WeatherService();
        $type = $weather_service->getType();
        $today_temperature = $weather_service->getWendu();
        $head_img = $this->user->detail->head_img;
        $head_img = $this->resizeHeadImg($head_img, 132);
//        $head_img = substr($head_img, 0, strlen($head_img)-1).'132';
        $client = new Client();
        $avatar = $client->get($head_img);
        $image_path = public_path('images/sign/weather/'.$this->user->id.'.jpeg');
        $image = Image::make($avatar->getBody()->getContents())->resize(110,110);
        Image::make(public_path('images/sign/new_year/7.png'))->insert($image, null, 290, 688)->text('早起打卡时间', 256, 870, function ($font) {
            $font->file(public_path('fonts/msyh.ttf'));
            $font->size(40);
            $font->color('#ef0f45');
        })->text($today_time, 260, 960, function ($font) {
            $font->file(public_path('fonts/msyh.ttf'));
            $font->size(90);
            $font->color('#ef0f45');
        })->text(Carbon::today()->format('Y年m月d日'), 260, 1000, function ($font) {
            $font->file(public_path('fonts/msyh.ttf'));
            $font->size(30);
            $font->color('#ef0f45');
        })->save($image_path);

        return $image_path;
    }

    public function specialReport($sign, $text1, $text2, User $user = null)
    {
        $this->setUser($user);

        $image_path = public_path('images/sign/weather/'.$this->user->id.'.jpeg');
        Image::make(public_path('images/sign/day-0.jpeg'))->save($image_path);

        return $image_path;
    }

    public function generateWeatherReport($sign, $text1, $text2, User $user = null)
    {
        $this->setUser($user);
        $sign_repository = new SignRepository(new Sign());
        $today_time = Carbon::parse($sign->today_time)->format('H:i');
        $rank = $sign_repository->getTodaySignRank($sign);
        $weather_service = new WeatherService();
        $type = $weather_service->getType();
        $today_temperature = $weather_service->getWendu();
        $head_img = $this->user->detail->head_img;
        $head_img = $this->resizeHeadImg($head_img, 132);
//        $head_img = substr($head_img, 0, strlen($head_img)-1).'132';
        $client = new Client();
        $avatar = $client->get($head_img);
        $image_path = public_path('images/sign/weather/'.$this->user->id.'.jpeg');
        $image = Image::make($avatar->getBody()->getContents())->resize(100,100);
        Image::make(public_path('images/sign/day-7.jpg'))->insert($image, null, 330, 360)->text($this->user->detail->nickname.'起床啦！', $this->text_center($this->user->detail->nickname.'起床啦'), 290, function ($font) {
            $font->file(public_path('fonts/msyh.ttf'));
            $font->size(30);
            $font->color('#4e4e4e');
        })->text('打卡时间', 315, 330, function ($font) {
            $font->file(public_path('fonts/msyh.ttf'));
            $font->size(30);
            $font->color('#4e4e4e');
        })->text($today_time, 270, 540, function ($font) {
            $font->file(public_path('fonts/msyh.ttf'));
            $font->size(80);
            $font->color('#4e4e4e');
        })->text('今日排名:'.$rank, 255 , 595, function ($font) {
            $font->file(public_path('fonts/msyh.ttf'));
            $font->size(45);
            $font->color('#4e4e4e');
        })->text(Carbon::today()->format('Y年m月d日'), 260, 635, function ($font) {
            $font->file(public_path('fonts/msyh.ttf'));
            $font->size(30);
            $font->color('#4e4e4e');
        })->text('深圳  '.$today_temperature.'℃', 315, 670, function ($font) {
            $font->file(public_path('fonts/msyh.ttf'));
            $font->size(30);
            $font->color('#4e4e4e');
        })->text($type, $this->text_center($type), 710, function ($font) {
            $font->file(public_path('fonts/msyh.ttf'));
            $font->size(30);
            $font->color('#4e4e4e');
        })->text($text1, $this->text_center($text1), 845, function ($font) {
            $font->file(public_path('fonts/msyh.ttf'));
            $font->size(30);
            $font->color('#ffffff');
        })->text($text2, $this->text_center($text2), 885, function ($font) {
            $font->file(public_path('fonts/msyh.ttf'));
            $font->size(30);
            $font->color('#ffffff');
        })->save($image_path);

        return $image_path;
    }

    public function text_center($text)
    {
        $text_numer = 315;
        $every_font = 15;
        $length = mb_strlen($text);
        $diff = $length - 4;
        $result = $text_numer - ($every_font*$diff);

        return $result;
    }

    public function sendImageWithDelete($image_path, User $user = null)
    {
        $this->setUser($user);
        $wechat = app('wechat');
        $staff  = $wechat->staff;
        $temporary = $wechat->material_temporary;
        $result = $temporary->uploadImage($image_path);
        $image = new WechatImage();
        $image->media_id = $result->media_id;
        try {
            $staff->message($image)->to($user->openid)->send();
            unlink($image_path);
        } catch (\Exception $exception) {
            Log::warning($exception);
//            unlink($image_path);

            return false;
        }

        return true;
    }

    public function medalEveryWeek()
    {
        $signs = SignRepository::static_getWeekRank();
        $sign_medal_repository = new SignMedalRepository();
        foreach ($signs as $key => $sign) {
            if ($key < 3) {
                $sign_medal_repository->attainMedal('gold', $key+1, $sign->user);
            } else if ($key < 8) {
                $sign_medal_repository->attainMedal('silver', $key+1, $sign->user);
            } else {
                $sign_medal_repository->attainMedal('bronze', $key+1, $sign->user);
            }
        }

        return 'successful';
    }

    public function carveUpReward(SignDealRepository $signDealRepository)
    {
        return $signDealRepository->getOpeningCanCarveReward();
    }

    public function generateApplySharePoster(User $user = null)
    {
        $this->setUser($user);
        $filename = 'images/sign/share_apply/'. $this->user->id . '.jpg';
        $image_path = public_path($filename);
//        $image_path = public_path('images/sign/share_apply/154.jpg');
        if (!file_exists($image_path)) {
            $image = Image::make(public_path('images/sign/apply-share.jpg'))
                ->text(str_limit($this->user->detail->nickname, 8), 340, 250, function ($font) {
                    $font->file(public_path('fonts/msyh.ttf'));
                    $font->size(50);
                    $font->color('#ffffff');
                })
                ->save($image_path);
        }

        return $filename;
    }

    public function getFailUser()
    {
        return User::whereHas('signDeals', function ($query) {
            $query->where('result_code', SignDealRepository::CLOSE_STATUS)->where('sign_timer_id', (SignTimerRepository::static_getOpeningTimer())->id);
        })->with('sign_info')->with(['signDeals' => function ($query) {
            $query->where('sign_timer_id', (SignTimerRepository::self()->getOpeningTimer())->id);
        }])->with('detail')->get();
    }

    public function getSuccessUser()
    {
        return User::whereHas('sign_info', function ($query) {
            $query->where('status', SignInfoRepository::NORMAL_SIGN);
        })->whereHas('signApplies', function ($query) {
            $query->where('timer_id', (SignTimerRepository::static_getOpeningTimer())->id)->where('is_free', 0);
        })->with(['sign_info', 'signDeals', 'detail'])->get();
    }

    public function getNoFailUser()
    {
        return User::whereHas('sign_info', function ($query) {
            $query->whereNotIn('status', [SignInfoRepository::NORMAL_SIGN, SignInfoRepository::FAIL_SIGN]);
        })->whereHas('signDeals', function ($query) {
            $query->where('sign_timer_id', (SignTimerRepository::self()->getOpeningTimer())->id)->where(function ($q) {
                $q->whereIn('result_code', [SignDealRepository::SUCCESS_STATUS, SignDealRepository::OVER_STATUS, SignDealRepository::CONTINUE_STATUS, SignDealRepository::CONTINUE_SUCCESS_STATUS]);
            });
        })->with(['signDeals' => function ($query) {
            $query->where('sign_timer_id', (SignTimerRepository::self()->getOpeningTimer())->id)->where(function ($q) {
                $q->whereIn('result_code', [SignDealRepository::SUCCESS_STATUS, SignDealRepository::OVER_STATUS, SignDealRepository::CONTINUE_STATUS, SignDealRepository::CONTINUE_SUCCESS_STATUS]);
            });
        }])->with(['sign_info', 'detail'])->get();
    }

    public function getSuccessButNotApplyUser()
    {
        $openTimer = SignTimerRepository::static_getOpeningTimer();
        $applyTimer = SignTimerRepository::static_getApplyingTimer();
        $closeTimer = SignTimerRepository::static_getCloseTimer();
//        $next_users = User::whereHas('signApplies', function ($query) use ($applyTimer) {
//            $query->where('timer_id', $applyTimer->id)->where('is_free', 0);
//        })->get();
        $next_users = [];
        $prev_users = User::whereHas('signApplies', function ($query) use ($openTimer) {
            $query->where('timer_id', $openTimer->id)->where('is_free', 0);
        })->whereHas('sign_info', function ($query) {
            $query->where('status', SignInfoRepository::NORMAL_SIGN);
        })->whereHas('signDeals', function ($query) use ($openTimer) {
            $query->where('sign_timer_id', $openTimer->id)->where(function ($q) {
                $q->whereIn('result_code', [SignDealRepository::SUCCESS_STATUS, SignDealRepository::CONTINUE_STATUS, SignDealRepository::CONTINUE_SUCCESS_STATUS, SignDealRepository::OVER_STATUS]);
//                    ->orWhere('result_code', SignDealRepository::CONTINUE_STATUS);
            });
        })->with(['signDeals' => function ($query) use ($openTimer) {
            $query->where('sign_timer_id', $openTimer->id)->where(function ($q) {
                $q->whereIn('result_code', [SignDealRepository::SUCCESS_STATUS, SignDealRepository::CONTINUE_STATUS, SignDealRepository::CONTINUE_SUCCESS_STATUS, SignDealRepository::OVER_STATUS]);
//                    ->orWhere('result_code', SignDealRepository::CONTINUE_STATUS);
            });
        }])->get();
//        $prev_users = User::whereHas('signApplies', function ($query) {
//            $query->where('timer_id', (SignTimerRepository::static_getCloseTimer())->id);
//        })->whereHas('sign_info', function ($query) {
//            $query->where('status', SignInfoRepository::NORMAL_SIGN)
//                ->where('is_free', 0);
//        })->with('signDeals')->get();
        $users = [];
        foreach ($prev_users as $prev_user) {
            $status = 0;
            foreach ($next_users as $key => $next_user) {
                if ($prev_user->id == $next_user->id) {
                    $status = 1;
                    unset($next_users[$key]);
                    break;
                }
            }
            if (!$status) {
                $users []= $prev_user;
            }
        }

        return $users;
    }

    public function summaryMonth()
    {
        $sign_infos = SignInfo::whereHas('user', function ($query) {
            $query->whereHas('signApplies', function ($q) {
                $q->where('timer_id', (SignTimerRepository::self()->getOpeningTimer())->id);
            });
        })->get();
        $insert_arr = [];
        foreach ($sign_infos as $info) {
            $item_arr = [
                'status' => $info->status,
                'total_day' => $info->month_count,
                'duration_day' => $info->duration_count,
                'time_value' => $info->time_value,
                'reward' => $info->total_reward,
                'timer_id' => (SignTimerRepository::self()->getOpeningTimer())->id,
                'user_id' => $info->user->id,
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString()
            ];
            $insert_arr [] = $item_arr;
        }
        SignMonth::insert($insert_arr);

        return true;
    }



}