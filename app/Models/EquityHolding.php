<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EquityHolding extends Model
{
    protected $fillable = ['demat_account_id','company_id','isin','symbol','issuer_name','units','avg_rate','last_traded_price','investment_value','current_value','raw'];
    protected $casts = ['raw' => 'array'];

    public function dematAccount(){ return $this->belongsTo(DematAccount::class); }
    public function company(){ return $this->belongsTo(EquityCompany::class); }
    public function transactions()
        {
            return $this->hasMany(EquityTransaction::class, 'demat_account_id', 'demat_account_id')
                        ->whereRaw('equity_transactions.isin = ?', [$this->isin]);
        }
}