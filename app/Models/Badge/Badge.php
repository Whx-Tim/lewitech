<?php

namespace App\Models\Badge;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
    protected $fillable = ['name', 'type', 'status', 'badge_url', 'remote_url', 'local_url'];

    public function users()
    {
        return $this->hasMany(BadgeUser::class, 'badge_id', 'id');
    }
}
