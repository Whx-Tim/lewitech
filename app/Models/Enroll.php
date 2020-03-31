<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Enroll extends Model
{
    use SoftDeletes;

    protected $guarded = ['_token', '_method'];
    /**
     * 获取报名的用户信息
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 获取报名的活动信息
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function active()
    {
        return $this->morphTo();
    }

    /**
     * 获取需求发布的需求信息
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function demand()
    {
        return $this->morphTo();
    }

    /**
     * 获取报名信息的访问量
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function view()
    {
        return $this->morphOne('App\Models\View', 'view');
    }

    /**
     * 获取报名信息的访问用户
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function viewUser()
    {
        return $this->morphMany('App\Models\ViewUser', 'view');
    }
}
