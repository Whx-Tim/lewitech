<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SignCard extends Model
{
    protected $fillable = ['status', 'start_at', 'end_at', 'user_id', 'card_id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function card()
    {
        return $this->belongsTo(Card::class, 'card_id', 'id');
    }

    public function deal()
    {
        return $this->hasOne(SignDeal::class, 'sign_card_id', 'id');
    }
}
