<?php
namespace App\Services;

use App\Library\Traits\SelfClass;
use App\Models\UmbrellaStation;
use App\Repositories\Wechat\QrcodeRepository;

class QrcodeService
{
    use SelfClass;

    const WECHAT_FOREVER   = 1;
    const WECHAT_TEMPORARY = 2;
    const LOCAL = 3;
    const STATUS_UNABLE = 1;
    const STATUS_ENABLE = 2;

    /**
     * @var array $type_map 类型映射解释
     */
    public $type_map = [
        self::WECHAT_FOREVER   => '微信永久二维码',
        self::WECHAT_TEMPORARY => '微信临时二维码',
        self::LOCAL            => '本地二维码',
    ];

    /**
     * action_name的类型映射
     *
     * @var array
     */
    public $action_name_map = [
        self::WECHAT_TEMPORARY => 'temporary',
        self::WECHAT_FOREVER   => 'forever'
    ];

    /**
     * @var array $status_map 状态映射解释
     */
    public $status_map = [
        self::STATUS_UNABLE => '不可用',
        self::STATUS_ENABLE => '可用'
    ];

    /**
     * 获取类型映射数组
     *
     * @return array
     */
    public function getTypeMap()
    {
        return $this->type_map;
    }

    /**
     * 获取状态映射数组
     *
     * @return array
     */
    public function getStatusMap()
    {
        return $this->status_map;
    }

    /**
     * 获得action_name的状态映射数组
     *
     * @return array
     */
    public function getActionNameMap()
    {
        return $this->action_name_map;
    }

    /**
     * 创建一个公益爱心伞的站点二维码
     *
     * @param UmbrellaStation $station
     * @throws \Exception
     */
    public function createStationQrcode(UmbrellaStation $station)
    {
        $scene = 'umbrella_still_station_' . $station->id;

        return QrcodeRepository::self()->forever($scene)->create();
    }
}