<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SignMonth extends Model
{
    protected $fillable = ['status', 'total_day', 'duration_day', 'time_value', 'reward', 'user_id', 'timer_id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function timer()
    {
        return $this->belongsTo(SignTimer::class, 'timer_id', 'id');
    }
}
