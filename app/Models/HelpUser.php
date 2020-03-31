<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HelpUser extends Model
{
    protected $fillable = ['user_id', 'deal_id', 'name', 'id_number', 'status', 'type'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function deal()
    {
        return $this->belongsTo(WechatDeal::class, 'deal_id', 'id');
    }
}
