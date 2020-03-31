<?php

namespace App\Http\Controllers\Wechat;

use App\Events\SendWeatherReportToUser;
use App\Events\TriggerSignEvent;
use App\Models\Sign;
use App\Models\SignInfo;
use App\Models\SignMedal;
use App\Models\SignTimer;
use App\Models\Temp;
use App\Models\User;
use App\Repositories\SignCardRepository;
use App\Repositories\WechatDealRepository;
use App\Services\ImageService;
use App\Services\QrcodeService;
use App\Services\RedPackService;
use App\Services\SignService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Repositories\SignDealRepository;
use App\Repositories\SignInfoRepository;
use App\Repositories\SignRepository;
use App\Repositories\SignShareRepository;
use App\Repositories\SignTimerRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class SignController extends Controller
{
    private $sign_repository;
    private $sign_card_repository;
    private $sign_deal_repository;
    private $sign_info_repository;
    private $sign_timer_repository;
    private $sign_share_repository;

    public function __construct(SignRepository $signRepository,
                                SignCardRepository $signCardRepository,
                                SignDealRepository $signDealRepository,
                                SignInfoRepository $signInfoRepository,
                                SignShareRepository $signShareRepository,
                                SignTimerRepository $signTimerRepository)
    {
        $this->sign_repository = $signRepository;
        $this->sign_card_repository = $signCardRepository;
        $this->sign_info_repository = $signInfoRepository;
        $this->sign_deal_repository = $signDealRepository;
        $this->sign_share_repository = $signShareRepository;
        $this->sign_timer_repository = $signTimerRepository;
    }

    public function showApply()
    {
        $this->sign_info_repository->init();
        $this->sign_card_repository->giveUserCard();
        $have_card = $this->sign_card_repository->haveCards();
        $timer = SignTimerRepository::static_getApplyingTimer();
        if (!$timer) {
            $timer = SignTimerRepository::static_getOpeningTimer();
        }
        $now_reward = $timer->reward;
        $is_have = count($have_card);
        $js = app('wechat')->js;

        return view('wechat.sign.apply_new', compact('is_have', 'now_reward', 'js'));
    }

    public function showApplyConfirm()
    {
        $is_apply = $this->sign_info_repository->isApply();

        return view('wechat.sign.confirm', compact('is_apply'));
    }

    public function showApplyShare(SignService $signService)
    {
        $path = $signService->generateApplySharePoster(Auth::user());
        $path = asset($path);
        $js = app('wechat')->js;

        return view('wechat.sign.apply_share', compact('js', 'path'));
    }

    public function showPosterWeek()
    {
        $user = Auth::user();
        $path = 'images/sign/week/'. $user->id.'.png';
        $exist = true;
        if (!file_exists(public_path($path))) {
            $exist = false;
//            $head_img = $user->detail->head_img;
//            $head_img = substr($head_img, 0, strlen($head_img)-1). '132';
//            $head_img = file_get_contents($head_img);
//
//            $image_service = new ImageService(public_path('images/sign/week_gold.png'));
//            $image = $image_service->initSrcImageFormResource($head_img)->resizeSrc(150)->addSrcImage(0.24, 0.595);
//            $image = $image->text('昵称:XIN', [255,255,255], 0.465, 0.625,30);
//            $image = $image->text('总排名:2', [255,255,255], 0.465, 0.660,30);
//            $image = $image->text('早起值:4485', [255,255,255], 0.465, 0.695,30);
//            $image->save(public_path($path));
//
//            unset($image_service);
        }
        $path = asset($path);
        $js = app('wechat')->js;

        return view('wechat.sign.week', compact('path', 'exist', 'js'));

    }

    public function showSharePoster()
    {
        $user = Auth::user();
        $path = 'images/sign/share/'.$user->id.'.png';
        if (!file_exists($path)) {
            $qrcode_service = new QrcodeService(public_path('images/sign/share_qrcode/sign_share_'.$user->id.'.png'));
            $qrcode = $qrcode_service->generateWechat()->addLogo(public_path('images/logo_white.png'))->getPath();
            $image_service = new ImageService(public_path('images/sign/share.jpg'));
            $image_service->initSrcImage($qrcode)
                ->resizeSrc(240)
                ->addSrcImage(0.725, 0.39)
                ->save(public_path('images/sign/share/'.$user->id.'.png'));
            unset($image_service);
        }
        $path = asset($path);
        $js = app('wechat')->js;

        return view('wechat.sign.poster_share', compact('path', 'js'));
    }

    public function showShare(User $user)
    {
        $sign_shares = $this->sign_share_repository->getHelpUserList($user);
        $my_user = $user;
//        dd($sign_shares);

        return view('wechat.sign.share', compact('sign_shares', 'my_user'));
    }

    public function showReward()
    {
        $reward = Auth::user()->sign_info->reward;
        $js = app('wechat')->js;
        return view('wechat.sign.reward', compact('reward', 'js'));
    }

    public function showDonate()
    {
        return view('wechat.sign.donate');
    }

    public function showWithdraw()
    {
        return view('wechat.sign.withdraw');
    }

    public function showDonateInfo()
    {
        return view('wechat.sign.donate_info');
    }

    public function showRefuse()
    {
        return view('wechat.sign.refuse');
    }

    public function realRefuse()
    {
        $temp = Temp::where('type', 'signApplyRefuse')->first();
        if ($temp) {
            $refuseArr = json_decode($temp->data);
            $refuseArr[] = Auth::id();
            $temp->data = json_encode($refuseArr);
            $temp->save();
        } else {
            $refuseArr[] = Auth::id();
            Temp::create([
                'data' => json_encode($refuseArr),
                'type' => 'signApplyRefuse'
            ]);
        }


        return $this->ajaxReturn(0, 'success');
    }

    public function showDonateCert(User $user)
    {
        $user = $user->load('detail');
        $signDonate = $user->signDonates()->where('name', '<>', 'NULL')->orderBy('created_at', 'desc')->first();
        $js = app('wechat')->js;

        return view('wechat.sign.donate_cert', compact('user', 'signDonate', 'js'));
    }

    public function showInfo()
    {
        return view('wechat.sign.info');
    }

    public function donateToUmbrella(WechatDealRepository $wechatDealRepository)
    {
        $money = $this->sign_info_repository->getUmbrellaDonateMoney();
        if ($money) {
            $config = $wechatDealRepository->wechatPayOrder('签到打卡雨伞捐赠', $money, route('wechat.sign.response.donate'));

            return $this->ajaxReturn(1, '等待支付', compact('config'));
        } else {
            return $this->ajaxReturn(0, '捐赠成功', ['redirect' => route('wechat.sign.donate.info')]);
        }

    }

    public function showSetting()
    {
        $this->sign_info_repository->init();
        $user_sign = $this->sign_info_repository->getUserSign();
        $today_sign = $this->sign_repository->getTodaySign();
        $timer = $this->sign_timer_repository->getOpeningTimer();
        $value_rank = $this->sign_info_repository->getTimeValueRank();
        $card_list = $user_sign->user->sign_cards()->orderBy('status', 'asc')->orderBy('created_at', 'desc')->get();

        if ($today_sign) {
            $today_time = $today_sign->today_time;
        } else {
            $today_time = '';
        }

        return view('wechat.sign.setting', compact('user_sign', 'today_sign', 'timer', 'value_rank', 'today_time', 'card_list'));
    }

    public function showRank()
    {
        $all_medals = SignMedal::orderByDesc('gold')
            ->orderByDesc('silver')
            ->orderByDesc('bronze')
            ->with('user.detail')
            ->with('user.sign_info')
            ->get();
        $medals = $all_medals->take(10);
        $length = count($medals);
        for($i = 0; $i < $length; $i++) {
            for ($j = $i+1; $j < $length; $j++) {
                if ($medals[$i]->user->sign_info->time_value > $medals[$j]->user->sign_info->time_value) {
                    if ($medals[$j]->gold >= $medals[$i]->gold) {
                        if ($medals[$j]->silver >= $medals[$i]->silver) {
                            if ($medals[$j]->bronze >= $medals[$i]->bronze) {
                                $temp = $medals[$i];
                                $medals[$i] = $medals[$j];
                                $medals[$j] = $temp;
                            }
                        }
                    }
                }
            }
        }
        $user = Auth::user()->load('detail')->load('signMedal');
        $rank = 0;
        foreach ($all_medals as $key => $medal) {
            $rank++;
            if (!$user->signMedal) {
                $rank = 0;
                break;
            }
            if ($medal->id == $user->signMedal->id) {
                break;
            }
        }


        return view('wechat.sign.rank', compact('medals', 'user', 'rank'));
    }

    public function showRewardRank()
    {
        $reward_ranks = ($this->sign_info_repository->getRewardRank())->take(10);
        $user_sign = Auth::user()->sign_info;
        $user_rank = $this->sign_info_repository->getUserRewardRank();

        return view('wechat.sign.rank_reward', compact('reward_ranks', 'user_sign', 'user_rank'));
    }

    public function index()
    {
        $this->sign_info_repository->init();
        $is_sign = $this->sign_repository->is_sign();
        $today_sign = $this->sign_repository->getTodaySign();
        $today_list = $this->sign_repository->getTodaySignList();
        $month_list = $this->sign_info_repository->getMonthList();
        $total_list = $this->sign_info_repository->getTotalList();
        $user_sign  = $this->sign_info_repository->getUserSign();

        $today_rank = $this->sign_repository->getTodaySignRank($today_sign);
        $month_rank = $this->sign_info_repository->getMonthRank();
        $total_rank = $this->sign_info_repository->getTotalRank();
        $js = (app('wechat'))->js;

        if ($today_sign) {
            $today_time = $today_sign->today_time;
        } else {
            $today_time = '';
        }

        return view('wechat.sign.index', compact('is_sign', 'month_list', 'today_list', 'total_list', 'user_sign', 'today_rank', 'month_rank', 'total_rank', 'today_sign', 'today_time', 'js'));
    }

    public function apply(Request $request)
    {
        if ($this->sign_info_repository->isApply()) {
            return $this->ajaxReturn(3, '您已经报名参与过本轮早起打卡');
        } else {
            $timer = $this->sign_timer_repository->getApplyingTimer();
//            $timer = $this->sign_timer_repository->getApplyingTimer();
            if ($request->get('is_free')) {
                if ($timer) {
                    $this->sign_info_repository->freeApply($timer);
                } else {
                    $timer = $this->sign_timer_repository->getOpeningTimer();
                    if ($this->sign_info_repository->isApplyInTimer($timer)) {
                        return $this->ajaxReturn(4, '您已经报名参与了，请不要重复报名');
                    }
                    $this->sign_info_repository->freeApply($timer);
                }

                return $this->ajaxReturn(0, '免费参与成功', ['redirect' => route('wechat.sign.apply.share')]);
            } else {
                if ($timer) {
                    $config = $this->sign_deal_repository->order($timer->id);
                    if (is_null($config)) {
                        $this->sign_info_repository->depositApply($timer);
                        event(new TriggerSignEvent(Auth::user(), 'applySuccess'));
                        return $this->ajaxReturn(0, '报名成功，上个月缴纳的押金已经自动续费，多出的金额会进入您的个人奖金池，请注意查收', ['redirect' => route('wechat.sign.apply.share')]);
                    }

                    return $this->ajaxReturn(1, '请等待验证支付结果', compact('config'));
                } else {
                    return $this->ajaxReturn(2, '奖金报名已经截止，只能参与免费报名体验本次活动');
                }
            }
        }
    }

    public function useCard(Request $request)
    {
        $success_url = route('wechat.sign.apply.share');
        if ($request->input('sign_card_id')) {
            if ($sign_card = $this->sign_card_repository->isOwnerBySignCardId($request->input('sign_card_id'))) {
                if (!$this->sign_card_repository->canUseBySignCard($sign_card)) {
                    return $this->ajaxReturn(3, '当前折扣卡不可用');
                }
//                $timer = $this->sign_timer_repository->getApplyingTimer();
                $timer = $this->sign_timer_repository->getApplyingTimer();
                if ($sign_card->card->name == '补签卡') {
                    return $this->signAgainUseCard();
                }
                if ($this->sign_info_repository->isApply()) {
                    return $this->ajaxReturn(5, '您已经报名参与本轮签到，请不要重复使用卡券');
                }
                if ($timer) {
                    if ($sign_card->card->name == '五折卡') {
                        if (is_null($config = $this->sign_deal_repository->order($timer->id, $sign_card))) {
                            $this->sign_card_repository->useCardByName('五折卡');
                            $this->sign_info_repository->depositApply($timer);
                            event(new TriggerSignEvent(Auth::user(), 'applySuccess'));

                            return $this->ajaxReturn(0, '报名成功，已经自动续费，若有剩余金额将会转入您的个人奖金', ['redirect' => $success_url]);
                        }
                        Cache::forget('sign_half_card_'.Auth::id());
                        Cache::put('sign_half_card_'.Auth::id(), 1, 10);

                        return $this->ajaxReturn(1, '请等待验证支付结果', compact('config'));
                    }
                    if ($sign_card->card->name == '全免卡') {
                        if (!$this->sign_card_repository->useCardByName('全免卡')) {
                            return $this->ajaxReturn(6, '报名失败');
                        }

                        $this->sign_info_repository->depositApply($timer);
                        event(new TriggerSignEvent(Auth::user(), 'applySuccess'));

                        return $this->ajaxReturn(0, '报名成功', ['redirect' => $success_url]);
                    }

                    if ($sign_card->card->name == '九折卡') {
                        if (is_null($config = $this->sign_deal_repository->order($timer->id, $sign_card))) {
                            $this->sign_card_repository->useCardByName('九折卡');
                            $this->sign_info_repository->depositApply($timer);
                            event(new TriggerSignEvent(Auth::user(), 'applySuccess'));

                            return $this->ajaxReturn(0, '报名成功，已经自动续费，若有剩余金额将会转入您的个人奖金', ['redirect' => $success_url]);
                        }
                        Cache::forget('sign_half_card_9_'.Auth::id());
                        Cache::put('sign_half_card_9_'.Auth::id(), 1, 10);

                        return $this->ajaxReturn(1, '请等待验证支付结果', compact('config'));
                    }

                    if ($sign_card->card->name == '12月九折卡') {
                        if (is_null($config = $this->sign_deal_repository->order($timer->id, $sign_card))) {
                            $this->sign_card_repository->useCardByName('12月九折卡');
                            $this->sign_info_repository->depositApply($timer);
                            event(new TriggerSignEvent(Auth::user(), 'applySuccess'));

                            return $this->ajaxReturn(0, '报名成功，已经自动续费，若有剩余金额将会转入您的个人奖金', ['redirect' => $success_url]);
                        }
                        Cache::forget('sign_half_card_10_'.Auth::id());
                        Cache::put('sign_half_card_10_'.Auth::id(), 1, 10);

                        return $this->ajaxReturn(1, '请等待验证支付结果', compact('config'));
                    }

                    return $this->ajaxReturn(10, '使用失败，参数错误');
//                    if (is_null($config = $this->sign_deal_repository->order($timer->id, $sign_card))) {
//                        if (!$this->sign_card_repository->useCardByName('全免卡')) {
//                            return $this->ajaxReturn(6, '报名失败');
//                        }
//                        $this->sign_info_repository->depositApply($timer);
//
//                        return $this->ajaxReturn(0, '报名成功', ['redirect' => route('wechat.sign.index')]);
//                    }
//                    Cache::put('sign_half_card_'.Auth::id(), 1, 10);
//
//                    return $this->ajaxReturn(1, '请等待验证支付结果', compact('config'));
                } else {
                    return $this->ajaxReturn(8, '奖金报名已经截止，只能参与免费报名体验本次活动', ['redirect' => route('wechat.sign.apply')]);
                }

            } else {
                return $this->ajaxReturn(7, '该卡券您不可使用');
            }
        } else {
            return $this->ajaxReturn(4, '参数错误，请刷新后重试');
        }
    }

    public function showFree2depositApply()
    {
        $timer = SignTimerRepository::static_getOpeningTimer();
        $now_reward = $timer->reward;
        $js = app('wechat')->js;

        return view('wechat.sign.apply_free2deposit', compact('now_reward', 'js'));
    }

    public function free2depositApply()
    {
        return $this->ajaxReturn(3, '报名已截止');
        if (!$this->sign_info_repository->free2depositCanApply()) {
            return $this->ajaxReturn(2, '您没有权限报名');
        }
        $timer = $this->sign_timer_repository->getOpeningTimer();
        if ($timer) {
            $config = $this->sign_deal_repository->free2depositOrder($timer->id);

            return $this->ajaxReturn(1, '请等待验证支付结果', compact('config'));
        } else {
            return $this->ajaxReturn(2, '暂时没有报名的相关信息');
        }
    }


    public function free2depositCheck(Request $request)
    {
        $success_redirect = route('wechat.sign.index');
        $fail_redirect = route('wechat.sign.apply');

        $sign_timer = $this->sign_timer_repository->getOpeningTimer();

        if (Cache::has($request->get('out_trade_no'))) {
            $cache = Cache::get($request->get('out_trade_no'));
            if ($cache == 1) {
                $this->sign_info_repository->depositApply($sign_timer);
                event(new TriggerSignEvent(Auth::user(), 'applySuccess'));

                return $this->ajaxReturn(0, '支付成功', ['redirect' => $success_redirect]);
            } else if ($cache == 2){
                return $this->ajaxReturn(2, '支付失败，请重新报名', ['redirect' => $fail_redirect]);
            } else {
                return $this->ajaxReturn(1, '等待验证');
            }
        } else {
            if ($this->sign_deal_repository->isApplyDeposit($sign_timer)) {
                $this->sign_info_repository->depositApply($sign_timer);
                event(new TriggerSignEvent(Auth::user(), 'applySuccess'));

                return $this->ajaxReturn(0, '支付成功', ['redirect' => $success_redirect]);
            } else {
                $check_status = $this->sign_deal_repository->checkSuccessful($sign_timer);
                if ($check_status == 1) {
                    $this->sign_info_repository->depositApply($sign_timer);
                    event(new TriggerSignEvent(Auth::user(), 'applySuccess'));

                    return $this->ajaxReturn(0, '支付成功', ['redirect' => $success_redirect]);
                } else if ($check_status == 2){
                    $this->ajaxReturn(2, '支付失败，请重新报名'. ['redirect' => $fail_redirect]);
                } else {
                    return $this->ajaxReturn(1, '等待验证');
                }
            }
        }
    }

    public function orderResponse(Request $request)
    {
        $success_redirect = route('wechat.sign.apply.share');
        $fail_redirect = route('wechat.sign.apply');

        $sign_timer = $this->sign_timer_repository->getApplyingTimer();
        if (Cache::has($request->get('out_trade_no'))) {
            $cache = Cache::get($request->get('out_trade_no'));
            if ($cache == 1) {
                $this->sign_info_repository->depositApply($sign_timer);
                if (Cache::has('sign_half_card_'.Auth::id())) {
                    $this->sign_card_repository->useCardByName('五折卡');
                    Cache::forget('sign_half_card_'.Auth::id());
                }
                if (Cache::has('sign_half_card_10_'.Auth::id())) {
                    $this->sign_card_repository->useCardByName('12月九折卡');
                    Cache::forget('sign_half_card_10_'.Auth::id());
                }
                if (Cache::has('sign_half_card_9_'.Auth::id())) {
                    $this->sign_card_repository->useCardByName('九折卡');
                    Cache::forget('sign_half_card_9_'.Auth::id());
                }
                event(new TriggerSignEvent(Auth::user(), 'applySuccess'));

                return $this->ajaxReturn(0, '支付成功', ['redirect' => $success_redirect]);
            } else if ($cache == 2){
                return $this->ajaxReturn(2, '支付失败，请重新报名', ['redirect' => $fail_redirect]);
            } else {
                return $this->ajaxReturn(1, '等待验证');
            }
        } else {
            if ($this->sign_deal_repository->isApplyDeposit($sign_timer)) {
                $this->sign_info_repository->depositApply($sign_timer);
                if (Cache::has('sign_half_card_'.Auth::id())) {
                    $this->sign_card_repository->useCardByName('五折卡');
                }
                event(new TriggerSignEvent(Auth::user(), 'applySuccess'));

                return $this->ajaxReturn(0, '支付成功', ['redirect' => $success_redirect]);
            } else {
                $check_status = $this->sign_deal_repository->checkSuccessful($sign_timer);
                if ($check_status == 1) {
                    $this->sign_info_repository->depositApply($sign_timer);
                    if (Cache::has('sign_half_card_'.Auth::id())) {
                        $this->sign_card_repository->useCardByName('五折卡');
                    }
                    event(new TriggerSignEvent(Auth::user(), 'applySuccess'));

                    return $this->ajaxReturn(0, '支付成功', ['redirect' => $success_redirect]);
                } else if ($check_status == 2){
                    $this->ajaxReturn(2, '支付失败，请重新报名'. ['redirect' => $fail_redirect]);
                } else {
                    return $this->ajaxReturn(1, '等待验证');
                }
            }
        }
    }

    public function payResponse(Request $request)
    {
        return $this->sign_deal_repository->response($request);
    }

    public function donateOrderResponse(Request $request)
    {
        if (Cache::has($request->get('out_trade_no'))) {
            $cache = Cache::get($request->get('out_trade_no'));
            if ($cache == 1) {
                return $this->ajaxReturn(0, '支付成功');
            } else {
                return $this->ajaxReturn(2, '支付异常');
            }
        } else {
            return $this->ajaxReturn(1, '正在验证支付结果');
        }
    }

    public function perfectDonateInfo(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'phone' => 'required'
        ], [
            'name.required' => '请输入姓名',
            'phone.required' => '请输入联系电话'
        ]);

        $user = Auth::user();
        $signDonate = $user->signDonates()->where('name', null)->where('phone', null)->orderBy('created_at', 'desc')->first();
        if ($signDonate) {
            if ($signDonate->type == 'cash') {
                if (is_null($signDonate->wechat_deal_id)) {
                    return $this->ajaxReturn(1, '您还没付款成功');
                } else {
                    $signDonate->name = $request->input('name');
                    $signDonate->phone = $request->input('phone');
                    $signDonate->save();

                    return $this->ajaxReturn(0, '填写成功', ['redirect' => route('wechat.sign.donate.cert', ['user' => $user->id])]);
                }
            } else {
                $signDonate->name = $request->input('name');
                $signDonate->phone = $request->input('phone');
                $signDonate->save();

                return $this->ajaxReturn(0, '填写成功', ['redirect' => route('wechat.sign.donate.cert', ['user' => $user->id])]);
            }
        } else {
            return $this->ajaxReturn(2, '非法操作');
        }
    }

    public function donateResponse(WechatDealRepository $wechatDealRepository) {
        return $wechatDealRepository->signDonateResponse();
    }

    public function clock()
    {
        $sign_timer = $this->sign_timer_repository->getOpeningTimer();

        if (!$sign_timer) {
            return $this->ajaxReturn(1, '本轮签到尚未开始');
        }

        if (!$this->sign_info_repository->canSign()) {
            return $this->ajaxReturn(4, '您还未参与本轮签到');
        }

        if ($this->sign_repository->is_sign()) {
            return $this->ajaxReturn(3, '您今日已经打卡了');
        }

        $sign = $this->sign_repository->sign($sign_timer);
        if ($sign instanceof Sign) {
            $this->sign_repository->computeTimeValue($sign);
        } else {
            return $this->ajaxReturn(2, '签到时间为每天的上午5点 ~ 10点');
        }
        try {
            event(new SendWeatherReportToUser(Auth::user(), $sign));
        } catch (\Exception $exception) {
            Log::error($exception);
        }


        return $this->ajaxReturn(0, '签到成功', compact('sign', 'time_value'));
    }

    private function signAgainUseCard()
    {
        $user = Auth::user();

        switch ($user->sign_info->status) {
            case SignInfoRepository::FIRST_LOST_SIGN:
            case SignInfoRepository::SECOND_LOST_SIGN:
                if ($lost_sign = $this->sign_repository->getLostSign()) {
                    DB::transaction(function () use ($lost_sign) {
                        $this->sign_info_repository->recoverSign();
                        $this->sign_repository->signAgain($lost_sign);
                    });

                    if ($this->sign_card_repository->useCardByName('补签卡')) {
                        return $this->ajaxReturn(0, '补签一次成功', ['redirect' => route('wechat.sign.index')]);
                    } else {
                        return $this->ajaxReturn(1, '补签失败');
                    }
                }
                break;
            case SignInfoRepository::FAIL_SIGN:
                return $this->ajaxReturn(3, '您本轮签到已经失败，不需要进行补签');
                break;
            case SignInfoRepository::NORMAL_SIGN:
            default:
                return $this->ajaxReturn(2, '您不需要进行补签');
                break;
        }
    }

    public function getReward()
    {
        $redpackService = new RedPackService();
        $sign_info = Auth::user()->sign_info;
        if ($sign_info->reward > 0) {
            $reward = $sign_info->reward;
            $sign_info->reward = 0;
            $sign_info->save();
            $redpackService->sign_send($reward);

            return $this->ajaxReturn(0, '提现成功，请留意公众号发送的现金红包');
        } else {
            return $this->ajaxReturn(1, '您没有奖金可以提现');
        }

    }

    public function showCarveUpReward()
    {
        $reward = $this->sign_deal_repository->getOpeningCanCarveReward() / 100;
        $total_reward = $this->sign_deal_repository->getOpeningTimerTotalFee() / 100;
        $fail_reward = $this->sign_deal_repository->getOpeningTimerFailFee() / 100;
        $success_reward = $this->sign_deal_repository->getOpeningTimerSuccessFee() / 100;
        $refund_fee = $this->sign_deal_repository->getOpeningTimerRefundFee() / 100;
//        dd($fail_reward);
        $signInfos = $this->sign_info_repository->carveUpReward($fail_reward);

//        return view('wechat.sign.rank_reward_temp', ['reward_ranks' => $signInfos]);
        return view('data.sign.reward', compact('signInfos', 'reward', 'fail_reward', 'success_reward', 'total_reward', 'refund_fee'));
    }

    public function showTotalReward()
    {
        dd(SignInfo::where('total_reward', '>', 0)->sum('total_reward'));
        $signs = SignInfo::orderBy('total_reward', 'desc')->take(10)->get();

        return view('wechat.sign.rank_reward_total', compact('signs'));
    }

    public function test()
    {
        $sign = Sign::where('user_id', 154)->orderBy('created_at', 'desc')->first();
        $sign_service = new SignService();
        $sign_service->generateWeatherReportNewYear($sign, '1', '1', User::find(154));
    }
}
