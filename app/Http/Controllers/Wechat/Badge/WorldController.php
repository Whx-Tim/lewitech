<?php

namespace App\Http\Controllers\Wechat\Badge;

use App\Models\Badge\Badge;
use App\Repositories\Badge\BadgeUserRepository;
use App\Repositories\Badge\WorldRepository;
use App\Repositories\Wechat\UserRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class WorldController extends Controller
{
    private $share_link = 'http://wx.lewitech.cn/wechat/badge/world';

    public function index()
    {
        $badges = WorldRepository::self()->closeCache()->getAllBadge();
        $js = UserRepository::self()->getJS();
        $link = $this->share_link;

        return view('wechat.badge.world.index', compact('badges', 'js', 'link'));
    }

    public function avatar(Request $request)
    {
        if (!$request->has('type')) {
            abort(404);
        }
        $type = $request->get('type');
        $user = Auth::user();
        $badge = BadgeUserRepository::self()->getBadgeUser($user);
        $user_avatar = 'images/badge/world/avatar/' . $user->id . WorldRepository::AVATAR_IMAGE_EXT . '?' . md5(time());
        $js = UserRepository::self()->getJS();
        $link = $this->share_link;
        $media = $request->get('media');

        return view('wechat.badge.world.avatar', compact('user_avatar', 'js', 'badge', 'type', 'link', 'media'));
    }

    public function share()
    {
        $user = Auth::user();
        $share = 'images/badge/world/share/' . $user->id . WorldRepository::SHARE_IMAGE_EXT . '?' . md5(time());
        $js = UserRepository::self()->getJS();
        $link = $this->share_link;

        return view('wechat.badge.world.share', compact('share', 'js', 'link'));
    }

    public function saveShare(Request $request)
    {
        if (!$request->has('type')) {
            return $this->ajaxReturn(1, '参数错误，请刷新后重试');
        }
        if (!$request->has('content_1') || !$request->has('content_2') || !$request->has('content_3')) {
            return $this->ajaxReturn(2, '请输入相应的文案内容');
        }
        $type = $request->input('type');
        if ($type == 'diy') {
            if (!$request->has('clip_image')) {
                return $this->ajaxReturn(3, '请上传您的头像，或是选择其他模式');
            }
        }
        $user = Auth::user();
        $data = [
            'content_1' => $request->input('content_1'),
            'content_2' => $request->input('content_2'),
            'content_3' => $request->input('content_3')
        ];
        if ($request->has('clip_image')) {
            $image = urldecode($request->input('clip_image'));
            $image = str_replace('data:image/jpeg;base64,', '', $image);
            $image = base64_decode($image);
            $clip_image = 'images/badge/world/clip/' . $user->id . '.jpg';
            file_put_contents($clip_image, $image);
            $data['clip_image'] = $clip_image;
        }



        $badge_user = (BadgeUserRepository::self()->getBadgeUser($user));
        $badge_user->data = json_encode($data);
        $badge_user->save();
        $badge = $badge_user->badge;
        WorldRepository::self()->combineShare($user, $badge, $type);

        return $this->ajaxReturn(0, '生成成功', ['redirect' => route('wechat.badge.world.share')]);
    }

    /**
     * 合成头像
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function combine(Request $request)
    {
        if (!$request->has('type')) {
            $this->ajaxReturn(1, '参数错误，请刷新页面后重试');
        }
        $user = Auth::user();
        $badge_id = $request->input('badge_id');
        $badge = Badge::find($badge_id);
        BadgeUserRepository::self()->bindBadge($user, $badge->id);
        $media_id = WorldRepository::self()->combineAvatar($user, $badge);

        return $this->ajaxReturn(0 , '生成成功', ['redirect' => route('wechat.badge.world.avatar', ['type' => $request->get('type'), 'media' => $media_id])]);
    }

    public function subscribe()
    {
        return view('wechat.badge.world.subscribe');
    }
}
