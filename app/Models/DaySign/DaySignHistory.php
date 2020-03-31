<?php

namespace App\Models\DaySign;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class DaySignHistory extends Model
{
    protected $fillable = ['status', 'time', 'time_value', 'user_id', 'day_sign_id'];

    public function daySign()
    {
        return $this->belongsTo(DaySign::class, 'day_sign_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
