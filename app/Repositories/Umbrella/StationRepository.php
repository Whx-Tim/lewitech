<?
namespace App\Repositories\Umbrella;

use App\Library\Traits\CacheSwitch;
use App\Library\Traits\SelfClass;
use App\Models\UmbrellaStation;
use App\Services\QrcodeService;
use Illuminate\Support\Facades\Cache;

class StationRepository
{
    use SelfClass;
    use CacheSwitch;

    const STATUS_ENABLE = 1;
    const STATUS_UNABLE = 0;

    const CACHE_STATIONS = 'umbrella_station_all';

    private $status_map = [
        self::STATUS_ENABLE => '可用',
        self::STATUS_UNABLE => '不可用'
    ];

    private $model;

    public function __construct()
    {
        $this->model = new UmbrellaStation();
    }

    /**
     * 创建一个公益爱心伞站点
     *
     * @param $name
     * @throws \Exception
     */
    public function createWithQrcode($name)
    {
        $station = UmbrellaStation::create(['name' => $name]);

        QrcodeService::self()->createStationQrcode($station);
    }

    /**
     * 获取站点数据
     *
     * @return mixed
     */
    public function getStations()
    {
        if (!$this->cache_switch) {
            Cache::forget(self::CACHE_STATIONS);
        }

        return Cache::remember(self::CACHE_STATIONS, $this->cache_time, function () {
            return $this->model->where('status', self::STATUS_ENABLE)->withCount('umbrellas')->get();
        });
    }

    public function getStationsWithNotLendUmbrella()
    {
        return $this->model->where('status', self::STATUS_ENABLE)->withCount(['umbrellas' => function ($query) {
            $query->where('user_id', 0);
        }])->get();
    }

    public function getStationsWithLendUmbrella()
    {
        return $this->model->where('status', self::STATUS_ENABLE)->withCount(['umbrellas' => function ($query) {
            $query->where('user_id', '<>', 0);
        }])->get();
    }
}