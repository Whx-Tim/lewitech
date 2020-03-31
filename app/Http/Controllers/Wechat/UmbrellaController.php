<?php

namespace App\Http\Controllers\Wechat;

use App\Events\ScanUmbrellaCode;
use App\Events\SendPhoneCode;
use App\Events\SendSMS;
use App\Events\TriggerWarning;
use App\Http\Requests\Umbrella\RegisterRequest;
use App\Models\SmsHistroy;
use App\Models\Umbrella;
use App\Models\UmbrellaHistory;
use App\Models\UmbrellaStation;
use App\Models\User;
use App\Models\WechatDeal;
use App\Repositories\UmbrellaHistoryRepository;
use App\Repositories\UmbrellaRepository;
use App\Repositories\WechatDealRepository;
use App\Services\UmbrellaService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UmbrellaController extends Controller
{
    /**
     * 显示设置页面
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function index()
    {
        $wechat = app('wechat');
        $js = $wechat->js;

        $user = Auth::user();
        if (is_null($user->phone) | is_null($user->ID_number) | is_null($user->real_name)) {
            return redirect()->route('wechat.umbrella.register');
        }

        $history_count = UmbrellaHistory::where('user_id', $user->id)->count();
        $can_share = false;

        if (!is_null($user->umbrella)) {
            if ($user->umbrellaInfo->status == 2) {
                $can_share = true;
            }
        }

        return view('wechat.umbrella.index', compact('user', 'history_count', 'can_share', 'js'));
    }

    /**
     * 显示用户注册页面
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showRegister()
    {
        return view('wechat.umbrella.register');
    }

    /**
     * 显示认证校友信息页面
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showSchoolmate()
    {
        return view('wechat.umbrella.schoolmate');
    }

    /**
     * 显示取伞凭证页面
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showPass()
    {
        $user = Auth::user();
        if ($user->umbrellaInfo->status != 2) {
            return redirect()->route('wechat.umbrella.index');
        }
        $time = Carbon::parse(Auth::user()->umbrellaInfo->borrow_at)->format('m月d日 H点i分s秒');

        return view('wechat.umbrella.pass', compact('time'));
    }

    public function showPrompt()
    {
        return view('wechat.umbrella.result', ['code' => 0, 'message' => '请到借伞工作人员处还伞']);
    }

    public function showSelect()
    {
        $is_schoolmate = Auth::user()->is_schoolmate;

        return view('wechat.umbrella.select', compact('is_schoolmate'));
    }

    public function showHistory()
    {
        $user = Auth::user();
        $histories = $user->umbrellaHistories()->orderBy('created_at', 'desc')->get();

        return view('wechat.umbrella.history', compact('histories'));
    }

    public function showHistoryDetail(Request $request)
    {
        $id = $request->get('id');
        if (empty($id)) {
            return redirect()->route('wechat.umbrella.history');
        }
        $history = UmbrellaHistoryRepository::auth()->getHistory($id);
        if (empty($history)) {
            return redirect()->route('wechat.umbrella.history');
        }
        $history = UmbrellaHistoryRepository::formatHistory($history);

        return view('wechat.umbrella.history_detail', compact('history'));
    }

    public function showUseHistory()
    {
        return view('wechat.umbrella.use_history');
    }

    /**
     * 认证校友信息
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function isSchoolmate(Request $request)
    {
        $this->validate($request, [
            'college'        => 'required',
            'grade'          => 'required',
            'student_number' => 'required'
        ], [
            'college.reuqired'        => '请输入您的学院全称',
            'grade.required'          => '请输入您的入学年份',
            'student_number.required' => '请输入您的学号'
        ]);

        $user = Auth::User();
        $user->schoolmateInfo()->create($request->except(['_token', 'name', '_method']));
        $user->update(['is_schoolmate' => 1]);
        $user->umbrellaInfo()->update(['status' => 1]);

        return $this->ajaxReturn(0, '基础认证成功', ['redirect' => route('wechat.umbrella.index')]);
    }

    /**
     * 注册账号
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterRequest $request)
    {
        $user = Auth::user();
        if (!is_null($user->phone) & !is_null($user->ID_number) & !is_null($user->real_name)) {
            return $this->ajaxReturn(4, '您已经注册登记过身份证信息，请直接使用平台进行信用借伞');
        }

        if (!$this->authenticateID($request->input('ID_number'), $request->input('birthday'))) {
            return $this->ajaxReturn(2, '身份认证失败，请输入正确的身份证号码');
        }

        if (Cache::has('vcode_'.$request->input('phone'))) {
            $code = Cache::get('vcode_'.$request->input('phone'));
        } else {
            return $this->ajaxReturn(3, '验证码已经过期，请重新点击发送验证码');
        }

        if ($code == $request->input('vcode')) {

            $user->update($request->except(['vcode']));
            if (is_null($user->umbrellaInfo)) {
                $user->umbrellaInfo()->create(['status' => 1]);
            } else {
                $user->umbrellaInfo()->update(['status' => 1]);
            }

            return $this->ajaxReturn(0, '注册成功', ['redirect' => route('wechat.umbrella.index')]);
        } else {
            return $this->ajaxReturn(1, '注册失败，验证码错误');
        }
    }

    private function authenticateID($ID_number, $birthday)
    {
        $match = [];
        $birthday_arr = explode('-', $birthday);
        if (strlen($ID_number) == 15) {
            preg_match('/^[1-9]\d{5}(\d{2})((0[1-9])|(10|11|12))(([0-2][1-9])|10|20|30|31)\d{2}[0-9Xx]$/', $ID_number, $match);
            if ((int)$match[1] == substr($birthday_arr[0],2) & (int)$match[2] == $birthday_arr[1] & (int)$match[5] == $birthday_arr[2]) {
                return true;
            }
        } else if (strlen($ID_number) == 18) {
            preg_match('/^[1-9]\d{5}((18|19|([23]\d))\d{2})((0[1-9])|(10|11|12))(([0-2][1-9])|10|20|30|31)\d{3}[0-9Xx]$/', $ID_number, $match);
            if ((int)$match[1] == $birthday_arr[0] & (int)$match[4] == $birthday_arr[1] & (int)$match[7] == $birthday_arr[2]) {
                return true;
            }
        }

        return false;
    }

    /**
     * 手机验证码
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function vcode(Request $request)
    {
        $this->validate($request, [
            'phone' => 'required|phone',
        ], [
            'phone.required' => '请输入手机号码',
            'phone.phone'    => '手机号码格式错误'
        ]);
        if (User::where('phone', $request->input('phone'))->first()) {
            return $this->ajaxReturn(1, '该手机已经被注册了');
        }
        $code = mt_rand(100000, 999999);
        Cache::put('vcode_'.$request->input('phone'), $code, 5);
        event(new SendSMS($request->input('phone'), $code, 'umbrellaRegister'));

        return $this->ajaxReturn(0, '验证码已经发送到您的手机，请注意查收');
    }

    public function bindStationUmbrella(Umbrella $umbrella)
    {
        $user = Auth::user();

        try {
            return $this->scanUmbrella($user, $umbrella);
        } catch (\Exception $exception) {
            event(new TriggerWarning('用户扫描借伞异常；查看标识：user_scan_umbrella_error，时间：' . Carbon::now()->toDateTimeString()));

            return view('wechat.umbrella.result', ['code' => 4, 'message' => '系统繁忙，请稍后重试']);
        } catch (\Throwable $e) {

        }

    }

    public function confirmBindStationUmbrella()
    {

    }

    /**
     * 扫描雨伞
     *
     * @param User $user
     * @param Umbrella $umbrella
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     * @throws \Exception
     * @throws \Throwable
     */
    private function scanUmbrella(User $user, Umbrella $umbrella)
    {
        if ($user->blacklists()->where('type', 'umbrella')->first()) {
            return view('wechat.umbrella.result', [
                'code' => 100,
                'message' => '您因公益伞逾期未归还，无权执行该操作，详情请咨询客服公众号~'
            ]);
        }
        $now = Carbon::now()->toDateTimeString();
        event(new ScanUmbrellaCode($umbrella, 'scan'));
        switch ($user->umbrellaInfo->status) {
            case 0:
                return redirect()->route('wechat.umbrella.register');
            case 1:
                if (!is_null($umbrella->user)) {
                    if ($umbrella->user->umbrellaInfo->status == 3) {
                        DB::transaction(function () use ($user, $umbrella) {
                            $now = Carbon::now()->toDateTimeString();
                            if (is_null($umbrella->station)) {
                                $user->umbrellaHistories()->create(['umbrella_id' => $umbrella->id, 'borrow_at' => $now, 'status' => 0, 'form_id' => $umbrella->user->id]);
                            } else {
                                $user->umbrellaHistories()->create(['umbrella_id' => $umbrella->id, 'borrow_at' => $now, 'borrow_station' => $umbrella->station->name, 'status' => 0, 'form_id' => $umbrella->user->id]);
                            }

                            $umbrella->user->umbrellaInfo()->update(['status' => 1, 'still_at' => $now]);
                            $umbrella->user->umbrellaHistories()->where('status', 0)->update(['still_at' => $now, 'status' => 1]);
                            $umbrella->update(['user_id' => $user->id, 'bind_at' => $now, 'borrow_at' => $now, 'still_at' => $now]);
                            $user->umbrellaInfo()->update(['borrow_at' => $now,'status' => 2,'still_at' => NULL]);
                        }, 3);

                        event(new ScanUmbrellaCode($umbrella, 'realScan'));
                        //                        UmbrellaService::donateResponse();

                        $result = ['code' => 0, 'message' => '绑定成功'];
                        break;
                    } else {
                        if ($user->adminset == 5) {
                            $umbrella->update(['user_id' => 0, 'still_at' => $now]);
                            Log::alert('解除绑定雨伞id:' . $umbrella->id);
                            $result = ['code' => 0, 'message' =>'解除绑定成功，该雨伞已经解除绑定状态'];
                            break;
                        }
                        if ($umbrella->user_id == $user->id) {
                            $result = ['code' => 3, 'message' => '已经绑定过该伞了，请不要重复绑定'];
                            break;
                        }

                        $result = ['code' => 8, 'message' => '绑定失败，该雨伞已经被绑定了，请不要重复扫码'];
                        break;
                    }
                } else {
                    DB::transaction(function () use ($user, $umbrella, $now){
                        if (is_null($umbrella->station)) {
                            $user->umbrellaHistories()->create(['borrow_at' => $now, 'umbrella_id' => $umbrella->id]);
                        } else {
                            $user->umbrellaHistories()->create(['borrow_at' => $now, 'umbrella_id' => $umbrella->id, 'borrow_station' => $umbrella->station->name]);
                            $umbrella->station()->decrement('amount');
                        }

                        $user->umbrellaInfo()->update(['borrow_at' => $now,'status' => 2,'still_at' => NULL]);
                        $umbrella->update(['user_id' => $user->id, 'bind_at' => $now, 'borrow_at' => $now]);

                    });
                    event(new ScanUmbrellaCode($umbrella, 'realScan'));

                    $result = ['code' => 0, 'message' => '绑定成功', 'redirect' => route('wechat.umbrella.pass')];
                    break;
                }

                break;
            case 2:
                if ($user->adminset == 5) {
                    $umbrella->update(['user_id' => 0, 'still_at' => $now]);
                    Log::alert('解除绑定雨伞id:' . $umbrella->id);
                    $result = ['code' => 0, 'message' =>'解除绑定成功，该雨伞已经解除绑定状态'];
                    break;
                }
                $result = ['code' => 6, 'message' => '你已经绑定过雨伞了，请不要重复绑定'];

                break;
            case 3:
                $result = ['code' => 5, 'message' => '你的雨伞正在流转中，请不要重复绑定雨伞'];
                break;
            default:
                $result = ['code' => 4, 'message' => '系统繁忙，请稍后重试'];
                break;
        }

        return view('wechat.umbrella.result', $result);
    }

    /**
     * 万能二维码-强制还伞
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function forceStill()
    {
        $result = $this->still_handle();

        return view('wechat.umbrella.result', $result);
    }

    /**
     * @return array
     * @throws \Throwable
     */
    public function still_handle()
    {
        try {
            $user = Auth::user();
            if ($user->blacklists()->where('type', 'umbrella')->first()) {
                return ['code' => 200, 'message' => '您因公益伞逾期未归还，无权执行该操作，详情请咨询客服公众号~'];
            }
            $now = Carbon::now()->toDateTimeString();
            if ($user->umbrellaInfo->status == 1) {
//                $user->umbrellaHistories()->create(['borrow_at' => $now]);
//                $user->umbrellaInfo()->update(['borrow_at' => $now,'status' => 2,'still_at' => NULL]);
                $result = ['code' => 100, 'message' => '请扫描雨伞上的二维码绑定借伞'];

//                $result = ['code' => 0, 'message' => '借伞成功', ['redirect' => route('wechat.umbrella.pass')]];
            } else {
                if (is_null($history = $user->umbrellaHistories()->where('status', 0)->first())) {
                    DB::transaction(function () use ($user, $now) {
                        $user->umbrellaInfo()->update(['status' => 1, 'still_at' => $now]);
                        $user->umbrellaInfo()->increment('force_count');
                    });
                } else {
                    if (is_null($user->umbrella)) {
                        DB::transaction(function () use ($history, $user, $now) {
                            $history->update(['status' => 1, 'still_at' => $now]);
                            $user->umbrellaInfo()->update(['status' => 1, 'still_at' => $now]);
                            $user->umbrellaInfo()->increment('force_count');
                        }, 3);
                    } else {
                        DB::transaction(function () use ($history, $user, $now) {
                            $history->update(['status' => 1, 'still_at' => $now]);
                            $user->umbrellaInfo()->update(['status' => 1, 'still_at' => $now]);
                            $user->umbrellaInfo()->increment('force_count');
                            $user->umbrella()->update(['user_id' => 0, 'still_at' => $now]);
                        }, 3);
                    }
                }

                $result = ['code' => 0, 'message' => '还伞成功！'];
            }
        } catch (\Exception $exception) {
            $result = ['code' => 2, 'message' => '系统繁忙，请稍后重试'];
            $error_sign = 'umbrella_force_still_error';
            event(new TriggerWarning('万能备用码出现异常，请搜索'. $error_sign .'查看异常日志'));
            Log::warning($error_sign.':'.$exception);
        }

        return $result;
    }

    /**
     * 开启传递
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function openShare()
    {
        $user = Auth::user();
        $user->umbrellaInfo()->update(['status' => 3]);

        return $this->ajaxReturn(0, '流转打开成功');
    }

    /**
     * 取消传递
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancelShare()
    {
        $user = Auth::user();
        $user->umbrellaInfo()->update(['status' => 2]);

        return $this->ajaxReturn(0, '取消流转成功');
    }

    /**
     * 显示打赏页面
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showGratuity()
    {
        $deals = WechatDeal::select(['user_id', 'openid', 'total_fee'])->where('description', '公益爱心伞用户打赏')->where('result_code', WechatDealRepository::SUCCESS_STATUS)->with('user.detail')->get();
        $deals->each(function ($item) {
            $item->money = $item->total_fee / 100;
            return $item;
        });
        $deal_count = count($deals);
        $first_deal = $deals->first();
        $first_deal->money = $first_deal->total_fee / 100;

        return view('wechat.umbrella.gratuity', compact('deals', 'deal_count', 'first_deal'));
    }

    /**
     * 打赏
     *
     * @param Request $request
     * @param WechatDealRepository $wechatDealRepository
     * @return \Illuminate\Http\JsonResponse
     */
    public function gratuity(Request $request, WechatDealRepository $wechatDealRepository)
    {
        $this->validate($request, [
            'money' => 'required'
        ], [
            'money.required' => '请输入打赏金额'
        ]);

        $config = $wechatDealRepository->wechatPayOrder('公益爱心伞用户打赏', $request->input('money'));

        return $this->ajaxReturn(1, '下单成功', compact('config'));
    }

    /**
     * 打赏支付回应
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function gratuityOrderResponse(Request $request)
    {
        if (Cache::has($request->get('out_trade_no'))) {
            $cache = Cache::get($request->get('out_trade_no'));
            if ($cache == 1) {
                return $this->ajaxReturn(0, '支付成功', ['redirect' => route('wechat.umbrella.index')]);
            } else {
                return $this->ajaxReturn(2, '支付异常');
            }
        } else {
            return $this->ajaxReturn(1, '正在验证支付结果');
        }
    }

    /**
     * 检查传递
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkShare()
    {
        $user = Auth::user();
        if ($user->umbrellaInfo->status != 3) {
            return $this->ajaxReturn(0, '传递成功');
        }

        return $this->ajaxReturn(1, '正在传递中，请稍等');
    }

    /**
     * 显示传递成功
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showShareSuccess()
    {
        return view('wechat.umbrella.result', ['code' => 0, 'message' => '传递成功']);
    }

}
