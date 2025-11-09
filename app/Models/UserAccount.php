<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Transaction;

class UserAccount extends Model
{
    use HasFactory;

     protected $fillable = [
        'user_id', 'account_name', 'account_type', 'institution_name', 
        'account_number', 'current_balance', 'credit_limit',
        'is_synced', 'sync_metadata'
    ];

    protected $casts = [
        'sync_metadata' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'account_id');
    }
}
