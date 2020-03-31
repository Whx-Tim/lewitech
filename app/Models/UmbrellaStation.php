<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UmbrellaStation extends Model
{
    protected $guarded = ['_token', '_method'];

    public function umbrellas()
    {
        return $this->hasMany(Umbrella::class, 'station_id', 'id');
    }

    static public function number2station($number)
    {
        switch ($number) {
            case 1:
                return '深大';
            case 2:
                return '桃园';
            case 3:
                return '高新园';
            case 4:
                return '车公庙';
            default:
                return '未知';
        }
    }

    static public function status2string($status)
    {
        switch ($status) {
            case 0:
                return '不可借';
            case 1:
                return '可借';
            default:
                return '站点异常';

        }
    }
}
