<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EquityTransaction extends Model
{
    protected $fillable = ['demat_account_id','txn_id','txn_type','instrument_type','exchange','isin','symbol','company_name','units','rate','trade_value','other_charges','total_charge','transaction_date_time','narration','raw'];
    protected $casts = ['raw' => 'array', 'transaction_date_time' => 'datetime'];

    public function dematAccount(){ return $this->belongsTo(DematAccount::class); }


     public function holding()
    {
        return $this->belongsTo(EquityHolding::class);
    }
}
