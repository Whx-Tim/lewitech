<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    protected $fillable = ['name', 'badge_url', 'remote_url', 'local_url'];

    /**
     * 获取该学校的所有用户信息
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_school', 'school_id', 'user_id');
    }
}
