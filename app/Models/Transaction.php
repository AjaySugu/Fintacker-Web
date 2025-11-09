<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'category_id', 'type', 'amount', 'transaction_date', 'transaction_time', 'payment_method', 'tags', 'notes', 'attachment_path'
    ];

    protected $casts = [
        'tags' => 'array',
        'transaction_date' => 'date',
        'transaction_time' => 'time',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
