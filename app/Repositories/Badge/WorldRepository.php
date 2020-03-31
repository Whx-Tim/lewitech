<?php
namespace App\Repositories\Badge;

use App\Library\Traits\CacheSwitch;
use App\Library\Traits\SelfClass;
use App\Models\Badge\Badge;
use App\Models\Badge\BadgeUser;
use App\Models\User;
use App\Repositories\Wechat\UserRepository;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;

class WorldRepository
{
    use SelfClass;
    use CacheSwitch;

    const TYPE = 'world';
    const SHARE_IMAGE_EXT = '.jpg';
    const AVATAR_IMAGE_EXT = '.jpg';
    const USER_IMAGE_EXT = '.jpg';
    const AVATAR_SAVE_LEVEL = 50;
    const SHARE_SAVE_LEVEL = 30;

    const CACHE_ALL_BADGE_NAME = 'all_badge_world_name';
    const CACHE_ALL_BADGE_ID = 'all_badge_world_id';
    const CACHE_ALL_BADGE = 'all_badge_world';

    const STATUS_ENABLE = 1;

    private $share_type = ['default', 'diy', 'team'];

    private $model;

    public function __construct()
    {
        $this->model = new Badge();
    }

    public function getAllBadgeName()
    {
        if ($this->cache_switch) {
            Cache::forget(self::CACHE_ALL_BADGE_NAME);
        }

        return Cache::remember(self::CACHE_ALL_BADGE_NAME, $this->cache_time, function () {
            return $this->model->where('type', self::TYPE)->where('status', self::STATUS_ENABLE)->get(['name']);
        });
    }

    public function getAllBadge()
    {
        if ($this->cache_switch) {
            Cache::forget(self::CACHE_ALL_BADGE);
        }

        return Cache::remember(self::CACHE_ALL_BADGE, $this->cache_time, function () {
            return $this->model->where('type', self::TYPE)->where('status', self::STATUS_ENABLE)->get();
        });
    }

    public function getAllBadgeId()
    {
        if ($this->cache_switch) {
            Cache::forget(self::CACHE_ALL_BADGE_ID);
        }

        return Cache::remember(self::CACHE_ALL_BADGE_ID, $this->cache_time, function () {
            return $this->model->where('type', self::TYPE)->where('status', self::STATUS_ENABLE)->get(['id']);
        });
    }

    public function search($needle)
    {
        $badges = $this->getAllBadgeName();

        return arraySearch($needle, $badges);
    }

    /**
     * 合并头像
     *
     * @param User $user
     * @param Badge $badge
     * @return string
     * @throws \Exception
     */
    public function combineAvatar(User $user, Badge $badge)
    {
        $start_time = microtime(true);
        $client = new Client();
        $user_avatar = $user->detail->head_img;
        $head_filename = md5($user_avatar);
        $head_path = public_path('images/badge/user/' . $head_filename . self::USER_IMAGE_EXT);
        if (file_exists($head_path)) {
            $head_img = $head_path;
        } else {
            $avatar = $client->get(UserRepository::self()->changeWechatAvatarLevel($user_avatar, 0));
            $head_img = $avatar->getBody()->getContents();
            file_put_contents($head_path, $head_img);
        }
        $badge_img = public_path($badge->local_url);
        $save_uri = 'images/badge/'. self::TYPE .'/avatar/' . $user->id . self::AVATAR_IMAGE_EXT;
        $head_img = Image::make($head_img)->resize(950, 950);
        Image::canvas(1000,1000)->insert($head_img, 'top-left', 25, 0)->insert($badge_img, 'top-left', 0, 0)->save(public_path($save_uri), self::AVATAR_SAVE_LEVEL);
        $end_time = microtime(true);
        Log::alert('头像生成耗时：' . bcsub($end_time, $start_time, 10));

        return $this->uplodaImageToWechat(public_path($save_uri));
    }

    private function uplodaImageToWechat($path)
    {
        $wechat = app('wechat');
        $temporary = $wechat->material_temporary;
        $result = $temporary->uploadImage($path);

        return $result->media_id;
    }

    public function saveUserAvatar(User $user)
    {
        $client = new Client();
        $user_avatar = $user->detail->head_img;
        $head_filename = md5($user_avatar);
        $head_path = public_path('images/badge/user/' . $head_filename . self::USER_IMAGE_EXT);
        if (file_exists($head_path)) {
            $head_img = $head_path;
        } else {
            $avatar = $client->get(UserRepository::self()->changeWechatAvatarLevel($user_avatar, 0));
            $head_img = $avatar->getBody()->getContents();
            file_put_contents($head_path, $head_img);
        }

        return $head_path;
    }

    public function combineShare(User $user, Badge $badge, $type)
    {
        $start_time = microtime(true);
        if (!in_array($type, $this->share_type)) {
            throw new \Exception('所选类型未注册');
        }
        $avatar = public_path('images/badge/user/' . md5($user->detail->head_img) . self::USER_IMAGE_EXT);
        if (!file_exists($avatar)) {
            $avatar = $this->saveUserAvatar($user);
        }
        $avatar = Image::make($avatar)->resize(165, 165);
        $save_path = 'images/badge/'. self::TYPE .'/share/' . $user->id . self::SHARE_IMAGE_EXT;
        $function = $type . 'CombineShare';
        $badge_user = BadgeUserRepository::self()->getBadgeUser($user);
        $data = json_decode($badge_user->data);
        $end_time = microtime(true);
        Log::alert('分享生成耗时：' . bcsub($end_time, $start_time, 10));

        return $this->{$function}($avatar, $badge, $save_path, $data);
    }

    public function defaultCombineShare($avatar, Badge $badge, $save_path, $data)
    {
        $bg = public_path('images/badge/' . self::TYPE . '/default/' . $badge->name . '.png');
        Image::canvas(750, 1196)->insert($avatar, 'top-left', 545, 22)
                                ->insert($bg, 'top-left', 0, 0)
                                ->text($data->content_1, 50, 200, function ($font) {
                                    $font->file(public_path('fonts/MSYHBD.TTF'));
                                    $font->size(80);
                                    $font->align('left');
                                    $font->color('#ffffff');
                                })->text($data->content_2, 50, 300, function ($font) {
                                    $font->file(public_path('fonts/MSYHBD.TTF'));
                                    $font->size(80);
                                    $font->align('left');
                                    $font->color('#ffffff');
                                })->text($data->content_3, 50, 380, function ($font) {
                                    $font->file(public_path('fonts/MSYHBD.TTF'));
                                    $font->size(60);
                                    $font->align('left');
                                    $font->color('#ffffff');
                                })
                                ->save(public_path($save_path), self::SHARE_SAVE_LEVEL);

        return $save_path;
    }

    public function diyCombineShare($avatar, Badge $badge, $save_path, $data)
    {
        $bg = public_path('images/badge/' . self::TYPE . '/diy/' . $badge->name . '.png');
        $clip_image = public_path($data->clip_image);
        $clip_image = Image::make($clip_image)->resize(200,200);
        Image::canvas(750, 1196)->insert($avatar, 'top-left', 545, 22)
                                ->insert($clip_image, 'top-left', 428, 478)
                                ->insert($bg, 'top-left', 0, 0)
                                ->text($data->content_1, 50, 200, function ($font) {
                                    $font->file(public_path('fonts/MSYHBD.TTF'));
                                    $font->size(80);
                                    $font->align('left');
                                    $font->color('#ffffff');
                                })->text($data->content_2, 50, 300, function ($font) {
                                    $font->file(public_path('fonts/MSYHBD.TTF'));
                                    $font->size(80);
                                    $font->align('left');
                                    $font->color('#ffffff');
                                })->text($data->content_3, 50, 380, function ($font) {
                                    $font->file(public_path('fonts/MSYHBD.TTF'));
                                    $font->size(60);
                                    $font->align('left');
                                    $font->color('#ffffff');
                                })->save(public_path($save_path), self::SHARE_SAVE_LEVEL);

        return $save_path;
    }

    public function teamCombineShare($avatar, Badge $badge, $save_path, $data = [])
    {
        $bg = public_path('images/badge/' . self::TYPE . '/team/' . $badge->name . '.png');
        Image::canvas(750, 1196)->insert($avatar, 'top-left', 545, 22)
             ->insert($bg, 'top-left', 0, 0)
             ->save(public_path($save_path), self::SHARE_SAVE_LEVEL);

        return $save_path;
    }

}