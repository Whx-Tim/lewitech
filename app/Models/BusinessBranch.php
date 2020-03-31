<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessBranch extends Model
{
    protected $guarded = ['_token', '_method'];

    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id', 'id');
    }

    public function image()
    {
        return $this->morphMany('App\Models\Image', 'imageable');
    }
}
