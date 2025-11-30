<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MFTransaction extends Model
{

    protected $table = 'MF_transactions';

    protected $fillable = [
        'folio_id', 'scheme_id', 'txn_id', 'txn_type', 
        'mode', 'units', 'amount', 'nav', 
        'transaction_date', 'narration', 'raw'
    ];

    protected $casts = [
        'transaction_date' => 'datetime',
        'raw' => 'array'
    ];

    public function folio()
    {
        return $this->belongsTo(MFFolio::class);
    }

    public function scheme()
    {
        return $this->belongsTo(MFScheme::class);
    }
}
