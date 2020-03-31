<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Palette extends Model
{
    protected $fillable = ['source', 'description', 'sky_index', 'month'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
