<?php
/**
 * Created by PhpStorm.
 * User: carnegie
 * Date: 10/05/2017
 * Time: 7:23 PM
 */

namespace App\Http\Controllers\GetUp;


use App\Http\Controllers\Controller;
use App\Models\GetUp;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class LeaderboardController extends Controller
{
    public function getGetUpRankList($limit = 10, $start = 0)
    {
        $openId = session('wechat.oauth_user')->getId();
        $people = DB::table('get_up')
            ->where('open_id',$openId)
            ->first();
        $startDatetime = Carbon::createFromTime(5, 0, 0)->toDateTimeString();
        $lastGetUpDatetime = empty($people->last_get_up_datetime) ? false : $people->last_get_up_datetime;
        $startTimestamp = strtotime($startDatetime);
        $lastGetUpTimestamp = strtotime($lastGetUpDatetime);
        if ($people) {
            if ($lastGetUpDatetime) {
                if ($lastGetUpTimestamp < $startTimestamp) {
                    $rank = -1;
                } else {
                    $earlyCount = DB::table('get_up')
                        ->where([
                            ['last_get_up_datetime','<',$people->last_get_up_datetime],
                            ['last_get_up_datetime','>',$startDatetime]
                        ])->count();
                    $rank = $earlyCount + 1;
                }
            } else {
                $rank = -1;
            }
        } else {
            $rank = -1;
        }


//        $list = DB::query("SELECT a.open_id,a.last_get_up_datetime,b.nickname,b.headimgurl FROM get_up AS a,user_info AS b WHERE a.open_id = b.open_id AND a.last_get_up_datetime > '" . $startDatetime . "' ORDER BY a.last_get_up_datetime LIMIT " . $start . "," . $limit);
        $get_up = GetUp::where('last_get_up_datetime', '>=', $startDatetime)->orderBy('last_get_up_datetime', 'asc')->with('user.detail')->take(10)->get();
        return response()->json(
            [
                'error_code' => 0,
                'error_msg' => 'ok!',
                'data' => [
                    'rank' => $rank,
                    'list' => $get_up
                ]
            ]
        ) ;

    }

    public function getDaySumRankList($limit = 10, $start = 0)
    {
        $openId = session('wechat.oauth_user')->getId();
        $people = DB::table('get_up')
            ->where('open_id',$openId)
            ->first();
        if ($people) {
            $wellCount = DB::table('get_up')
                ->where('day_sum','>',$people->day_sum)
                ->count();
            $rank = $wellCount + 1;
        } else {
            $rank = -1;
        }


//        $list = DB::query("SELECT a.open_id,a.day_sum,b.nickname,b.headimgurl FROM get_up AS a,user_info AS b WHERE a.open_id = b.open_id ORDER BY a.day_sum DESC,a.day_duration DESC,a.last_get_up_datetime LIMIT " . $start . "," . $limit);
        $get_up = GetUp::orderBy('day_sum', 'desc')->orderBy('day_duration', 'desc')->orderBy('last_get_up_datetime')->with('user.detail')->take(10)->get();
        return response()->json([
            'error_code' => 0,
            'error_msg' => 'ok!',
            'data' => [
                'rank' => $rank,
                'list' => $get_up
            ]
        ]);
    }

    public function getDayTotalList()
    {
        $openid = session('wechat.oauth_user')->getId();
        $people = DB::table('get_up')
            ->where('open_id',$openid)
            ->first();
        if ($people) {
            $wellCount = DB::table('get_up')
                ->where('day_total','>',$people->day_total)
                ->count();
            $rank = $wellCount + 1;
        } else {
            $rank = -1;
        }

        $get_up = GetUp::orderBy('day_total', 'desc')->orderBy('day_sum', 'desc')->orderBy('day_duration', 'desc')->orderBy('last_get_up_datetime')->with('user.detail')->take(10)->get();
        return response()->json([
            'error_code' => 0,
            'error_msg' => 'ok!',
            'data' => [
                'rank' => $rank,
                'list' => $get_up
            ]
        ]);
    }

    public function getUserInfo()
    {
        $user = session('wechat.oauth_user');

        return $this->ajaxReturn(0, '加载成功', [
            'headimgurl' => $user->getAvatar(),
            'nickname'   => $user->getNickname(),
            'openid'     => $user->getId()
        ]);
    }

    public function index()
    {
        return Redirect::to(url('Leaderboard/index.html'));
    }

    public function data()
    {
        $getups = DB::table('get_up_history')->whereDate('get_up_datetime', '>=', Carbon::create(2017,9,1)->toDateTimeString())
            ->whereDate('get_up_datetime', '<', Carbon::create(2017,10,1)->toDateTimeString())
//            ->groupBy('open_id')
            ->get();
        $getups = $getups->groupBy('open_id')->toArray();
        $data = [
            'count' => count($getups),
            'getups' => $getups
        ];
        dd($data);
//        $getups = GetUp::
//            ->with('user.detail')
//            ->get();
//        $getups = GetUp::where('day_sum','>',0)->orderBy('day_sum','desc')->with('user.detail')->get();

        return view('data.getup', compact('getups'));
    }

    public function test()
    {
        return 'test successful!';
    }

    public function showAward()
    {
        $user = session('wechat.oauth_user');

        $getups = GetUp::orderBy('day_duration', 'desc')->orderBy('last_get_up_datetime', 'asc')->take(10)->get();
        $count = 0;
        foreach ($getups as $getup) {
            $count++;
            if ($getup->open_id == $user->getId())
                break;
        }
        $is_normal = false;
        if ($count == 1) {
            $awardUrl = asset('images/getup/first.png');
        } else if ($count == 2) {
            $awardUrl = asset('images/getup/second.png');
        } else if ($count == 3) {
            $awardUrl = asset('images/getup/third.png');
        } else {
            $awardUrl = asset('images/getup/normal.png');
            $is_normal = true;
        }

        return view('wechat.getup.award', compact('awardUrl', 'user', 'is_normal'));

    }

}