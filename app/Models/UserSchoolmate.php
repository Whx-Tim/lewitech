<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSchoolmate extends Model
{
    protected $guarded = ['_token', '_method'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
