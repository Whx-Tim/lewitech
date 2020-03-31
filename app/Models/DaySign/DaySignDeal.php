<?php

namespace App\Models\DaySign;

use Illuminate\Database\Eloquent\Model;

class DaySignDeal extends Model
{
    protected $fillable = [
        'user_id', 'day_sign_id', 'openid', 'result_code',
        'err_code', 'err_code_des', 'trade_type', 'bank_type', 'total_fee', 'cash_fee',
        'transaction_id', 'out_trade_no', 'out_refund_no', 'refund_id', 'refund_at',
        'time_end'
    ];

    public function daySign()
    {
        return $this->belongsTo(DaySign::class, 'day_sign_id', 'id');
    }
}
