<?php

namespace App\Models\DaySign;

use Illuminate\Database\Eloquent\Model;

class DaySign extends Model
{
    protected $fillable = ['status', 'reward', 'amount'];

    public function histories()
    {
        return $this->hasMany(DaySignHistory::class, 'day_sign_id', 'id');
    }

    public function deals()
    {
        return $this->hasMany(DaySignDeal::class, 'day_sign_id', 'id');
    }

    public function reward()
    {
        return $this->hasMany(DaySignReward::class, 'day_sign_id', 'id');
    }
}
