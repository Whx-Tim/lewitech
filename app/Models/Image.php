<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $guarded = ['_token', '_method'];

    public function image()
    {
        return $this->morphTo();
    }
}
