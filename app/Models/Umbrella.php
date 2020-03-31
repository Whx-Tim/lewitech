<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Umbrella extends Model
{
    use SoftDeletes;

    protected $guarded = ['_token', '_method'];

    protected $station_array = ['未激活', '深大', '高新园', '竹子林'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function station()
    {
        return $this->belongsTo(UmbrellaStation::class, 'station_id', 'id');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function histories()
    {
        return $this->hasMany(UmbrellaHistory::class);
    }

    static public function status2string($status)
    {
        switch ($status) {
            case 0:
                return '不可用';
            case 1:
                return '可用';
            case 2:
                return '正在使用中';
            case 3:
                return '遗失';
            default:
                return '异常';
        }
    }

    public function station2string()
    {
        foreach ($this->station_array as $key => $item) {
            if ($this->station_id == $key) {
                return $item;
            }
        }

        return '未激活';
    }

    public function scanCountIncrement()
    {
        $this->increment('scan_count');
    }

    public function realScanCountIncrement()
    {
        $this->increment('real_scan_count');
    }
}
