<?php

namespace App\Console\Commands;

use App\Events\SendSMS;
use App\Events\TriggerUmbrellaNotice;
use App\Models\Blacklist;
use App\Models\Qrcode;
use App\Models\Umbrella;
use App\Models\UmbrellaHistory;
use App\Models\UmbrellaStation;
use App\Models\User;
use App\Models\UserUmbrella;
use App\Models\WechatDeal;
use App\Repositories\Umbrella\OperationRepository;
use App\Repositories\Umbrella\StationRepository;
use App\Repositories\UmbrellaRepository;
use Carbon\Carbon;
use EasyWeChat\Message\Image;
use EasyWeChat\Message\Text;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UmbrellaDaemon extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'umbrella:daemon {method} {--id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'handle umbrella daemon';

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
        $method = $this->argument('method');
        if ($this->option('id')) {
            return $this->{$method}($this->option('id'));
        }

        return $this->{$method}();
    }

    /**
     * 临时执行程序方法
     */
    public function temporary()
    {
        $wechat = app('wechat');
//        $material = $wechat->material_temporary;
//        dd($material->uploadImage(public_path('images/temp/guanggao.png')));
//        exit;
        $user = User::find(154);
        $openid = $user->openid;
        $text = new Text();
        $staff = $wechat->staff;
        $text->content = '还伞成功！感谢您对共享雨伞的支持~您的每次用伞都由"乐微科技"冠名支持，请继续支持我们！
<a href="'. route('wechat.pay.response.umbrella.gratuity') .'">查看冠名企业</a>         <a href="'. route('wechat.umbrella.history.detail', ['id' => '10']) .'">查看还伞信息</a>';
        $image = new Image(['media_id' => 'uClInXJ_GP_6bHicJdGtfNJ8VxbpzU7yTSZhGPnY_23ZCHe5Y9MtqtwZvvzhVs6E']);
        $staff->message($image)->to($openid)->send();
        $staff->message($text)->to($openid)->send();
//        event(new TriggerUmbrellaNotice($user->openid, ['user' => $user], 'lastStill'));
//        event(new TriggerUmbrellaNotice($user->openid, ['user' => $user], 'remindStill'));
//        event(new SendSMS($user->phone, '明日', 'still'));
    }

    /**
     * 生成新的地铁站
     *
     * @throws \Exception
     */
    public function generateStation()
    {
        $station = '白石洲站';
        StationRepository::self()->createWithQrcode($station);
//        $stations = explode('、', $stations);
//        foreach ($stations as $station) {
//            StationRepository::self()->createWithQrcode($station);
//        }
    }

    /**
     * 创建雨伞的数据库记录
     */
    public function createUmbrella()
    {
        $count = 2000;
        $bar = $this->output->createProgressBar($count);
        for ($i = 0; $i < $count; $i++) {
            Umbrella::create(['station_id' => 0]);
            $bar->advance();
        }

        $bar->finish();
    }

    public function removeBlacklist($id)
    {
        $black = Blacklist::where('user_id', $id)->first();
        if ($black) {
            $black->delete();
            $this->info('清楚成功');
        } else {
            $this->info('未找到该用户');
        }
    }

    /**
     * 用户进行强制还伞
     *
     * @param $id
     */
    public function recovery($id)
    {
        $umbrella_repository = new UmbrellaRepository();
        $user = User::find($id);
        if (!$user) {
            $this->info('找不到用户');
            exit;
        }
        $openid = $user->openid;
        $result = $umbrella_repository->forceStill($openid);
        event(new TriggerUmbrellaNotice($openid, ['user' => $user], 'relieveNotice'));
        $black = Blacklist::where('user_id', $user->id)->first();
        if ($black) {
            $black->delete();
        }
        $this->info($result['message']);
    }

    public function moneyRecovery($id)
    {
        try {
            UmbrellaRepository::self()->moneyRecovery($id);
        } catch (\Exception $exception) {
            $this->info($exception->getMessage());
        }
    }

    /**
     * 清除用户注册信息
     *
     * @param $id
     */
    public function clearUmbrellaUserInfo($id)
    {
        $user = User::find($id);
        $user->birthday = null;
        $user->real_name = null;
        $user->phone = null;
        $user->ID_number = null;
        $user->save();

        $this->info('清除成功');
    }

    /**
     * 运营数据统计
     */
    public function operationData()
    {
        $operation = new OperationRepository();
        $this->info('站点激活的雨伞总数：' . $operation->stationStillUmbrellaCount());
        $this->info('二次使用累计借出雨伞次数：' . $operation->borrowUmbrellasCountNotNull());
        $this->info('累计归还的雨伞次数：' . $operation->stillUmbrellasCount());
        $this->info('总计借出雨伞次数：' . $operation->borrowUmbrellasCount());
        $this->info('雨伞归还率：' . $operation->stillPercent() . '%');
        $this->info('使用人数：' . $operation->umbrellaUsedUsers());
        $this->info('注册人数：' . $operation->umbrellaRegisterUsers());
        $this->info('总计使用过的雨伞数量：' . $operation->usedUmbrellas());
        $this->info('仍在使用的雨伞数量：' . $operation->NotStillUmbrellas());
        $this->info('雨伞丢失率：' . $operation->lostPercent() . '%');
        $this->info('每把雨伞的平均使用次数：' . $operation->umbrellaUseTime());
        $this->info('黑名单人数：' . $operation->blacklists());
        $this->info('运营时间：' . $operation->operationDates() . '天');
    }

    /**
     * 打赏金额统计
     */
    public function donateData()
    {
        $deals = WechatDeal::where('description', '公益爱心伞用户打赏')->where('result_code', 'PAID')->where('id', '>=', 311)->get();
        $money = 0;
        foreach ($deals as $deal) {
            $money += ($deal->cash_fee / 100);
        }

        $this->info('雨伞累计打赏金额：' . $money);
    }

    public function stationData()
    {
        $stations = StationRepository::self()->closeCache()->getStationsWithNotLendUmbrella();
        $headers = ['站点名称', '现存登记数量'];
        $tables = [];
        foreach ($stations as $station) {
            $tables[] = [$station->name, $station->umbrellas_count];
        }
        $this->table($headers, $tables);
    }

    public function data()
    {
        $umbrella = UmbrellaHistory::select('borrow_station', DB::raw('count(borrow_station) as borrow_count'))->whereNotNull('borrow_station')->whereDate('borrow_at', '>=', '2018-06-19')->groupBy('borrow_station')->get()->toArray();
        $headers = ['借出站点', '借出数量'];
        $this->table($headers, $umbrella);
    }

    public function checkUserStatus()
    {
        $users = UserUmbrella::where('status', 2)->get();
        foreach ($users as $user) {
            $borrow_at = Carbon::parse($user->borrow_at);
            if (!is_null($user->still_at)) {
                $still_at = Carbon::parse($user->still_at);
                if ($still_at->gt($borrow_at)) {
                    $user->status = 1;
                    $user->save();
                }
            }
        }
    }
}
