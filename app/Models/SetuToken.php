<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SetuToken extends Model
{
    protected $fillable = ['user_id', 'token', 'expires_at'];

    protected $dates = ['expires_at'];
}
