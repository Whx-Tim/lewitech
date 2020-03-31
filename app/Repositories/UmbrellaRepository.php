<?php
namespace App\Repositories;

use App\Events\TriggerWarning;
use App\Library\Traits\SelfClass;
use App\Models\Umbrella;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UmbrellaRepository
{
    use SelfClass;

    const STATUS_DEFAULT = 0;
    const STATUS_ENABLE = 1;
    const STATUS_UNABLE = 2;

    private $umbrella;

    public function __construct()
    {
        $this->umbrella = new Umbrella();
    }

    /**
     * @param $openid
     * @return array
     * @throws \Throwable
     */
    public function forceStill($openid)
    {
        try {
            $user = User::where('openid', $openid)->first();
            $now = Carbon::now()->toDateTimeString();
            if ($user->umbrellaInfo->status == 1) {

                $result = ['code' => 2, 'message' => '请扫码雨伞上的二维码进行借伞'];
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

    public function moneyRecovery($id)
    {
        if (empty($id)) {
            throw new \Exception('请输入用户id');
        }
        $user = User::where('id', $id)->first();
        if (!$user) {
            throw new \Exception('找不到该用户');
        }

        $history = $user->umbrellaHistories()->where('status', 0)->first();
        if (!$history) {
            throw new \Exception('找不到该用户的借伞记录');
        }
        $now = Carbon::now()->toDateTimeString();
        DB::beginTransaction();
        $history->update([
            'status' => 1,
            'still_at' => $now
        ]);
        $user->umbrellaInfo()->update(['status' => 1, 'still_at' => $now]);
        $user->umbrella()->delete();
        DB::commit();

        return true;
    }
}