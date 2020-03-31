<?php
namespace App\Services;

use App\Library\Traits\CacheSwitch;
use App\Library\Traits\SelfClass;
use Illuminate\Support\Facades\Cache;

class WeatherService
{
    use SelfClass;
    use CacheSwitch;

    const CACHE_NAME = 'weather';

    private $url = 'http://wthrcdn.etouch.cn/weather_mini?city=深圳';

    /*
     * 昨日天气情况
     */
    private $yesterday;

    /*
     * 今日天气情况
     */
    private $today;

    /*
     * 未来五天天气预测（包括今日）
     */
    private $forecast;

    /*
     *  今日最高温度
     */
    private $high;

    /*
     * 今日最低温度
     */
    private $low;

    /*
     * 今日天气
     */
    private $type;

    /*
     * 实时温度
     */
    private $wendu;

    /*
     * 今日日期
     */
    private $date;

    /*
     * 获取到的数据
     */
    private $origin;

    /*
     * 城市
     */
    private $city;

    public function __construct($url = null)
    {
        if (!is_null($url)) {
            $this->setUrl($url);
        }

        $this->init($this->url);
    }

    public function init($url)
    {
        $output = $this->curlWeatherData($url);

        $this->origin = $output;
        $this->today = ($output->forecast)[0];
        $this->type = $this->today->type;
        $this->high = $this->today->high;
        $this->low = $this->today->low;
        $this->date = $this->today->date;
        $this->wendu = $output->wendu;
        $this->yesterday = $output->yesterday;
        $this->city = $output->city;
        $this->forecast = $output->forecast;

        return $this;
    }

    private function curlWeatherData($url)
    {
        if (!$this->cache_switch) {
            Cache::forget(self::CACHE_NAME);
        }

        return Cache::remember(self::CACHE_NAME, $this->cache_time, function () use ($url) {
            $this->setUrl($url);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');
            curl_setopt($ch, CURLOPT_POST, false);
            curl_setopt($ch, CURLOPT_HEADER, 0);

            $output = curl_exec($ch);
            curl_close($ch);
            $output = (json_decode($output))->data;
            $output->yesterday->fl = $this->xml_decode($output->yesterday->fl);
            foreach ($output->forecast as &$value) {
                $value->fengli = $this->xml_decode($value->fengli);
            }

            return $output;
        });
    }

    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function getOrigin()
    {
        return $this->origin;
    }

    public function getYesterday()
    {
        return $this->yesterday;
    }

    public function getToday()
    {
        return $this->today;
    }

    public function getForecast()
    {
        return $this->forecast;
    }

    public function getTodayHigh()
    {
        return $this->high;
    }

    public function getTodayLow()
    {
        return $this->low;
    }

    public function getCity()
    {
        return $this->city;
    }

    public function getWendu()
    {
        return $this->wendu;
    }

    public function getTodayDate()
    {
        return $this->date;
    }

    public function getType()
    {
        return $this->type;
    }

    private function xml_decode($xml_item)
    {
        preg_match('/<!\[CDATA\[(.*)\]\]>/', $xml_item, $match);

        return $match[1];
    }


}