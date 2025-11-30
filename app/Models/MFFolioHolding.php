<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MFFolioHolding extends Model
{

    protected $table = 'MF_folio_holdings'; 

    protected $fillable = [
        'folio_id', 'scheme_id', 'units', 'lien_units',
        'lockin_units', 'nav', 'nav_date', 'cost_value',
        'current_value', 'fatca_status', 'raw'
    ];

    protected $casts = [
        'raw' => 'array',
        'nav_date' => 'date',
    ];

     public function folio()
    {
        return $this->belongsTo(MFFolio::class, 'folio_id'); // matches migration column
    }

    public function transactions()
    {
        return $this->hasMany(MFTransaction::class, 'folio_id'); // matches transaction table
    }

    public function scheme()
    {
        return $this->belongsTo(MFScheme::class);
    }

    public function navHistories()
    {
        return $this->hasMany(MFNavHistory::class);
    }
}
