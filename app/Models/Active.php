<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Active extends Model
{
    use SoftDeletes;

    protected $guarded = ['_token', '_method'];

    protected $dates = [
        'start_time',
        'end_time',
        'time'
    ];

    /**
     * 获取活动的报名信息
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function enrolls()
    {
        return $this->morphMany('App\Models\Enroll', 'enrollable');
    }

    /**
     * 获取活动的创建者信息
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 获取活动的浏览量
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function view()
    {
        return $this->morphOne('App\Models\View', 'view');
    }

    /**
     * 增加活动浏览量默认增加一次
     *
     * @param int $count
     */
    public function viewIncrement($count = 1)
    {
        if ($this->view) {
            $this->view()->increment('count', $count);
        } else {
            $this->view()->create(['count' => 0]);
        }
    }

    /**
     * 获取活动参与链接
     *
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string
     */
    public function applyUrl()
    {
        return url('wechat/active/apply/'.$this->id);
    }

    /**
     * 获取活动的详情链接
     *
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string
     */
    public function detailUrl()
    {
        return url('wechat/active/detail/'.$this->id);
    }

    public function homeUrl()
    {
        return url('wechat/active/list');
    }

    /**
     * 获取活动的取消报名链接
     *
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string
     */
    public function cancelUrl()
    {
        return url('wechat/active/cancel/apply/'. $this->id);
    }

    public function adminEditUrl()
    {
        return route('admin.active.edit', ['cache_active' => $this->id]);
    }

    public function adminDetailUrl()
    {
        return route('admin.active.detail', ['cache_active' => $this->id]);
    }

    public function adminDeleteUrl()
    {
        return route('admin.active.delete', ['cache_active' => $this->id]);
    }

    public function adminAddUrl()
    {
        return route('admin.active.add');
    }

    public function adminCheckUrl()
    {
        return route('admin.active.check', ['cache_active' => $this->id]);
    }

    public function status2String()
    {
        switch ($this->status) {
            case 0:
                return '未审核';
            case 1:
                return '已审核';
            case 2:
                return '已关闭';
        }
    }


}
