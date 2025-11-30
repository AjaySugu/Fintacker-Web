<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DematAccount extends Model
{
    use HasFactory;
    protected $table = 'demant_accounts'; 

    protected $fillable = ['user_id','consent_id','link_ref','masked_acc_number','masked_demat_id','fip_id','broker_name','status','raw_response'];

    protected $casts = [
        'raw_response' => 'array',
    ];

    public function holdings() { return $this->hasMany(EquityHolding::class); }
    public function transactions() { return $this->hasMany(EquityTransaction::class); }
    public function user() { return $this->belongsTo(User::class); }

    public function folios()
    {
         return $this->hasMany(MFFolio::class, 'demat_account_id'); 
    }
}