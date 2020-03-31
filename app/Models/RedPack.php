<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RedPack extends Model
{
    protected $table = 'wechat_red_packs';

    protected $fillable = ['return_code', 'return_msg', 'result_code', 'mch_bill_no', 'openid', 'total_amount', 'send_list_id', 'user_id', 'err_code', 'err_code_des'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
