<?php

namespace App\Models\Score;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ScoreHistory extends Model
{
    use SoftDeletes;

    protected $fillable = ['score', 'type', 'operation', 'operation_type', 'operation_notice', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
