<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UmbrellaShare extends Model
{
    protected $guarded = ['_token', '_method'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function friend()
    {
        return $this->belongsTo(User::class, 'friend_id');
    }
}
