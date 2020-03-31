<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Business extends Model
{
    protected $guarded = ['_token', '_method'];

    public function image()
    {
        return $this->morphMany('App\Models\Image', 'imageable');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function comments()
    {
        return $this->morphMany('App\Models\Comment', 'commentable');
    }

    public function branches()
    {
        return $this->hasMany(BusinessBranch::class, 'business_id', 'id');
    }

    public function type2string()
    {
        switch ($this->type) {
            case 0:
                return '餐饮娱乐类';
            case 1:
                return '酒店类';
            case 2:
                return '生活出行类';
            case 3:
                return '运动健康类';
            default:
                return '未知';
        }
    }
}
