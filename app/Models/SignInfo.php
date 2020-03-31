<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SignInfo extends Model
{
    protected $fillable = ['status', 'total_count', 'duration_count', 'time_value', 'reward', 'is_free', 'is_apply', 'user_id', 'month_count', 'total_reward'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
