<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MFScheme extends Model
{

    protected $table = 'MF_schemes'; 

    protected $fillable = [
        'amc', 'scheme_code', 'scheme_plan', 'scheme_option',
        'scheme_category', 'isin', 'isin_description', 'ucc',
        'amfi_code', 'registrar', 'metadata'
    ];

    protected $casts = [
        'metadata' => 'array'
    ];

    public function holdings()
    {
        return $this->hasMany(MFFolioHolding::class);
    }

    public function transactions()
    {
        return $this->hasMany(MFTransaction::class);
    }
}
