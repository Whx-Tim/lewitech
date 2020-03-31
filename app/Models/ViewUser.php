<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ViewUser extends Model
{
    protected $guarded = ['_token', '_method'];

    /**
     * 获取用户查看的需求信息
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function demand()
    {
        return $this->morphTo();
    }

    /**
     * 获取浏览的用户信息
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
