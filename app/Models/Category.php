<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'icon',
        'color',
        'type',
        'is_ai_suggested',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scope to get either default categories or user categories
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId)
                     ->orWhereNull('user_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
