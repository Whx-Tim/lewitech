<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Sign extends Model
{
    protected $fillable = ['today_time', 'today_status', 'sign_timer_id', 'user_id', 'time_value'];

    protected $guarded = ['_token', '_method'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function sign_timer()
    {
        return $this->belongsTo(SignTimer::class, 'sign_timer_id', 'id');
    }
}
