<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SignMedalWeek extends Model
{
    protected $fillable = ['medal', 'rank', 'time_value', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
