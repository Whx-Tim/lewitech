<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    protected $fillable = ['type', 'content'];

    public function scopeActiveBanner($query)
    {
        return $query->where('type', 'activeBanner');
    }
}
