<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SignTimerApply extends Model
{
    protected $fillable = ['timer_id', 'user_id', 'is_free'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function timer()
    {
        return $this->belongsTo(SignTimer::class, 'timer_id', 'id');
    }
}
