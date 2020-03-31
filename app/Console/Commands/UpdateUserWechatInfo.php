<?php

namespace App\Console\Commands;

use App\Events\TriggerWarning;
use App\Models\User;
use Illuminate\Console\Command;

class UpdateUserWechatInfo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'update user wechat info';

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
        $app = app('wechat');
        $userService = $app->user;
        $userList = $userService->lists();

        $bar = $this->output->createProgressBar(count($userList['data']['openid']));
        foreach ($userList['data']['openid'] as $openid) {

            $this->updateUserInfo($userService, $openid);

            $bar->advance();
        }
        event(new TriggerWarning('更新用户信息成功'));
        $bar->finish();
        $this->info('update user info successful!');
    }

    protected function updateUserInfo($userService, $openid)
    {
        $userInfo = $userService->get($openid);
        $user = User::where('openid', $openid)->first();
        if (!is_null($user)) {
            $user->detail()->update([
                'head_img' => $userInfo->headimgurl,
                'nickname' => $userInfo->nickname,
                'sex'      => $userInfo->sex,
                'city'     => $userInfo->city,
                'country'  => $userInfo->country,
                'language' => $userInfo->language,
                'subscribe'=> $userInfo->subscribe,
                'subscribe_time' => $userInfo->subscribe_time,
            ]);
        } else {
            $user = User::create(['openid' => $openid]);

            $user->detail()->create([
                'head_img' => $userInfo->headimgurl,
                'nickname' => $userInfo->nickname,
                'sex'      => $userInfo->sex,
                'city'     => $userInfo->city,
                'country'  => $userInfo->country,
                'language' => $userInfo->language,
                'subscribe'=> $userInfo->subscribe,
                'subscribe_time' => $userInfo->subscribe_time,
            ]);
        }
    }
}
