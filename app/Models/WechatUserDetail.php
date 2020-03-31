<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WechatUserDetail extends Model
{
    protected $guarded = ['_token', '_method'];

    /**
     * 获取详情信息的主体信息
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sex2String()
    {
        switch ($this->sex) {
            case 1:
                return '男';
            case 2:
                return  '女';
            default:
                return '未知';
        }
    }

    public function subscribe2string()
    {
        switch ($this->subscribe) {
            case 0:
                return '否';
            case 1:
                return '是';
            default:
                return '未知';
        }
    }

    public function is_shareholder()
    {
        switch ($this->is_shareholder) {
            case 1:
                return '是';
            default:
                return '否';
        }
    }


}
