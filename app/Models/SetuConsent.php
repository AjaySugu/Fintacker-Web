<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SetuConsent extends Model
{
    protected $fillable = ['user_id', 'consent_id','start_date','end_date', 'status', 'redirect_url'];
}