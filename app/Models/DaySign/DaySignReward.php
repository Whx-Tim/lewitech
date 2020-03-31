<?php

namespace App\Models\DaySign;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class DaySignReward extends Model
{
    protected $fillable = ['user_id', 'day_sign_id', 'reward', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function daySign()
    {
        return $this->belongsTo(DaySign::class, 'day_sign_id', 'id');
    }
}
