<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolUser extends Model
{
    protected $table = 'user_school';

    protected $fillable = ['user_id', 'school_id'];

    public function school()
    {
        return $this->belongsTo(School::class, 'school_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
