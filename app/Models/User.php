<?php

namespace App\Models;

use App\Events\TriggerWarning;
use App\Models\Badge\Badge;
use App\Models\Badge\BadgeUser;
use App\Models\DaySign\DaySignDeal;
use App\Models\DaySign\DaySignHistory;
use App\Models\DaySign\DaySignReward;
use App\Models\Score\Score;
use App\Models\Score\ScoreHistory;
use Carbon\Carbon;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Log;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','openid','ID_number','is_schoolmate','phone','is_real', 'real_name', 'birthday', 'adminset'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * 获取用户发布过的所有需求信息
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function demands()
    {
        return $this->hasMany(ShareholderDemand::class);
    }

    /**
     * 获取用户的详情信息
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function detail()
    {
        return $this->hasOne(WechatUserDetail::class);
    }

    /**
     * 获取用户的报名信息
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function enrolls()
    {
        return $this->hasMany(Enroll::class);
    }

    /**
     * 获取浏览过的信息
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function views()
    {
        return $this->hasMany(ViewUser::class);
    }

    /**
     * 获取用户发布的活动信息
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function actives()
    {
        return $this->hasMany(Active::class);
    }

    /**
     * 获取签到打卡信息
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function getUpInfo()
    {
        return $this->hasOne(GetUp::class,'open_id', 'openid');
    }

    /**
     * 获取用户参与共享雨伞的信息
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function umbrellaInfo()
    {
        return $this->hasOne(UserUmbrella::class);
    }

    /**
     * 获取用户的校友信息
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function schoolmateInfo()
    {
        return $this->hasOne(UserSchoolmate::class);
    }

    /**
     * 获取当前绑定的雨伞信息
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function umbrella()
    {
        return $this->hasOne(Umbrella::class);
    }

    /**
     * 获取捐赠的雨伞记录
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function myUmbrellas()
    {
        return $this->hasMany(Umbrella::class, 'owner_id');
    }

    /**
     * 获取借伞历史记录
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function umbrellaHistories()
    {
        return $this->hasMany(UmbrellaHistory::class);
    }

    /**
     * 获取用户申诉、举报、反馈等信息
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    /**
     * 获取用户拉人的用户信息
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function UmbrellaFriends()
    {
        return $this->hasMany(UmbrellaShare::class, 'friend_id');
    }

    /**
     * 获取用户缴纳押金信息
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function deposits()
    {
        return $this->hasMany(UmbrellaDeposit::class);
    }

    public function businesses()
    {
        return $this->hasMany(Business::class, 'user_id', 'id');
    }

    public function comment()
    {
        return $this->hasMany(Comment::class, 'user_id', 'id');
    }

    public function noticeHistories()
    {
        return $this->hasMany(NoticeHistory::class, 'user_id', 'id');
    }

    public function blacklists()
    {
        return $this->hasMany(Blacklist::class, 'user_id', 'id');
    }

    public function palette()
    {
        return $this->hasOne(Palette::class, 'user_id', 'id');
    }

    //签到打卡关系模型绑定---start

    public function signs()
    {
        return $this->hasMany(Sign::class, 'user_id', 'id');
    }

    public function cards()
    {
        return $this->belongsTo(Card::class, 'sign_cards', 'user_id', 'card_id');
    }

    public function sign_info()
    {
        return $this->hasOne(SignInfo::class, 'user_id', 'id');
    }

    public function sign_cards()
    {
        return $this->hasMany(SignCard::class, 'user_id', 'id');
    }

    public function sign_help_tos()
    {
        return $this->hasMany(SignShare::class, 'help_id', 'id');
    }

    public function sign_help_froms()
    {
        return $this->hasMany(SignShare::class, 'user_id', 'id');
    }

    public function signDeals()
    {
        return $this->hasMany(SignDeal::class, 'user_id', 'id');
    }

    public function signMedal()
    {
        return $this->hasOne(SignMedal::class, 'user_id', 'id');
    }

    public function signMedalWeeks()
    {
        return $this->hasMany(SignMedalWeek::class, 'user_id', 'id');
    }

    public function signApplies()
    {
        return $this->hasMany(SignTimerApply::class, 'user_id', 'id');
    }

    public function signDonates()
    {
        return $this->hasMany(SignDonate::class, 'user_id', 'id');
    }

    public function signMonths()
    {
        return $this->hasMany(SignMonth::class, 'user_id', 'id');
    }

    public function daySignDeals()
    {
        return $this->hasMany(DaySignDeal::class, 'user_id', 'id');
    }

    public function daySigns()
    {
        return $this->hasMany(DaySignHistory::class, 'user_id', 'id');
    }

    public function daySignRewards()
    {
        return $this->hasMany(DaySignReward::class, 'user_id', 'id');
    }

    //签到打卡关系模型绑定---end

    //乐微互助关系模型绑定---start
    public function help()
    {
        return $this->hasOne(HelpUser::class, 'user_id', 'id');
    }
    //乐微互助关系模型绑定---end

    //微信支付交易记录
    public function wechatDeals()
    {
        return $this->hasMany(WechatDeal::class, 'user_id', 'id');
    }

    //乐微保险
    public function insurances()
    {
        return $this->hasMany(Insurance::class, 'user_id', 'id');
    }

    //微信红包
    public function red_packs()
    {
        return $this->hasMany(RedPack::class, 'user_id', 'id');
    }

    /**
     * 获取该用户所属的学校信息
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function schools()
    {
        return $this->belongsToMany(School::class, 'user_school', 'user_id', 'school_id');
    }

    /**
     * 用户所属徽章
     */
    public function badges()
    {
        return $this->hasMany(BadgeUser::class, 'user_id', 'id');
    }

    //积分体系绑定------start
    public function scores()
    {
        return $this->hasMany(Score::class, 'user_id', 'id');
    }

    public function score_histories()
    {
        return $this->hasMany(ScoreHistory::class, 'user_id', 'id');
    }
    //积分体系绑定------end

    public static function firstCreateOrUpdate($openid)
    {
        try {
            $app = app('wechat');
            $userService = $app->user;
            $midUser = $userService->get($openid);
            if (is_null($midUser)) {
                event(new TriggerWarning('获取不到用户中信息，请查看系统日志查看bug'));
                return false;
            }

            $user = User::firstOrCreate(compact('openid'));
            $data = [
                'head_img' => $midUser->headimgurl,
                'nickname' => $midUser->nickname,
                'sex'      => $midUser->sex,
                'city'     => $midUser->city,
                'country'  => $midUser->country,
                'language' => $midUser->language,
                'subscribe'=> $midUser->subscribe,
                'subscribe_time' => $midUser->subscribe_time,
            ];
            if (is_null($user->detail)) {
                $user->detail()->create($data);
            } else {
                $user->detail()->update($data);
            }

            return true;
        } catch (\Exception $exception) {
            Log::warning('wechat-error:'. $exception);
            event(new TriggerWarning('初始化用户失败，创建用户或更新用户失败，请查看系统日志查看bug'));

            return false;
        }

    }

    protected function adminRoute($route)
    {
        return route($route, ['cache_user' => $this->id]);
    }

    public function adminDetailUrl()
    {
        return route('admin.users.detail', ['user' => $this->id]);
    }

    public function adminEditUrl()
    {
//        return $this->adminRoute('admin.user.edit');
    }

    public function adminDeleteUrl()
    {
//        return $this->adminRoute('admin.user.delete');
    }

    public function sex2String()
    {
        switch ($this->detail->sex) {
            case 0:
                return '未知';
            case 1:
                return '男';
            case 2:
                return '女';
            default:
                return '未知';
        }
    }

    public function subscribe2String()
    {
        switch ($this->detail->subscribe) {
            case 0:
                return '未关注';
            case 1:
                return '已关注';
            default:
                return '未知';
        }
    }

    public function umbrellaRemindStillDate()
    {
        return ($this->umbrellaHistories()->where('status', 0)->orderBy('created_at', 'desc')->first())->remindStillDate();
    }
}
