<?php

namespace App\Http\Controllers\Wechat;

use App\Events\viewPage;
use App\Models\Temp;
use App\Repositories\InsuranceRepository;
use App\Services\ImageService;
use App\Services\WeatherService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class TempActiveController extends Controller
{
    /**
     * 临时显示页面
     *
     * @param $name
     * @return mixed
     */
    public function show($name)
    {
        return call_user_func([new self(), $name]);
    }

    /**
     * 6.17号临时活动
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function active617()
    {
        return view('wechat.tempActive.active617');
    }

    /**
     * 8.04号股东活动
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function active804()
    {
        return view('wechat.tempActive.active804');
    }

    public function video()
    {
        return view('temp.video');
    }

    public function showTest()
    {

    }

    /**
     * 报名参与活动
     *
     * @param Request $request
     * @param $type
     * @return \Illuminate\Http\JsonResponse
     */
    public function apply(Request $request, $type)
    {
        $this->validate($request, [
            'name' => 'required',
            'position' => 'required',
            'undertaking_introduction' => 'required',
            'undertaking_content' => 'required',
            'lewitech' => 'required'
        ], [
            'name.required' => '请填写您的姓名',
            'position.required' => '请填写您的工作单位及职务',
            'undertaking_introduction.required' => '请填写企业简介',
            'undertaking_content.required' => '请填写企业供需内容',
            'lewitech.required' => '请填写我心中的乐微'
        ]);
        $data = json_encode($request->except(['_token', '_method']));
        $apply = Temp::create([
            'data' => $data,
            'type' => $type
        ]);

        return $this->ajaxReturn(0, '报名成功', ['redirect' => route('wechat.temp.show', ['name' => 'shareholderResult'])]);
    }

    /**
     * 显示6.17号数据统计
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showDataActive617()
    {
        $applies = Temp::where('type', 'active617')->orderBy('created_at', 'desc')->get();

        return view('data.active617', compact('applies'));
    }

    public function showDataActive804()
    {
        $applies = Temp::where('type', 'active804')->orderBy('created_at', 'desc')->get();

        return view('data.active617', compact('applies'));
    }

    public function test()
    {
        $weather_service = new WeatherService();
        $type = $weather_service->getType();
        $image_path = public_path('images/weather/sign/sunny.png');
        if (str_contains($type, '雨')) {
            $image_path = public_path('images/weather/sign/rain.png');
        }
        if (str_contains($type, '云')) {
            $image_path = public_path('images/weather/sign/cloud.png');
        }
        $today_week = Carbon::today()->dayOfWeek;
        switch ($today_week) {
            case 0:
                $today_week = '周日';
                break;
            case 1:
                $today_week = '周一';
                break;
            case 2:
                $today_week = '周二';
                break;
            case 3:
                $today_week = '周三';
                break;
            case 4:
                $today_week = '周四';
                break;
            case 5:
                $today_week = '周五';
                break;
            case 6:
                $today_week = '周六';
                break;
        }
        $image_x = 0.05;
        $rgb = [255,255,255];

        $image_service = new ImageService($image_path);
        $today_temperature = $weather_service->getWendu();
        $image = $image_service->text($today_temperature.'℃', $rgb, $image_x, 0.13, 80)
            ->text($today_week, $rgb, $image_x, 0.17, 30)
            ->text($weather_service->getType(), $rgb, $image_x, 0.28, 45)
            ->text(Carbon::today()->format('Y年m月d日'), $rgb, $image_x, 0.32, 28)
            ->text('打卡时间', $rgb, 0.377, 0.53, 35)
            ->text('09:55', $rgb, 0.377, 0.59, 55)
//            ->text('今日鸡汤:', $rgb, 0.08, 0.815, 30)
            ->text('你若要别人你若要别人喜爱你的价值，',$rgb, $this->text_center('你若要别人你若要别人喜爱你的价值，'), 0.85, 25)
            ->text('你就得给世界创造价值。', $rgb, $this->text_center('你就得给世界创造价值。'), 0.88, 25)
            ->get();
        header('Content-Type:image/png');
        imagepng($image);
        exit;
    }

    public function text_center($text)
    {
        $text_numer = 0.27;
        $every_font = 0.02;
        $length = mb_strlen($text);
        $diff = $length - 11;
        $result = $text_numer - ($every_font*$diff);

        return $result;
    }

    public function insurance()
    {
        return view('wechat.tempActive.insurance');
    }

    public function addInsurance(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'phone' => 'required'
        ], [
            'name.required' => '请输入投保人的姓名',
            'phone.required' => '请输入投保人的联系手机'
        ]);

        $insuranceRepository = new InsuranceRepository();
        $insurance = $insuranceRepository->create($request);

        return $this->ajaxReturn(0, '保存成功', ['redirect' => 'https://esales.aegonthtf.com/sales/preference/page/premium.html?sourceFrom=aegon-cnooc&sid=CBC']);
    }

    public function shareholder()
    {
        return view('temp.shareholder');
    }

    public function shareholderResult()
    {
        return view('temp.shareholder_result', ['message' => '感谢您的填写~']);
    }

    public function red_hat()
    {
        $subscribe = false;
        $user = Auth::user();
        if ($user->detail->subscribe) {
            $subscribe = true;
        }

        $head_img = str_replace('wx.qlogo.cn','wx.qlogo.h-hy.com', $user->detail->head_img);

        return view('wechat.tempActive.red_hat', compact('subscribe', 'head_img'));
    }


}
