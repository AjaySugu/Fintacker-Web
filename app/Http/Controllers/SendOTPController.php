<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Twilio\Rest\Client;

class SendOTPController extends Controller
{
    protected $twilio;

    public function __construct()
    {
        $this->twilio = new Client(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required'
        ]);

        $phone = $request->phone;

        $verification = $this->twilio->verify->v2->services(env('TWILIO_VERIFY_SID'))
            ->verifications
            ->create($phone, "sms");

        

        return response()->json([
            'all' => $verification,
            'message' => 'OTP sent successfully',
            'status' => $verification->status
        ]);
    }
}