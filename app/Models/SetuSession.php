<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SetuSession extends Model
{
    protected $fillable = ['consent_id', 'session_id', 'start_date', 'end_date'];
}