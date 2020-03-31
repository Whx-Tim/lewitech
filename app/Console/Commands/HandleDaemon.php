<?php

namespace App\Console\Commands;

use App\Events\SendSMS;
use App\Events\TriggerGetUpNotice;
use App\Events\TriggerUmbrellaNotice;
use App\Events\TriggerWarning;
use App\Events\viewPage;
use App\Models\Badge\Badge;
use App\Models\Blacklist;
use App\Models\Business;
use App\Models\GetUp;
use App\Models\School;
use App\Models\SignDonate;
use App\Models\SignInfo;
use App\Models\SignTimer;
use App\Models\Umbrella;
use App\Models\UmbrellaHistory;
use App\Models\User;
use App\Models\WechatUserDetail;
use App\Repositories\BlacklistRepository;
use App\Repositories\SchoolBadgeRepository;
use App\Repositories\SchoolRepository;
use App\Repositories\SignInfoRepository;
use App\Repositories\SignTimerRepository;
use App\Repositories\UmbrellaRepository;
use App\Services\WeatherService;
use Carbon\Carbon;
use EasyWeChat\Message\Text;
use Illuminate\Console\Command;
include_once app_path('/vendor/Ucpaas.class.php');
use Illuminate\Support\Facades\Log;
use Ucpaas;

class HandleDaemon extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'daemon:handle {--name=} {--id=} {--filename=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "handle some daemon for option's name";

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $name = $this->option('name');
        if (!empty($this->option('id'))) {
            $this->{$name}($this->option('id'));
            exit;
        }

        if (!empty($this->option('filename'))) {
            $this->{$name}($this->option('filename'));
            exit;
        }

        $this->{$name}();
    }

    public function menu()
    {
        $wechat = app('wechat');
        $menu = $wechat->menu;
        $buttons = [
            [
                'type' => 'view',
                'name' => '借伞·还伞',
                'url'  => 'http://wx.lewitech.cn/wechat/umbrella/index'
            ],
            [
                'type' => 'view',
                'name' => '免费体验课',
                'url'  => 'https://mp.weixin.qq.com/s/pgot2U6ZO9OraoNRa9j2ug'
            ],
            [
                'name' => '早起打卡',
//                'type' => 'view',
//                'url'  =>  'http://wx.lewitech.cn/wechat/daysign'
                'sub_button' => [
                    [
                        'type' => 'view',
                        'name' => '早起打卡3.0',
                        'url'  => 'http://wx.lewitech.cn/wechat/daysign'
                    ],
                    [
                        'type' => 'view',
                        'name' => '世界杯头像',
                        'url'  => 'http://wx.lewitech.cn/wechat/badge/world'
                    ]
                ]
            ]
        ];
        $menu->add($buttons);
    }

    public function test()
    {
        $first = Carbon::today()->addHours(5);
        $second = Carbon::today()->addHours(8)->addMinutes(30);
        dd($first->diffInSeconds($second));
        exit;
        $names = ['乌拉圭', '俄罗斯', '克罗地亚', '巴西', '比利时', '法国', '瑞典', '英格兰'];
        foreach ($names as $name) {
            Badge::create([
                'name' => $name,
                'type' => 'world',
                'local_url' => 'images/badge/world/badge/' . $name . '.png'
            ]);
        }
        $this->info('successful');
    }

    public function static_test()
    {
        $this->info('static_succcessful');
    }

    public function image()
    {
        $this->recursionImage('http://116.13.96.74:2017/%E5%8E%86%E5%B1%8A%E6%AF%95%E4%B8%9A%E5%85%B8%E7%A4%BC/2016%E5%B1%8A%E6%AF%95%E4%B8%9A%E5%85%B8%E7%A4%BC/2016%E5%B9%B4%E6%AF%95%E4%B8%9A%E5%85%B8%E7%A4%BC6.21%E4%B8%8B%E5%8D%88/%E6%9C%BA%E4%BD%8D%E4%BA%8C/', '/Users/timx/Downloads/2016-6-21-pm/2号');
    }

    public function recursionImage($url = '',$image_path = '')
    {
        $content = file_get_contents($url);
        preg_match_all('/<A HREF="([^\s>]+)"[^>]*>[\s\S]*?([^<>]*)<\/A>/', $content, $array);
        $link_array = $array[1];
        $name_array = $array[2];
        $bar = $this->output->createProgressBar(count($link_array));
        foreach ($link_array as $key => $item) {
            if ($key == 0) continue;
            if (substr($item, -1) == '/') {
                $this->recursionImage('http://116.13.96.74:2017'.$item, $image_path.$name_array[$key]);
                continue;
            }
            $file_name = $name_array[$key];
            try {
                $file_path = 'http://116.13.96.74:2017'.$item;
                $file = file_get_contents($file_path);
                $local_path = $image_path.$file_name;
                file_put_contents($local_path, $file);
            } catch (\Exception $exception) {
                $this->info('缺失'.$file_name);
            }
            $bar->advance();
        }
        $bar->finish();
    }

    protected function number2filename($number, $prefix)
    {
        $length = strlen($number);
        for($i = $length; $i < 4; $i++) {
            $number = '0'.$number;
        }
        $filename = $prefix.$number.'.jpg';
        return $filename;
    }

    protected function qrcode($filename)
    {
        $wechat = app('wechat');
        $qrcode = $wechat->qrcode;
        $result = $qrcode->forever($filename);
        $ticket = $result->ticket;
        $url    = $qrcode->url($ticket);


        $content = file_get_contents($url);
        $this->info(public_path('images/event_qrcode/'. $filename .'.png'));
        file_put_contents(public_path('images/event_qrcode/'. $filename .'.png'), $content);

        $this->info('successful');

    }

    protected function sms()
    {
    }

    public function tag()
    {
        $wechat = app('wechat');
//        $group = $wechat->user_group;
        $tag = $wechat->user_tag;
//        $tag->create('5月9号之前');
//        dd($tag->lists());
//
        $users = WechatUserDetail::where('subscribe', 1)->whereDate('created_at', '<=', '2018-05-09')->orderBy('id', 'asc')->with('user')->get();
        $openids = [];
        foreach ($users as $user) {
            $openids[] = $user->user->openid;
//            $this->info($user->user->openid);
        }
//        dd(count($openids));
        foreach (array_chunk($openids, 50) as $chunk_openids) {
            $this->info($chunk_openids[0]);
            $tag->batchTagUsers($chunk_openids, '107');
        }
//        $tag->batchTagUsers($openids, '106');
        $this->info('successful');

//        $tag->batchTagUsers($openids, 100);
    }

    public function group()
    {
        $wechat = app('wechat');
        $userService = $wechat->user;
        $bar = $this->output->createProgressBar(count($userService->lists()->data['openid']));
        foreach ($userService->lists()->data['openid'] as $openid) {
            User::firstCreateOrUpdate($openid);

            $bar->advance();
        }

        $bar->finish();


        $this->info('successful');
//        $this->info($tag->create('排除地铁人员标签'));
//
//        $this->info($group->create('排除地铁人员组'));
    }

    public function business()
    {
        $data = file_get_contents('httP://weijuan.szu.edu.cn/n/api/v1/index/map/business');
        $data = json_decode($data)->data;
        $bar = $this->output->createProgressBar(count($data));

        foreach ($data as $datum) {
            $content = [];
            foreach ($datum as $key => $item) {
                $content[str_replace('business_', '', $key)] = $item;
            }
            $content['user_id'] = 154;
            $content['score'] = 0;
            unset($content['id']);

            if (is_null($content['branch_address'])) {
                unset($content['branch_address']);
                Business::create($content);
            } else {
                $branches = json_decode($content['branch_address']);
                unset($content['branch_address']);
                $business = Business::create($content);
                foreach ($branches as $branch) {
                    $business->branches()->create([
                        'name' => $branch->business_store,
                        'address' => $branch->business_address,
                        'phone' => $branch->business_phone
                    ]);
                }
            }
            $bar->advance();
        }

        $bar->finish();
    }

    public function blacklist()
    {
        $blacklist = new BlacklistRepository(new Blacklist());
        $blacklist->delete(5);
        $this->info('successful');
    }

    public function recoveryUmbrella($id)
    {
        $umbrella_repository = new UmbrellaRepository();
        $user = User::find($id);
        if (!$user) {
            $this->info('找不到用户');
            exit;
        }
        $openid = $user->openid;
        $result = $umbrella_repository->forceStill($openid);

        $this->info($result['message']);
    }

    public function testSms()
    {
        $user = User::find(154);
        dd($user);
        exit;
        event(new SendSMS('13418866733', '123456', 'still'));
    }

    public function getup()
    {
        $users = User::with('detail')->with('getUpInfo')->get();
        $this->info(count($users));
        $count = 0;
        event(new TriggerWarning('发送早起打卡推广消息开始'));
        foreach ($users as $user) {
            $getup = $user->getUpInfo;
            if (!empty($user->detail->subscribe)) {
                if ($user->detail->subscribe) {
                    if (empty($user->getUpInfo->day_duration)) {
//                $this->info($user->openid);
                        $openid= $user->openid;
                        $app = app('wechat');
                        $data = [
                            'first' => [
                                'value' => '听说，早起的一天，时间会变多！早起让你专注力提升，效率杠杠滴~早起还能轻松躲过地铁高峰！早睡早起，身体棒棒！校友共享圈邀你一起加入早起打卡啦！

',
                                'color' => '#000000'
                            ],
                            'keyword1' => '0天',
                            'keyword2' => '暂无',
                            'keyword3' => '暂未打卡',
                            'remark'   => [
                                'value' => '★点击底部菜单"互动互助"-"早起打卡"就能参与。
☆打卡开放时间：每天上午5:00~10:00
★打卡成功还可以查看"早起排行榜"，加油加油！',
                                'color' => '#000000'
                            ]
                        ];

                        Log::info('早起打卡模板推广openid:'.$openid);
                        try {
                            $url = url('/wechat/getup/index');
                            $app->notice->uses('rvOhmHAHZ5fiJosF0dX4_SU0rpuiAN3NGxMFM9igH44')->andData($data)->andReceiver($openid)->withUrl($url)->send();
                            Log::info('早起打卡模板推广成功openid: '. $openid);
                        } catch (\Exception $exception) {
                            event(new TriggerWarning('早起打卡推广消息发送异常，请查看日志'));
                            Log::warning($exception);
                        }
                        $count++;
                    } else {
                        if (!Carbon::parse($user->getUpInfo->last_get_up_datetime)->isToday()) {
                            $openid= $user->openid;
                            $app = app('wechat');
                            $data = [
                                'first' => [
                                    'value' => '听说，早起的一天，时间会变多！早起让你专注力提升，效率杠杠滴~早起还能轻松躲过地铁高峰！早睡早起，身体棒棒！校友共享圈邀你一起加入早起打卡啦！

',
                                    'color' => '#000000'
                                ],
                                'keyword1' => '0天',
                                'keyword2' => '暂无',
                                'keyword3' => '暂未打卡',
                                'remark'   => [
                                    'value' => '★点击底部菜单"互动互助"-"早起打卡"就能参与。
☆打卡开放时间：每天上午5:00~10:00
★打卡成功还可以查看"早起排行榜"，加油加油！',
                                    'color' => '#000000'
                                ]
                            ];

                            Log::info('早起打卡模板推广openid:'.$openid);
                            try {
                                $url = url('/wechat/getup/index');
                                $app->notice->uses('rvOhmHAHZ5fiJosF0dX4_SU0rpuiAN3NGxMFM9igH44')->andData($data)->andReceiver($openid)->withUrl($url)->send();
                                Log::info('早起打卡模板推广成功openid: '. $openid);
                            } catch (\Exception $exception) {
                                event(new TriggerWarning('早起打卡推广消息发送异常，请查看日志'));
                                Log::warning($exception);
                            }
                            $count++;
                        }
                    }
                }
            }
        }
        event(new TriggerWarning('发送早起打卡推广消息结束，成功发送'.$count.'条'));
//        event(new TriggerGetUpNotice('oSiVJ0s1VNlyopzRrJZL4oCHbVVQ', $getup, 'all'));
//        event(new TriggerGetUpNotice('oSiVJ0s1VNlyopzRrJZL4oCHbVVQ', $getup, 'everyday'));
    }

    public function getupRemind()
    {
        $todays = GetUp::whereDate('last_get_up_datetime', Carbon::today()->subDay()->toDateString())->with('user.detail')->get();
        foreach($todays as $getUp) {
            if ($getUp->user->detail->subscribe == 1) {
                $this->info($getUp->open_id);
            }

        }

        $this->info(count($todays));
    }

    public function sign()
    {
        $getUp = GetUp::where('open_id', 'oSiVJ0s1VNlyopzRrJZL4oCHbVVQ')->first();
        event(new TriggerGetUpNotice($getUp->open_id, $getUp, 'today'));
        event(new TriggerGetUpNotice($getUp->open_id, $getUp, 'everyDay'));
        event(new TriggerGetUpNotice($getUp->open_id, $getUp, 'all'));
        event(new TriggerGetUpNotice($getUp->open_id, $getUp, 'hadApply'));

    }

    public function sign_timer_create()
    {
        $timer_repository = new SignTimerRepository(new SignTimer());
        $timer_repository->create();
        $this->info('successful');
    }

    public function testCarbon()
    {
        $this->info(Carbon::now()->format('Ymdhis'));
        $this->info(Carbon::parse(20171019180231)->toDateTimeString());
//        $this->info(Carbon::createFromFormat('ymdhis', 20171019180231)->toDateTimeString());
    }

    public function getUpAvatar()
    {
        $getups = GetUp::orderBy('day_total', 'desc')->take(10)->get();
        $array = [];
        foreach ($getups as $item) {
            $array[] = $item->open_id;
        }
        $users = User::whereIn('openid', $array)->with('detail')->get();
        foreach ($users as $user) {
            $this->info($user->detail->head_img);
        }
    }

    public function umbrellaQuestion()
    {
        $users = User::whereHas('umbrellaInfo', function ($query) {
            $query->whereNotNull('borrow_at')->whereNotIn('user_id', [1, 4, 91, 466]);
        })->get();
        $bar = $this->output->createProgressBar(count($users));
        foreach ($users as $user) {
            event(new TriggerUmbrellaNotice($user->openid, [], 'question'));
            $bar->advance();
        }

        $bar->finish();
    }

    public function ApplyStaff()
    {
        $wechat = app('wechat');
        $users = User::where('id', '>=', 1772)->where('id', '<=', 1783)->with('detail')->get();
        $staff = $wechat->staff;
        $text = new Text();
        $text->content = '您好！亲爱的用户，现在可以点击-互动互助-早起打卡，即可参与签到打卡，已经为您特意延长报名时间';
        foreach ($users as $user) {
            $this->info($user->id);
            if ($user->detail->subscribe == 1) {
                $this->info($user->id);
                $staff->message($text)->to($user->openid)->send();
            }
        }


    }

    public function umbrellaRemind()
    {
        $users = User::whereHas('umbrellaHistories', function ($query) {
            $query->where('id', '>=', 710)->where('id', '<=', 722);
        })->get();
        $bar = $this->output->createProgressBar(count($users));
        foreach ($users as $user) {
            event(new TriggerUmbrellaNotice($user->openid, ['user'=> $user],  'remindStill'));
            $bar->advance();
        }
        $bar->finish();

    }

    public function writeText()
    {
        $text = file_get_contents(base_path('sign_week_text'));

        $text = json_decode($text);
        file_put_contents(base_path('temp/sign_everyday.json'), json_encode($text));
        $this->info('successful');
    }

    public function readText()
    {
        $text = file_get_contents(base_path('temp/sign_everyday.json'));
        $messages = json_decode($text);
        foreach ($messages as $message) {
            $this->info($message->text1);
        }
    }

    public function sendMessage()
    {
        $wechat = app('wechat');
        $staff = $wechat->staff;
        $sign_info_repository = new SignInfoRepository(new SignInfo());
        $sign_infos = $sign_info_repository->getTotalList();
        foreach ($sign_infos as $key => $sign_info) {
            if ($key > 1) {
//                $this->info($sign_info->user->openid);
                $text = new Text();
//                $text->content = '亲爱的'. $sign_info->user->detail->nickname .'您好，根据排行榜显示，您一直居于我们早起打卡的榜首，请问是什么使您坚持每天早起打卡的呢？或者有什么坚持早起的秘诀呢？可否跟我们简单分享一下~或者有什么宝贵建议也可以给到我们~-[愉快] 直接回复公众号即可~';
                $this->info($text->content);
//                $staff->message($text)->to($sign_info->user->openid)->send();
            }
        }
        $staff->message($text)->to('oSiVJ0sIGM7hRMYTdRgulvSGwr3I')->send();
    }

    public function signDonateResponse()
    {
        $donates = SignDonate::where('remind', '>', 0)->where('wechat_deal_id', '<>', NULL)->where('name', '<>', NULL)->with('user.detail')->get();
        foreach ($donates as $donate) {
            $this->info($donate->name);
        }
    }

    public function signDonateEveryWeekAdd()
    {
        $ids = [1];
        SignDonate::whereIn('user_id', $ids)->where('name', '<>', NULL)->where('wechat_deal_id', '<>', NULL)->increment('remind');
    }





}
