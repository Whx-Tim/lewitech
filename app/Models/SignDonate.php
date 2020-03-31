<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SignDonate extends Model
{
    protected $fillable = ['name', 'phone', 'user_id', 'wechat_deal_id', 'type', 'remind'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function wechatDeal()
    {
        return $this->belongsTo(WechatDeal::class, 'wechat_deal_id', 'id');
    }
}
