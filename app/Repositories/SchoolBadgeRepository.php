<?
namespace App\Repositories;

use App\Library\Traits\CacheSwitch;
use App\Library\Traits\SelfClass;
use App\Models\School;
use App\Models\SchoolUser;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SchoolBadgeRepository
{
    use SelfClass;
    use CacheSwitch;

    const CACHE_SCHOOL_BADGE = 'school_badge_top';
    const CACHE_SCHOOL_BADGE_TOP_3 = 'school_badge_top_3';
    const CACHE_SCHOOL_BADGE_TOP_10 = 'school_badge_top_10';
    const CACHE_SCHOOL_USER_RANK = 'school_user_rank';
    const CACHE_SCHOOL_VIEW = 'school_badge_view';


    /**
     * 绑定学校
     *
     * @param User $user
     * @param School $school
     * @return bool
     */
    public function bindSchool(User $user, School $school)
    {
        $user->schools()->detach();
        $user->schools()->attach($school->id);
//        if (file_exists(public_path('images/badge/share/' . $user->id . '.jpg'))) {
//            unlink(public_path('images/badge/share/' . $user->id . '.jpg'));
//        }

        return true;
    }

    public function viewIncrement()
    {
        Cache::increment(self::CACHE_SCHOOL_VIEW);
    }

    public function getViewCount()
    {
        return Cache::get(self::CACHE_SCHOOL_VIEW);
    }

    /**
     * 通过id绑定学校
     *
     * @param User $user
     * @param $school_id
     * @return bool
     */
    public function bindSchoolById(User $user, $school_id)
    {
        $user->schools()->detach();
        $user->schools()->attach($school_id);
        if (file_exists(public_path('images/badge/share/' . $user->id . '.jpg'))) {
            unlink(public_path('images/badge/share/' . $user->id . '.jpg'));
        }

        return true;
    }

    /**
     * 获取学校排名top
     *
     * @param int $amount
     * @return mixed
     */
    public function getSchoolBadgeTopX($amount = 15)
    {
        $result =  SchoolUser::select(DB::raw('count(*) as user_count'), 'school_id')
            ->groupBy('school_id')
            ->orderBy('user_count', 'desc')
            ->orderBy('school_id', 'desc')
            ->take($amount)
            ->get()
            ->load('school');

        return $result;
    }

    /**
     * 获取学校排名top3
     *
     * @return mixed
     */
    public function getSchoolBadgeTop3()
    {
        if ($this->cache_switch) {
            return Cache::remember(self::CACHE_SCHOOL_BADGE_TOP_3, $this->cache_time, function () {
                return $this->getSchoolBadgeTopX(3);
            });
        }

        return $this->getSchoolBadgeTopX(3);
    }

    /**
     * 获取学校排名top10
     *
     * @return mixed
     */
    public function getSchoolBadgeTop10()
    {
        if ($this->cache_switch) {
            return Cache::remember(self::CACHE_SCHOOL_BADGE_TOP_10, $this->cache_time, function () {
                return $this->getSchoolBadgeTopX(10);
            });
        }

        return $this->getSchoolBadgeTopX(10);
    }

    /**
     * 获取学校排名
     *
     * @param School $school
     * @return int
     */
    public function getUserSchoolRank(School $school)
    {
        if (empty($school->users_count)) {
            $user_count = $school->users()->count();
        } else {
            $user_count = $school->users_count;
        }
        if ($this->cache_switch) {
            $cache_name = self::CACHE_SCHOOL_USER_RANK . '_' . $school->name;
            return Cache::remember($cache_name, $this->cache_time, function () use ($user_count) {
                return $this->getUserSchoolRankOperation($user_count);
            });
        } else {
            return $this->getUserSchoolRankOperation($user_count);
        }
    }

    /**
     * 获取学校排名的数据操作
     *
     * @param $user_count
     * @return int
     */
    public function getUserSchoolRankOperation($user_count)
    {
        $schools =  SchoolUser::select(DB::raw('count(*) as user_count, school_id'))
                         ->groupBy('school_id')
                         ->havingRaw('count(*) > ' . $user_count)
                         ->get();
        return count($schools);
    }
}