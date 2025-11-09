<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtpLog extends Model
{
    use HasFactory;

    protected $table = 'otp_logs';

    protected $fillable = [
        'phone_number',
        'otp',
        'message_sid',
        'status',
        'error_message',
        'payload',
    ];

     public function isDelivered()
    {
        return $this->status === 'delivered';
    }

    public function isFailed()
    {
        return in_array($this->status, ['failed', 'undelivered']);
    }
}
