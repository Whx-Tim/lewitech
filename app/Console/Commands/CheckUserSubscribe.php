<?php

namespace App\Console\Commands;

use App\Events\TriggerWarning;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckUserSubscribe extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:subscribe';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'check user subscribe status';

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
        $wechat = app('wechat');
        $userService = $wechat->user;
        $users = User::all();
        $bar = $this->output->createProgressBar(count($users));
        foreach ($users as $user) {
            $this->checkUserSubscribe($user, $userService);

            $bar->advance();
        }
        event(new TriggerWarning('检查用户关注状态成功！'));
        $bar->finish();
    }

    protected function checkUserSubscribe(User $user, $service) {
        try {
            $wechat_user = $service->get($user->openid);
            $user->detail()->update(['subscribe' => $wechat_user['subscribe']]);
        }catch (\Exception $exception) {
            event(new TriggerWarning(Carbon::now()->toDateTimeString().' 检查用户是否关注状态异常，请检查日志查看异常，openid:'. $user->openid));
            Log::info($exception);
        }
    }

}
