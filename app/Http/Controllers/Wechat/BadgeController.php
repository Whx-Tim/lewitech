<?php

namespace App\Http\Controllers\Wechat;

use App\Events\UserCombineAvatar;
use App\Models\School;
use App\Repositories\SchoolBadgeRepository;
use App\Repositories\SchoolRepository;
use App\Repositories\Wechat\UserRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BadgeController extends Controller
{
    /**
     * 首页
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $schools = SchoolRepository::self()->getAllSchoolName();
        SchoolBadgeRepository::self()->viewIncrement();
        $js = UserRepository::self()->getJS();

        return view('wechat.badge.index', compact('schools', 'js'));
    }

    /**
     * 显示排行榜
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function rank()
    {
        $user = Auth::user();
        $user_avatar = 'images/badge/avatar/'. $user->id . '.jpg';
        if (!file_exists(public_path($user_avatar))) {
            return redirect()->route('wechat.badge.index');
        }
        $school = $user->schools[0];
        $user_avatar = $user_avatar . '?' . md5($school->name . time());
        $school_count = SchoolRepository::self()->getSchoolUsers($school);
        $school_rank = SchoolBadgeRepository::self()->closeCache()->getUserSchoolRank($school);
        $school_tops = SchoolBadgeRepository::self()->closeCache()->getSchoolBadgeTop10();
        if ($school_rank < 10) {
            foreach ($school_tops as $key => $school_top) {
                if ($school_top->school->name == $school->name) {
                    $school_rank = $key;
                    break;
                }
            }
        }
        $js = UserRepository::self()->getJS();


        return view('wechat.badge.rank', compact('user_avatar', 'school_rank', 'school_tops', 'school_count', 'school', 'js'));
    }

    /**
     * 显示分享页面
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function share(Request $request)
    {
        $user = Auth::user();
        $user_avatar = 'images/badge/avatar/' . $user->id . SchoolRepository::SHARE_IMAGE_EXT;
        $school_id = $request->get('school_id');
        $share = 'images/badge/share/' . $user->id . SchoolRepository::SHARE_IMAGE_EXT . '?' . md5($school_id . time());
        $js = UserRepository::self()->getJS();

        return view('wechat.badge.share', compact('share', 'js', 'user_avatar'));
    }

    /**
     * 合成各种头像
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function combine(Request $request)
    {
        $user = Auth::user();
        Log::alert(serialize($user->detail));
        $school_name = $request->input('school_name');
        $school = School::where('name', $school_name)->first();
        SchoolBadgeRepository::self()->bindSchoolById($user, $school->id);
        SchoolRepository::self()->combineAvatar($user, $school);
        event(new UserCombineAvatar($user, $school));

        return $this->ajaxReturn(0, '生成成功', ['redirect' => route('wechat.badge.rank')]);
    }

    /**
     * 检查分享页面生成是否完成
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkShareImage(Request $request)
    {
        $user = Auth::user();
        $share = 'images/badge/share/' . $user->id . SchoolRepository::SHARE_IMAGE_EXT;
        if (!file_exists(public_path($share))) {
            return $this->ajaxReturn(1, '您的图片正在生成当中，请稍后重试');
        }
        $school_id = $request->get('school_id');

        return $this->ajaxReturn(0, 'success', ['redirect' => route('wechat.badge.share', ['school_id' => $school_id])]);
    }

    /**
     * 返回强制关注
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function subscribe()
    {
        return view('wechat.badge.subscribe');
    }

    /**
     * 搜索大学
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        if (!$request->has('search')) {
            return $this->ajaxReturn(2, '请输入需要搜索的大学关键词');
        }
        $search = $request->get('search');
        $result_array = SchoolRepository::self()->search($search);
        if (empty($result_array)) {
            return $this->ajaxReturn(1, '没有搜到相关的大学记录，敬请期待后续开放的大学校徽');
        } else {
            return $this->ajaxReturn(0, '搜索成功', ['result' => $result_array]);
        }
    }
}
