<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Qrcode extends Model
{
    protected $fillable = ['value', 'url', 'path', 'type', 'status', 'ticket', 'expire_seconds', 'scene_str', 'action_name', 'description'];
}
