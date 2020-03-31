<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SignMedal extends Model
{
    protected $fillable = ['gold', 'silver', 'bronze', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
