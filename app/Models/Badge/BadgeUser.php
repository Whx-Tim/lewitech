<?php

namespace App\Models\Badge;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class BadgeUser extends Model
{
    protected $fillable = ['badge_id', 'user_id', 'data'];

    public function badge()
    {
        return $this->belongsTo(Badge::class, 'badge_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
