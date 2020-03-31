<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShareholderDemand extends Model
{
    use SoftDeletes;

    protected $guarded = ['_token', '_method'];

    /**
     * 获取需求的点赞信息
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function view()
    {
        return $this->morphOne('App\Models\View', 'view');
    }

    /**
     * 获取查看需求的用户
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function viewUsers()
    {
        return $this->morphMany('App\Models\ViewUser', 'view');
    }

    /**
     * 获取需求的用户信息
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 获取需求帮助信息
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function enrolls()
    {
        return $this->morphMany('App\Models\Enroll', 'enrollable');
    }

    /**
     * 获取需求的审核信息
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function check()
    {
        return $this->morphOne('App\Models\Check', 'check');
    }

    /**
     * 获取需求的帮助链接
     *
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string
     */
    public function helpUrl()
    {
        return url('wechat/demand/help/' . $this->id);
    }

    /**
     * 获取需求的详情链接
     *
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string
     */
    public function detailUrl()
    {
        return url('wechat/demand/detail/'. $this->id);
    }

    public function adminHomeUrl()
    {
        return route('admin.demand.index');
    }


    public function adminDetailUrl()
    {
        return route('admin.demand.detail', ['cache_demand' => $this->id]);
    }

    public function adminEditUrl()
    {
        return route('admin.demand.edit', ['cache_demand' => $this->id]);
    }

    public function adminDeleteUrl()
    {
        return route('admin.demand.delete', ['cache_demand' => $this->id]);
    }

    public function adminCheckUrl()
    {
        return route('admin.demand.check', ['cache_demand' => $this->id]);
    }

    public function adminEnrollsUsersUrl()
    {
        return route('admin.demand.enrolls', ['cache_demand' => $this->id]);
    }

    public function adminViewUsersUrl()
    {
        return route('admin.demand.views', ['cache_demand' => $this->id]);
    }

    public function status2String()
    {
        switch ($this->check->status) {
            case 0:
                return '未审核';
            case 1:
                return '已审核';
            case 2:
                return '已关闭';
        }
    }

}
