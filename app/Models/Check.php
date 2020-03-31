<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Check extends Model
{
    protected $guarded = ['_token', '_method'];

    public function demand()
    {
        return $this->morphTo();
    }

    //
}
