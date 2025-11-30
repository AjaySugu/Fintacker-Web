<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MFFolio extends Model
{
    protected $table = 'MF_folios'; 

    protected $fillable = [
        'demat_account_id', 'folio_no', 'masked_folio_no',
        'pan', 'profile_type', 'holders', 'raw'
    ];

    protected $casts = [
        'holders' => 'array',
        'raw' => 'array',
    ];

    public function dematAccount()
    {
        return $this->belongsTo(DematAccount::class);
    }

    public function holdings()
    {
        return $this->hasMany(MFFolioHolding::class, 'folio_id');
    }

    public function transactions()
    {
        return $this->hasMany(MFTransaction::class);
    }
}
