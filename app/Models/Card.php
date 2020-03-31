<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    protected $fillable = ['name', 'description', 'status', 'regulation_type', 'regulation', 'duration', 'start_at', 'end_at'];

    public function sign_cards()
    {
        return $this->hasMany(SignCard::class, 'card_id', 'id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'sign_cards', 'card_id', 'user_id');
    }
}
