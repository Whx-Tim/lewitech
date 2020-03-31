<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SignShare extends Model
{
    protected $fillable = ['user_id', 'help_id', 'sign_timer_id', 'type'];

    public function user_from()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function user_to()
    {
        return $this->belongsTo(User::class, 'help_id', 'id');
    }

    public function sign_timer()
    {
        return $this->belongsTo(SignTimer::class, 'sign_timer_id', 'id');
    }

}
