<?php
namespace App\Repositories\Wechat;

use App\Library\Traits\CheckRequireData;
use App\Library\Traits\SelfClass;
use App\Models\User;
use EasyWeChat\Foundation\Application;
use Illuminate\Support\Facades\Log;

class UserRepository
{
    use CheckRequireData;
    use SelfClass;

    /**
     * 微信服务
     *
     * @var Application
     */
    private $wechat;

    private $result;

    /**
     * 要求必填的字段
     *
     * @var array
     */
    protected $require = ['openid', 'head_img', 'nickname'];

    /**
     * 可以写入用户微信信息的字段
     *
     * @var array
     */
    protected $detail_fillable = ['head_img', 'nickname', 'sex', 'city', 'country', 'language', 'subscribe', 'subscribe_time', 'phone', 'email', 'address', 'name'];

    protected $wechat_avatar_level_limit = [0, 46, 64, 96, 132];

    /**
     * UserRepository constructor.
     */
    public function __construct()
    {
        $this->wechat = app('wechat');
    }

    /**
     * 获取用户的在微信的详情信息
     *
     * @param $openid
     * @return array
     */
    public function getWechatInfo($openid)
    {
        $userService = $this->wechat->user;
        $result = $userService->get($openid);
        if ($result->subscribe) {
            $result_array = [
                'openid'          => $openid,
                'head_img'        => $result->headimgurl,
                'nickname'        => $result->nickname,
                'sex'             => $result->sex,
                'city'            => $result->city,
                'country'         => $result->country,
                'language'        => $result->language,
                'subscribe'       => $result->subscribe,
                'subscribe_time'  => $result->subscribe_time,
            ];
            $this->result = $result_array;
        }

        return $result;
    }

    public function getWechatInfoBySession()
    {
        $wechatUser = session('wechat.oauth_user');
        if (is_array($wechatUser)) {
            $result = $wechatUser;
        } else {
            $result = $wechatUser->getOriginal();
        }
//        Log::alert(serialize($result));
        $this->result = [
            'openid'   => $result['openid'],
            'head_img' => $result['headimgurl'],
            'nickname' => $result['nickname'],
            'sex'      => $result['sex'],
            'city'     => $result['city'],
            'country'  => $result['country']
        ];

        return $this->result;
    }

    /**
     * 更新或是新建一个微信用户信息
     *
     * @param array $data
     * @throws \Exception
     */
    public function updateOrCreate(array $data,User $user = null)
    {
        $this->checkRequire($data);
        if (empty($user)) {
            $user = User::where('openid', $data['openid'])->first();
        }
        if ($user) {
            $this->update($user, $data);
        } else {
            $user = $this->create($data);
        }

        return $user;
    }

    /**
     * 获取微信服务器的更新与创建用户
     *
     * @return int|mixed
     * @throws \Exception
     */
    public function updateOrCreateFromWechat()
    {
        $openid = ($this->getWechatInfoBySession())['openid'];
        $result = $this->getWechatInfo($openid);
        if ($result->subscribe) {
            $data = $this->result;
            return $this->updateOrCreate($data);
        } else {
            $data = $this->getWechatInfoBySession();
            $data['subscribe'] = $result->subscribe;

            return $this->updateOrCreate($data);
        }
    }

    /**
     * 从缓存中获取用户信息并创建，需要应用微信认证中间件后使用
     *
     * @return \Illuminate\Database\Eloquent\Model
     * @throws \Exception
     */
    public function createFromSession()
    {
        $data = $this->getWechatInfoBySession();

        $user = User::where('openid', $data['openid']);
        if (!$user) {
            return $this->create($data);
        } else {
            return $user;
        }
    }

    /**
     * 从缓存中获取用户信息并创建或更新，需要应用微信认证中间件后使用
     *
     * @return int|mixed
     * @throws \Exception
     */
    public function createOrUpdateFromSession(User $user = null)
    {
        $data = $this->getWechatInfoBySession();

        return $this->updateOrCreate($data, $user);
    }

    /**
     * 创建一个微信用户信息
     *
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Model
     * @throws \Exception
     */
    public function create(array $data)
    {
        $this->checkRequire($data);

        $user = User::create(['openid' => $data['openid']]);
        $detail_data = $this->filterDetailFillable($data);
        $user->detail()->create($detail_data);

        return $user;
    }

    /**
     * 更新一个微信用户信息
     *
     * @param User $user
     * @param array $data
     */
    public function update(User $user, array $data)
    {
        $data = $this->filterDetailFillable($data);

        if ($user->detail->exists) {
            $detail = $user->detail;
            foreach ($data as $key => $datum) {
                $detail->{$key} = $datum;
            }
            if (!empty($detail->getDirty())) {
                $detail->save();
            }
        } else {
            $user->detail()->create($data);
        }

        return $user;
    }

    /**
     * 过滤微信用户可以写入的字段
     *
     * @param array $data
     * @return array
     */
    private function filterDetailFillable(array $data)
    {
        $fillable = [];
        foreach ($this->detail_fillable as $value) {
            if (isset($data[$value])) {
                $fillable[$value] = $data[$value];
            }
        }

        return $fillable;
    }

    /**
     * 改变微信头像显示等级
     *
     * @param $avatar
     * @param int $level
     * @throws \Exception
     * @return string
     */
    public function changeWechatAvatarLevel($avatar, $level = 132)
    {
        $this->checkLevelLimit($level);
        $avatar_array = explode('/', $avatar);
        $avatar_array[count($avatar_array) - 1] = $level;

        return implode('/', $avatar_array);
    }

    /**
     * 检查头像级别是否在规定数组内
     *
     * @param $level
     * @return bool
     * @throws \Exception
     */
    private function checkLevelLimit($level)
    {
        if (in_array($level, $this->wechat_avatar_level_limit)) {
            return true;
        } else {
            throw new \Exception('微信头像不提供该级别的头像显示');
        }
    }

    /**
     * 获得微信的jssdk
     *
     * @return \EasyWeChat\Js\Js|mixed
     */
    public function getJS()
    {
        return $this->wechat->js;
    }
}