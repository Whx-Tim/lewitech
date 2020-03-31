<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GetUp extends Model
{
    protected $table = 'get_up';

    protected $guarded = ['_token', '_method'];

    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class, 'open_id', 'openid');
    }

}
