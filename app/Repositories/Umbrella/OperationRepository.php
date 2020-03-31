<?
namespace App\Repositories\Umbrella;

use App\Library\Traits\SelfClass;
use App\Models\Blacklist;
use App\Models\Umbrella;
use App\Models\UmbrellaHistory;
use App\Models\UmbrellaStation;
use App\Models\UserUmbrella;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class OperationRepository
{
    const STILL_STATUS = 1;
    const NOT_STILL_STATUS = 0;

    const START_DATETIME = '2017-09-01';

    use SelfClass;

    private $disable_station = ['商家1', '深大科技楼', '深大文科楼'];

    /**
     * 已经归还到各站点的雨伞情况
     *
     * @param array $select
     * @return array
     */
    public function stationAlreadyStillUmbrella(array $select = ['*'])
    {
        return UmbrellaStation::whereNotIn('name', $this->disable_station)->get($select)->toArray();
    }

    /**
     * 站点目前归还的雨伞总数
     *
     * @return mixed
     */
    public function stationStillUmbrellaCount()
    {
        return UmbrellaStation::sum('amount');
    }

    /**
     * 站点二次使用后累计借出的雨伞情况
     *
     * @return array
     */
    public function borrowUmbrellas()
    {
        return UmbrellaHistory::select(DB::raw('count(*) as total_amount, borrow_station'))->whereNotIn('borrow_station', $this->disable_station)->groupBy(['borrow_station'])->get()->toArray();
    }

    /**
     * 总计借出的雨伞次数
     *
     * @return int
     */
    public function borrowUmbrellasCount()
    {
        return (UmbrellaHistory::whereNotIn('borrow_station', $this->disable_station)->count()) + (UmbrellaHistory::whereNull('borrow_station')->count());
    }

    /**
     * 站点二次使用后累计借出的雨伞次数
     *
     * @return int
     */
    public function borrowUmbrellasCountNotNull()
    {
//        return UmbrellaHistory::whereNotIn('borrow_station', $this->disable_station)->whereNotNull('borrow_station')->count();
        return UmbrellaHistory::whereNotNull('borrow_station')->count();
    }

    /**
     * 各站点归还的雨伞的情况
     *
     * @return array
     */
    public function stillUmbrellas()
    {
        return UmbrellaHistory::select(DB::raw('count(*) as total_amount, still_station'))->where('status', self::STILL_STATUS)->whereNotIn('still_station', $this->disable_station)->groupBy(['still_station'])->get()->toArray();
    }

    /**
     * 总体归还的雨伞次数
     *
     * @return int
     */
    public function stillUmbrellasCount()
    {
//        return UmbrellaHistory::whereNotIn('still_station', $this->disable_station)->whereNotNull('still_at')->count();
        return UmbrellaHistory::whereNotNull('still_at')->count() - UmbrellaHistory::whereIn('still_station', $this->disable_station)->count();
    }

    /**
     * 公益爱心伞的注册用户量
     *
     * @return int
     */
    public function umbrellaRegisterUsers()
    {
        return UserUmbrella::count();
    }

    /**
     * 公益爱心伞的使用用户量
     *
     * @return int
     */
    public function umbrellaUsedUsers()
    {
        return UserUmbrella::where('borrow_at', '<>', null)->count();
    }

    /**
     * 已经被使用的雨伞数量
     *
     * @return int
     */
    public function usedUmbrellas()
    {
        return Umbrella::where('bind_at', '<>', null)->count();
    }

    /**
     * 黑名单的人数
     *
     * @return int
     */
    public function blacklists()
    {
        return Blacklist::where('type', 'umbrella')->count();
    }

    /**
     * 运营时间
     *
     * @return int
     */
    public function operationDates()
    {
        $now = Carbon::now();
        $start = Carbon::parse(self::START_DATETIME);

        return  $now->diffInDays($start);
    }

    /**
     * 雨伞归还率（次），单位%
     *
     * @return float|int
     */
    public function stillPercent()
    {
        return ($this->stillUmbrellasCount() / $this->borrowUmbrellasCount()) * 100;
    }

    /**
     * 仍未归还的雨伞数量
     *
     * @return int|mixed
     */
    public function NotStillUmbrellas()
    {
        return Umbrella::where('user_id', '>', 0)->count();
    }

    /**
     * 雨伞丢失率（把），单位%
     *
     * @return float|int
     */
    public function lostPercent()
    {
        return ($this->NotStillUmbrellas() / $this->usedUmbrellas()) * 100;
    }

    /**
     * 每把伞平均的使用次数
     *
     * @return float|int
     */
    public function umbrellaUseTime()
    {
        return $this->borrowUmbrellasCount() / $this->usedUmbrellas();
    }
}