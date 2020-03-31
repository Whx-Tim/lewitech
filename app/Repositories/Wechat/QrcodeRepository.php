<?
namespace App\Repositories\Wechat;

use App\Library\Traits\SelfClass;
use App\Models\Qrcode;
use App\Services\QrcodeService;
use EasyWeChat\Foundation\Application;

class QrcodeRepository
{
    use SelfClass;

    /**
     * @var Application $wechat
     */
    private $wechat;

    /**
     * @var Qrcode $qrcode
     */
    private $qrcode;

    private $result;

    public function __construct()
    {
        $this->wechat = app('wechat');
        $this->qrcode = new Qrcode();
    }

    /**
     * 创建二维码
     *
     * @param null $description
     * @return $this|\Illuminate\Database\Eloquent\Model
     * @throws \Exception
     */
    public function create($description = null)
    {
        if (empty($this->result)) {
            throw new \Exception('还未链式调用forever/temporary生成result');
        }
        $data = $this->parseResultData();
        if (!is_null($description)) {
            $data['description'] = $description;
        }

        return $this->qrcode->create($data);
    }

    /**
     * 整合写入数据库的result数据
     *
     * @return array
     */
    private function parseResultData()
    {
        return [
            'ticket'         => $this->result['ticket'],
            'value'          => $this->result['url'],
            'url'            => $this->wechat->qrcode->url($this->result['ticket']),
            'expire_seconds' => empty($this->result['expire_seconds']) ? 0 : $this->result['expire_seconds'],
            'action_name'    => $this->result['action_name'],
            'scene_str'      => $this->result['scene_str'],
            'type'           => $this->result['type'],
            'status'         => QrcodeService::STATUS_ENABLE,
        ];
    }

    /**
     * 设置result的type数据
     *
     * @param $type
     */
    private function setResultType($scene_str, $type)
    {
        if (!empty($this->result['type'])) {
            $this->result['origin_type'] = $this->result['type'];
        }

        $this->result['type'] = $type;
        $this->result['action_name'] = QrcodeService::self()->getActionNameMap()[$type];
        $this->result['scene_str'] = $scene_str;
    }

    /**
     * 创建一个永久的微信二维码
     *
     * @param $scene_str
     * @return $this
     */
    public function forever($scene_str)
    {
        $this->result = $this->wechat->qrcode->forever($scene_str);
        $this->setResultType($scene_str, QrcodeService::WECHAT_FOREVER);

        return $this;
    }

    /**
     * 创建一个临时的微信二维码
     *
     * @param $scene_str
     * @param int $expire_seconds
     * @return $this
     */
    public function temporary($scene_str, $expire_seconds = 2592000)
    {
        $this->result = $this->wechat->qrcode->temporary($scene_str, $expire_seconds);
        $this->setResultType($scene_str, QrcodeService::WECHAT_TEMPORARY);

        return $this;
    }
}