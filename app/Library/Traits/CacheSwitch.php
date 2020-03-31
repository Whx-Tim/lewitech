<?php
namespace App\Library\Traits;

trait CacheSwitch
{
    /**
     * 缓存开关
     *
     * @var bool
     */
    protected $cache_switch = true;

    /**
     * 缓存默认时间
     *
     * @var int
     */
    protected $cache_time = 15;

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
        $this->cache_switch = true;

        return $this;
    }

    /**
     * 关闭缓存
     *
     * @return $this
     */
    public function closeCache()
    {
        $this->cache_switch = false;

        return $this;
    }
}