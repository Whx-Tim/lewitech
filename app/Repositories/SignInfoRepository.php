<?php
namespace App\Repositories;

use App\Events\TriggerWarning;
use App\Models\Sign;
use App\Models\SignDonate;
use App\Models\SignInfo;
use App\Models\SignTimer;
use App\Models\User;
use App\Services\ImageService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SignInfoRepository
{
    const NORMAL_SIGN = 6; //正常用户签到状态
    const FIRST_LOST_SIGN = 1; //漏签一次的用户签到状态
    const SECOND_LOST_SIGN = 2; //漏签第二次的用户签到状态
    const FAIL_SIGN = 3;//失败用户签到状态
    const RECOVER_FIRST_LOST_SIGN = 4; // 漏签一次，补签之后的用户状态
    const RECOVER_SECOND_LOST_SIGN = 5; // 漏签第二次，补签之后的用户状态

    const UMBRELLA_DONATE_MONEY = 20;

    /**
     * @var SignInfo $sign_info
     */
    private $sign_info;

    /**
     * @var User $user
     */
    private $user;

    private $is_free;

    /**
     * @var SignTimer $timer
     */
    private $timer;


    public function __construct(SignInfo $signInfo)
    {
        $this->sign_info = $signInfo;
        $this->timer = SignTimer::where('status', SignTimerRepository::OPEN_STATUS)->first();
    }

    /**
     * 设置当前用户
     *
     * @param User|null $user
     */
    private function setUser(User $user = null)
    {
        if (!is_null($user)) {
            $this->user = $user;
        } else {
            $this->user = Auth::user();
        }
    }

    /**
     * 设置是否免费
     *
     * @param $status
     */
    private function setIsFree($status)
    {
        $this->is_free = $status;
    }

    /**
     * 设置当前用户信息
     *
     * @param SignInfo|null $signInfo
     */
    private function setSignInfo(SignInfo $signInfo = null)
    {
        if (empty($this->sign_info->getAttributes())) {
            if (!is_null($signInfo)) {
                $this->sign_info = $signInfo;
            }
        }
    }

    /**
     * 免费报名
     *
     * @param SignTimer $signTimer
     * @param User|null $user
     * @return User
     */
    public function freeApply(SignTimer $signTimer, User $user = null)
    {
        $this->setUser($user);
        $this->setIsFree(1);

        return $this->newApply($signTimer, $this->user);
    }

    /**
     * 押金报名
     *
     * @param SignTimer $signTimer
     * @param User|null $user
     * @return User
     */
    public function depositApply(SignTimer $signTimer, User $user = null)
    {
        $this->setUser($user);
        $this->setIsFree(0);

        return $this->newApply($signTimer, $this->user);
    }

    /**
     * 报名
     *
     * @param SignTimer $signTimer
     * @param SignInfo|null $sign_info
     * @return SignInfo
     */
    private function apply(SignTimer $signTimer, SignInfo $sign_info = null)
    {
        DB::transaction(function () use (&$sign_info, $signTimer) {
            if (!is_null($sign_info)) {
                $sign_info->is_free = $this->is_free;
                $sign_info->is_apply = 1;
                $sign_info->save();
            } else {
                $sign_info = $this->init([
                    'is_free' => $this->is_free,
                    'is_apply' => 1
                ]);
            }
            $signTimer->increment('apply_count');
        }, 5);

        return $sign_info;
    }

    /**
     * 报名
     *
     * @param SignTimer $signTimer
     * @param User $user
     * @return User
     */
    private function newApply(SignTimer $signTimer, User $user)
    {
        DB::transaction(function () use ($user, $signTimer) {
            if (!$this->is_free) {
                $apply = $user->signApplies()->where('timer_id', $signTimer->id)->first();
                if ($apply) {
                    $apply->is_free = $this->is_free;
                    $apply->save();
                } else {
                    $user->signApplies()->create([
                        'timer_id' => $signTimer->id,
                        'is_free' => $this->is_free
                    ]);
                }
            } else {
                $user->signApplies()->firstOrCreate([
                    'timer_id' => $signTimer->id,
                    'is_free' => $this->is_free
                ]);
            }
            // 超出时间段的报名逻辑
            if (!SignTimerRepository::static_getApplyingTimer()) {
                $user->sign_info()->update(['status' => self::NORMAL_SIGN]);
            }
            $signTimer->increment('apply_count');
        }, 5);

        return $user;
    }

    /**
     * 是否已经报名
     *
     * @param User|null $user
     * @return bool
     */
    public function isApply(User $user = null)
    {
        $this->setUser($user);
        $timer = SignTimerRepository::static_getApplyingTimer();
        if (!$timer) {
            $timer = SignTimerRepository::static_getOpeningTimer();
        }
        if ($apply = $this->user->signApplies()->where('timer_id', $timer->id)->first()) {
            return true;
        }

        return false;
    }

    public function free2depositCanApply(User $user = null)
    {
        $this->setUser($user);
        $users = User::whereHas('signApplies', function ($query) {
            $query->where('is_free', 1)->where('timer_id', (SignTimerRepository::static_getOpeningTimer())->id)->whereDate('created_at', '<=', Carbon::today()->startOfMonth()->toDateString());
        })->whereHas('sign_info', function ($query) {
            $query->where('status', SignInfoRepository::NORMAL_SIGN)->where('month_count', '>=', 8);
        })->select('id')->get()->toArray();

        if (in_array($this->user->id, array_flatten($users))) {
            return true;
        } else {
            return false;
        }
    }

    public function getFree2DepositUsers()
    {
        $users = User::whereHas('signApplies', function ($query) {
            $query->where('is_free', 1)->where('timer_id', (SignTimerRepository::static_getOpeningTimer())->id)->whereDate('created_at', '<=', Carbon::today()->startOfMonth()->toDateString());
        })->whereHas('sign_info', function ($query) {
            $query->where('status', SignInfoRepository::NORMAL_SIGN)->where('month_count', '>=', 8);
        })->get();

        return $users;
    }

    public function isApplyInTimer(SignTimer $timer, User $user = null)
    {
        $this->setUser($user);
        if ($timer) {
            if ($this->user->signApplies()->where('timer_id', $timer->id)->first()) {
                return true;
            }
        }

        return false;
    }

    /**
     * 重新初始化
     *
     * @param SignInfo|null $signInfo
     * @return bool
     */
    public function recoveryInit(SignInfo $signInfo = null)
    {
        $this->setSignInfo($signInfo);

        $this->sign_info->update([
            'is_free' => 0,
            'is_apply' => 0,
            'duration_count' => 0,
            'status' => 0,
            'time_value' => 0
        ]);

        return true;
    }

    /**
     * 初始化用户签到信息
     *
     * @param array $init_data
     * @param User|null $user
     * @return SignInfo|\Illuminate\Database\Eloquent\Model|mixed
     */
    public function init($init_data = [],User $user = null)
    {
        $this->setUser($user);
        $sign_info = $this->user->sign_info;
        if (!$sign_info) {
            $sign_info = $this->user->sign_info()->create(array_filter_empty(collect($init_data)->only($this->sign_info->getFillable())->toArray()));
        }

        return $sign_info;
    }

    /**
     * 漏签用户的状态变更
     *
     * @param User|null $user
     * @return SignInfo|mixed
     */
    public function lostSign(User $user = null)
    {
        $this->setUser($user);
        switch ($this->user->sign_info->status) {
            case self::NORMAL_SIGN:
                return $this->firstLostSign($user);
                break;
            case self::RECOVER_FIRST_LOST_SIGN:
                return $this->secondLostSign($user);
                break;
            case self::FIRST_LOST_SIGN:
            case self::SECOND_LOST_SIGN:
            case self::RECOVER_SECOND_LOST_SIGN:
                return $this->failSign($user);
                break;
            default:
                break;
        }
//        if ($this->isApply($this->user)) {
//            try {
//                $timer = SignTimerRepository::static_getOpeningTimer();
//                $sign_repository = new SignRepository(new Sign());
//                $sign_repository->sign($timer, $this->user);
//            } catch (\Exception $exception) {
//                event(new TriggerWarning('打卡光环异常'));
//                Log::warning($exception);
//            }
//
//        } else {
//
//        }
    }

    /**
     * 第一次漏签
     *
     * @param User|null $user
     * @return SignInfo|mixed
     */
    public function firstLostSign(User $user = null)
    {
        $this->setUser($user);
        $this->user->sign_info->status = self::FIRST_LOST_SIGN;
        $this->user->sign_info->duration_count = 0;
        $this->user->sign_info->save();

        return $this->user->sign_info;
    }

    /**
     * 第二次漏签
     *
     * @param User|null $user
     * @return SignInfo|mixed
     */
    public function secondLostSign(User $user = null)
    {
        $this->setUser($user);
        $this->user->sign_info->status = self::SECOND_LOST_SIGN;
        $this->user->sign_info->duration_count = 0;
        $this->user->sign_info->save();

        return $this->user->sign_info;
    }

    /**
     * 签到失败
     *
     * @param User|null $user
     * @return SignInfo|mixed
     */
    public function failSign(User $user = null)
    {
        $this->setUser($user);
        DB::transaction(function () {
            $this->user->sign_info->status = self::FAIL_SIGN;
            $this->user->sign_info->duration_count = 0;
            $this->user->sign_info->save();
            $this->timer->fail_count += 1;
            $this->timer->save();
        });

        return $this->user->sign_info;
    }

    /**
     * 恢复签到状态
     *
     * @param User|null $user
     * @return SignInfo
     */
    public function recoverSign(User $user = null)
    {
        $this->setUser($user);
        return $this->setSignRecoverStatus($this->user->sign_info);
    }

    /**
     * 设置签到的恢复状态
     *
     * @param SignInfo $sign_info
     * @return SignInfo
     */
    private function setSignRecoverStatus(SignInfo $sign_info)
    {
        if ($sign_info->status == self::FIRST_LOST_SIGN) {
            $sign_info->status = self::RECOVER_FIRST_LOST_SIGN;
            $sign_info->save();
        } else if ($sign_info->status == self::SECOND_LOST_SIGN) {
            $sign_info->status = self::RECOVER_SECOND_LOST_SIGN;
            $sign_info->save();
        }

        return $sign_info;
    }

    public function canSign(User $user = null)
    {
        $this->setUser($user);
        $timer = SignTimerRepository::static_getOpeningTimer();
        if ($timer) {
            if($this->user->signApplies()->where('timer_id', $timer->id)->first()) {
                return true;
            }
        }

        return false;
    }

    public function applySign(User $user = null)
    {
        if (is_null($user)) {
            $user = Auth::user();
        }

        $user->sign_info->is_apply = 1;
        $user->sign_info->save();

        return $user;
    }

    public function getDuration(User $user = null)
    {
        $this->setUser($user);
        return $this->user->sign_info->duration_count;
    }

    public function getUserSign(User $user = null)
    {
        $this->setUser($user);

        return $this->user->sign_info;
    }

    public function getTimeValueRank(User $user = null)
    {
        $this->setUser($user);
        if ($this->user->sign_info->time_value == 0) {
            return 0;
        }
        $count = $this->sign_info->where('time_value', '<', $this->user->sign_info->time_value)->where('time_value', '>', 0)->count();

        return $count+1;
    }

    /**
     * 获取用户签到月排行榜
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getMonthList()
    {
        return $this->sign_info->where('month_count', '>', 0)->orderBy('month_count', 'desc')->orderBy('time_value', 'asc')->take(10)->with('user.detail')->get();
    }

    /**
     * 获取用户总签到排行榜
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getTotalList()
    {
        return $this->sign_info->where('total_count', '>', 0)->orderBy('total_count', 'desc')->orderBy('time_value', 'asc')->take(10)->with('user.detail')->get();
    }

    public function getMonthRank(User $user = null)
    {
        $this->setUser($user);

        if ($this->user->sign_info) {
            return $this->sign_info->where('month_count', '>', $this->user->sign_info->month_count)->count() + 1;
        } else {
            return $this->sign_info->where('month_count', '>', 0)->count() + 1;
        }
    }

    public function getTotalRank(User $user = null)
    {
        $this->setUser($user);

        if ($this->user->sign_info) {
            return $this->sign_info->where('total_count', '>', $this->user->sign_info->total_count)->count() + 1;
        } else {
            return $this->sign_info->where('total_count', '>', 0)->count() + 1;
        }
    }


    /**
     * 获取用户签到信息
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getSignInfos()
    {
        return $this->sign_info->distinct()->get();
    }

    public function getSignWithUsers()
    {
        return $this->getSignInfos()->load('user');
    }

    /**
     * 获取有过签到信息的用户列表
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getSignUsers()
    {
        $users = User::has('sign_info')->get();

        return $users;
    }

    /**
     * TODO 用户奖金池捐款到公益雨伞当中
     */
    public function donateRewardToUmbrella()
    {

    }

    /**
     * TODO 用户在个人奖金池当中获取奖金
     */
    public function attainReward()
    {

    }

    public function canCarveSignInfo()
    {
        $signs = $this->sign_info->whereHas('user', function ($query) {
            $query->whereHas('signApplies', function ($query) {
                $timer = SignTimerRepository::static_getOpeningTimer();
                $query->where('timer_id', $timer->id)->where('is_free', 0);
            });
        })->where('status', self::NORMAL_SIGN)->where('duration_count', '>=', 25)->orderBy('duration_count','desc')->orderBy('time_value', 'asc')->with('user.signDeals')->get();

        return $signs;
    }

    /**
     * 瓜分奖金
     *
     * @param $total_reward 奖金
     * @return bool
     */
    public function carveUpReward($total_reward)
    {
//        $total_reward = 60;
        $sign_infos = $this->canCarveSignInfo();
        $count = (int)count($sign_infos);
        $other_count = 0;
        for ($i = 1; $i <= 10; $i++) {
            $other_count += $i;
        }
        $every_precent = (100/($count+$other_count))/100;
//        dd($every_precent);
//        $first_precent = 5 * $every_precent;
//        $second_precent = 3 * $every_precent;
//        $third_precent = 2 * $every_precent;

        $infoArr = [];
        foreach ($sign_infos as $key => $info) {
//            if ($key < 10) {
////                $reward = ($total_reward * (10-$key) * $every_precent);
//                $reward = (int)($total_reward * (10-$key) * $every_precent);
////                $reward = (int)($total_reward * (5-$key) * $every_precent);
//            } else {
////                $reward = ($total_reward * $every_precent);
//                $reward = (int)($total_reward * $every_precent);
//            }
//            $reward = (int)($total_reward * $every_precent);
//            $reward = $total_reward * $every_precent;
            $reward = (int)($total_reward / $count);
//            $reward = 1;
//            $reward += 15;
            $info->reward += $reward;
            $info->now_reward = $reward;
            $info->total_reward += $reward;

            $infoArr[] = $info;
//            $info->save();
        }
        return $infoArr;

//        return true;
    }

    public function generateWeekPoster(User $user = null, $rank)
    {
        if ($rank == 1) {
            $dst_path = public_path('images/sign/week_gold.png');
        } else if ($rank ==2) {
            $dst_path = public_path('images/sign/week_silver.png');
        } else {
            $dst_path = public_path('images/sign/week_bronze.png');
        }
        $this->setUser($user);
        $user = $this->user;
        $head_img = $user->detail->head_img;
        $head_img = substr($head_img, 0, strlen($head_img)-1). '132';
        $head_img = file_get_contents($head_img);
        $path = 'images/sign/week/'.$user->id.'.png';
        Log::warning($user->detail->nickname);
        Log::warning($rank);
        Log::warning($user->sign_info->time_value);

        if (!file_exists(public_path($path))) {
            Log::warning('successful');
            $image_service = new ImageService($dst_path);
            $image = $image_service->initSrcImageFormResource($head_img)->resizeSrc(150)->addSrcImage(0.24, 0.595);
            $image = $image->text('昵称:'.$user->detail->nickname, [255,255,255], 0.465, 0.625,30);
            $image = $image->text('总排名:'.$rank, [255,255,255], 0.465, 0.660,30);
            $image = $image->text('早起值:'.$user->sign_info->time_value, [255,255,255], 0.465, 0.695,30);
            $image = $image->save(public_path($path));

//            unset($image);
            unset($image_service);
        }
        $path = asset($path);

        return $path;
    }

    public function generateWeekPosterBySignInfo(SignInfo $signInfo, $rank)
    {
        if ($rank == 1) {
            $dst_path = public_path('images/sign/week_gold.png');
        } else if ($rank ==2) {
            $dst_path = public_path('images/sign/week_silver.png');
        } else {
            $dst_path = public_path('images/sign/week_bronze.png');
        }
        $head_img = $signInfo->user->detail->head_img;
        $head_img = substr($head_img, 0, strlen($head_img)-1). '132';
        $head_img = file_get_contents($head_img);
        $path = 'images/sign/week/'.$signInfo->user->id.'.png';
        if (!file_exists($path)) {
            $image_service = new ImageService($dst_path);
            $image = $image_service->initSrcImageFormResource($head_img)->resizeSrc(150)->addSrcImage(0.24, 0.595);
            $image = $image->text('昵称:'.$signInfo->user->detail->nickname, [255,255,255], 0.465, 0.625,30);
            $image = $image->text('总排名:'.$rank, [255,255,255], 0.465, 0.660,30);
            $image = $image->text('早起值:'.$signInfo->time_value, [255,255,255], 0.465, 0.695,30);
            $image = $image->save(public_path($path));
            unset($image_service);
        }
        $path = asset($path);

        return $path;
    }

    public function getUmbrellaDonateMoney(User $user = null)
    {
        $this->setUser($user);
        $reward = $this->user->sign_info->reward;
        $money = self::UMBRELLA_DONATE_MONEY - $reward;
        if ($money <= 0) {
            $this->user->signDonates()->create(['type' => 'reward']);
            $this->user->sign_info->reward = abs($money);
            $money = 0;
            $this->user->sign_info->save();
        } else {
            $this->user->signDonates()->create(['type' => 'cash']);
        }

        return $money;
    }

    public function donateUmbrella(User $user = null)
    {
        $this->setUser($user);
        $this->user->signDonates()->create([]);
    }

    public function getRewardRank()
    {
        return $this->sign_info->orderBy('total_reward', 'desc')->orderBy('time_value', 'asc')->take(10)->get();
    }

    public function getUserRewardRank(User $user = null)
    {
        $this->setUser($user);

        return $this->sign_info->where('total_reward', '>=', $this->user->sign_info->total_reward)->count() + 1;
    }


}