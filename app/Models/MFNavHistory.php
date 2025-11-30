<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MFNavHistory extends Model
{

    protected $table = 'MF_nav_histories';

    protected $fillable = [
        'folio_holding_id', 'nav', 'nav_date'
    ];

    protected $casts = [
        'nav_date' => 'date'
    ];

    public function folioHolding()
    {
        return $this->belongsTo(MFFolioHolding::class);
    }
}
