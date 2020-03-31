<?php

namespace App\Models\Score;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Score extends Model
{
    protected $fillable = ['type', 'score', 'status', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
