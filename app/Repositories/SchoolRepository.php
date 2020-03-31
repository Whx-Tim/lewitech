<?php
namespace App\Repositories;

use App\Library\Traits\CheckRequireData;
use App\Library\Traits\SelfClass;
use App\Models\School;
use App\Models\SchoolUser;
use App\Models\User;
use App\Repositories\Wechat\UserRepository;
use GuzzleHttp\Client;
use function GuzzleHttp\Psr7\str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;

class SchoolRepository
{
    use CheckRequireData;
    use SelfClass;

    const SCHOOLS_CACHE_NAME = 'all_schools';
    const SCHOOLS_NAME_CACHE_NAME = 'all_schools_name';
    const SHARE_IMAGE_EXT = '.jpg';
    const AVATAR_IMAGE_EXT = '.jpg';
    const AVATAR_SAVE_LEVEL = 50;
    const SHARE_SAVE_LEVEL = 50;

    private $cache_name = [
        self::SCHOOLS_CACHE_NAME, self::SCHOOLS_NAME_CACHE_NAME
    ];

    /**
     * @var School $school
     */
    private $school;

    /**
     * 缓存时间，默认20分钟
     *
     * @var int
     */
    private $cache_time = 20;

    private $cache_switch = true;

    protected $require = ['name', 'local_url'];

    /**
     * SchoolRepository constructor.
     */
    public function __construct()
    {
        $this->school = new School();
    }

    /**
     * 设置缓存时间
     *
     * @param $minutes
     * @return $this
     */
    public function setCacheTime($minutes)
    {
        $this->cache_time = $minutes;

        return $this;
    }

    /**
     * 获取缓存时间
     *
     * @return int
     */
    public function getCacheTime()
    {
        return $this->cache_time;
    }

    /**
     * 打开缓存
     *
     * @return $this
     */
    public function openCache()
    {
        $this->cache_time = true;

        return $this;
    }

    /**
     * 关闭缓存
     *
     * @return $this
     */
    public function closeCache()
    {
        $this->cache_time = false;

        return $this;
    }

    /**
     * 创建一个学校
     *
     * @param array $data
     * @return mixed
     * @throws \Exception
     */
    public function create(array $data)
    {
        $this->checkRequire($data);
        if ($this->isExist($data)) {
            new \Exception($data['name'] . '学校已经存在');
        } else {
            return $this->school->create($data);
        }
    }

    /**
     * 判断学校是否存在
     *
     * @param array $data
     * @return bool
     */
    private function isExist(array $data)
    {
        $school = School::where('name', '=',  $data['name'])->first();

        return !is_null($school);
    }

    /**
     * 自动识别
     *
     * @return bool
     * @throws \Exception
     */
    public function autoCreateSchoolFromBadge()
    {
//        $dir_array = [
//            'anhui', 'beijing', 'chongqing', 'fujian', 'gansu', 'guangdong',
//            'guangxi', 'guizhou', 'hainan', 'hebei', 'heilongjiang', 'huazhong',
//            'jiangsu', 'jiangxi', 'jilin', 'liaoning', 'neimenggu', 'ningxia', 'qinghai',
//            'shandong', 'shanghai', 'shanxi', 'shangxi', 'sichuan', 'tianjin', 'xinjiang',
//            'xizang', 'yunnan', 'zhejiang'
//        ];
        $dir_array = ['guangdong'];
        foreach ($dir_array as $dir) {
            $dir_path = public_path('images/badge/'. $dir);
            $schools = $this->scanDirFileName($dir_path);
            foreach ($schools as $school) {
                $this->create([
                    'name'       => $school,
                    'badge_url'  => 'http://wj.qn.h-hy.com/images/lewitech/badge/all/source/' . $school . '.jpg',
                    'local_url'  => '/images/badge/'. $dir .'/' . $school . '.png',
                    'remote_url' => 'http://wj.qn.h-hy.com/images/lewitech/badge/all/' . $school . '.png',
                ]);
            }
        }


        return true;
    }

    /**
     * 文件重命名
     *
     * @throws \Exception
     */
    public function renameSchool()
    {
        $path = 'images/badge/zhejiang';
        $schools = $this->scanDirFileName(public_path($path));
        foreach ($schools as $school) {
            $newname = str_replace('校徽', '', $school);
            $newname = str_replace('0', '', $newname);
            $newname = str_replace('_output', '', $newname);
            $oldname = public_path($path . '/' . $school . '.png');
            $newname = public_path($path . '/' . $newname . '.png');
            rename($oldname, $newname);
        }
    }

    /**
     * 扫描目录下的文件名称
     *
     * @param $dir_path
     * @return array
     * @throws \Exception
     */
    private function scanDirFileName($dir_path)
    {
        if (is_dir($dir_path)) {
            $file_array = scandir($dir_path);
            $key = array_search('.', $file_array);
            if ($key !== false) {
                unset($file_array[$key]);
            }
            $key = array_search('..', $file_array);
            if ($key !== false) {
                unset($file_array[$key]);
            }

            foreach ($file_array as &$item) {
                $item_array = explode('.', $item);
                $item_length = count($item_array);
                if ($item_length != 2) {
                    unset($item_array[$item_length - 1]);
                    $item = '';
                    foreach ($item_array as $value) {
                        $item .= $value;
                    }
                } else {
                    $item = $item_array[0];
                }
            }

            return array_values($file_array);
        } else {
            throw new \Exception('该目录不存在');
        }

    }

    /**
     * 获得所有学校的名字、id
     *
     * @return mixed
     */
    public function getAllSchoolName()
    {
        if ($this->cache_switch) {
            return Cache::remember(self::SCHOOLS_NAME_CACHE_NAME, 20, function () {
                return School::all(['id', 'name']);
            });
        } else {
            return School::all(['id', 'name']);
        }
    }

    /**
     * 搜索学校
     *
     * @param $needle
     * @return array
     */
    public function search($needle)
    {
        $schools = $this->getAllSchoolName();

        return $this->arraySearch($needle, $schools);
    }

    /**
     * 数组搜索法
     *
     * @param $needle
     * @param $array
     * @return array
     */
    private function arraySearch($needle, $array)
    {
        $result = [];
        foreach ($array as $item) {
            if (str_contains($item->name, $needle)) {
                $result []= $item->name;
            }
        }

        return $result;
    }

    /**
     * 清除所有学校名字的缓存
     */
    public function clearAllSchoolNameCache()
    {
        Cache::forget(self::SCHOOLS_NAME_CACHE_NAME);
    }

    /**
     * 清除所有学校的缓存
     */
    public function clearAllSchoolCache()
    {
        Cache::forget(self::SCHOOLS_CACHE_NAME);
    }

    /**
     * 清除所有缓存
     */
    public function clearAllCache()
    {
        foreach ($this->cache_name as $cache_name) {
            Cache::forget($cache_name);
        }
    }

    /**
     * 获得所有学校的对象
     *
     * @return \Illuminate\Database\Eloquent\Collection|mixed|static[]
     */
    public function getAllSchool()
    {
        if ($this->cache_switch) {
            return Cache::remember(self::SCHOOLS_CACHE_NAME, 20, function () {
                return School::all(['id', 'name', 'badge_url', 'local_url', 'remote_url']);
            });
        } else {
            return School::all(['id', 'name', 'badge_url', 'local_url', 'remote_url']);
        }
    }

    /**
     * 合并头像
     *
     * @param User $user
     * @throws \Exception
     * @param School $school
     * @return string
     */
    public function combineAvatar(User $user, School $school)
    {
        $client = new Client();
        $user_avatar = $user->detail->head_img;
        $avatar = $client->get(UserRepository::self()->changeWechatAvatarLevel($user_avatar, 0));
        $head_img = $avatar->getBody()->getContents();
        $badge_img = file_get_contents(public_path($school->local_url));
        $save_uri = 'images/badge/avatar/' . $user->id . self::AVATAR_IMAGE_EXT;
        Image::make($head_img)->widen(800)->insert($badge_img, 'top-left', 0, 0)->save(public_path($save_uri), self::AVATAR_SAVE_LEVEL);

        return $save_uri;
    }

    /**
     * 组合分享页面图片
     *
     * @param User $user
     * @param bool $cover
     * @return mixed
     * @throws \Exception
     */
    public function combineShare(User $user, School $school, $cover = false)
    {
        $save_path = 'images/badge/share/' . $user->id . self::SHARE_IMAGE_EXT;
        if ($cover) {
            return $this->realCombineShare($user, $school, $save_path);
        } else {
            if (!file_exists(public_path($save_path))) {
                return $this->realCombineShare($user, $school, $save_path);
            }

            return $save_path;
        }
    }

    /**
     * 实际组合分享页面图片
     *
     * @param User $user
     * @param $save_path
     * @return mixed
     * @throws \Exception
     */
    private function realCombineShare(User $user, School $school, $save_path)
    {
        $bg = file_get_contents(public_path('images/badge/share-bg.jpeg'));
        if (file_exists(public_path('images/badge/avatar/'. $user->id . '.jpg'))) {
            $avatar = file_get_contents(public_path('images/badge/avatar/'. $user->id . '.jpg'));
            $avatar = Image::make($avatar)->resize(300, 300);
            Image::canvas(750, 1197)->insert($bg, 'top-left', 0, 0)
                                    ->insert($avatar, 'top-left', 30, 80)
                                    ->text($school->name . '人,', 700, 292, function ($text) {
                                        $text->file(public_path('fonts/msyh.ttf'));
                                        $text->size(28);
                                        $text->align('right');
                                        $text->color('#0b7ec4');
                                    })
                                    ->save(public_path($save_path), self::SHARE_SAVE_LEVEL);
            return $save_path;
        } else {
            throw new \Exception('还未选择您的头像信息');
        }
    }

    /**
     * 获取学校当前支持的人数
     *
     * @param School $school
     * @return mixed
     */
    public function getSchoolUsers(School $school)
    {
        return SchoolUser::where('school_id', $school->id)->count();
    }

}