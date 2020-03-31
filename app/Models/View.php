<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class View extends Model
{
    protected $guarded = ['_token', '_method'];

    /**
     * 获取所有浏览的主体信息
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function view()
    {
        return $this->morphTo();
    }
}
