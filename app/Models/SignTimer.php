<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SignTimer extends Model
{
    protected $fillable = ['status', 'day', 'reward', 'apply_count', 'fail_count', 'start_at', 'end_at'];

    public function signs()
    {
        return $this->hasMany(Sign::class, 'sign_timer_id', 'id');
    }

    public function sign_shares()
    {
        return $this->hasMany(SignShare::class, 'sign_timer_id', 'id');
    }

    public function deals()
    {
        return $this->hasMany(SignDeal::class, 'sign_timer_id', 'id');
    }

    public function applies()
    {
        return $this->hasMany(SignTimerApply::class, 'timer_id', 'id');
    }

    public function months()
    {
        return $this->hasMany(SignMonth::class, 'timer_id', 'id');
    }
}
