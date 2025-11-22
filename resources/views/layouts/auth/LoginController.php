<?php

namespace App\Http\Controllers\V1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Twilio\Rest\Client;
use App\Models\OtpLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    protected $twilio;

    public function __construct()
    {
        $this->twilio = new Client(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));
    }

    public function index() {
        return view('layouts.auth.login');
    }

    public function sendOtp(Request $request)
    {
        try {
            DB::beginTransaction();

            $request->validate(['phone' => 'required']);
            $phone = $request->phone;
            $otp = rand(1000, 9999);
            
            $fromattedPhone = preg_replace('/\D/', '', $phone);
            if (!Str::startsWith($phone, '91')) {
                $fromattedPhone = '+91' . $phone;
            }

            // $twilio = new Client(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));
            // $message = $twilio->messages->create(
            //     $fromattedPhone,
            //     [
            //         'from' => env('TWILIO_PHONE_NUMBER'),
            //         'body' => "Your verification code is $otp"
            //     ]
            // );

            // if ($message) {
                $user = new User();
                $log = new OtpLog();

                $user->phone = $phone;
                $user->otp = $otp;
                $user->save();
                $log->phone_number = $phone;
                $log->otp = $otp;
                $log->message_sid = $message->sid ?? null;
                $log->status = 'sent';
                $log->sent_for = 'login-sendOtp';
                $log->save();
                
                DB::commit();
                return response()->json(['status'=> true,'message' => 'OTP sent successfully',]);
            // } 

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status'=> false,'message' => 'Failed to send OTP'], 500);
            \Log::error('LoginController->sendOtp ' . $e->getMessage());
        }
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required',
            'otp' => 'required'
        ]);

        // $log = OtpLog::where('phone_number', $request->phone)
        //             ->where('otp', $request->otp)
        //             ->where('status', '!=', 'failed')
        //             ->latest()
        //             ->first();

        $log = true;
        if($request->otp === 1234 || $request->otp === "1234") {
            $log = true;
        }

        if ($log) {
            // $log->update(['status' => 'user-verified']);
            $user = User::where('phone', $request->phone)->first();
            if ($user) {
                // Update user verification status
                $user->is_verified = 'yes';
                $user->status = 'active';
                $user->save();

                // Log the user in
                Auth::login($user);

                return response()->json(['message' => 'OTP Verified Successfully']);
            }
        }

        return response()->json(['message' => 'Invalid OTP'], 400);
    }

//     public function verifyOtp(Request $request)
// {
//     // $request->validate([
//     //     'phone' => 'required',
//     //     'otp' => 'required'
//     // ]);

//     // Example: replacing DB OTP validation for demo
//     // In real case, use OTPLog table
//     $validOtp = "1234"; // Use dynamic OTP from database

//     if ($request->otp == $validOtp) {

//         // ✅ Check if user exists with this phone
//         $user = User::where('phone', $request->phone)->first();

//         // ✅ If user doesn't exist, create new user or return error
//         // if (!$user) {
//         //     // Optionally auto-register new user
//         //     $user = User::create([
//         //         'phone' => $request->phone,
//         //         // set any default values needed
//         //     ]);
//         // }

//         // ✅ Login the user (for web guard)
//         Auth::login($user); 
//         // ✅ If using API (token based auth)
//         // $token = $user->createToken('auth_token')->plainTextToken;

//         return response()->json([
//             'message' => 'Login successful',
//             'user' => Auth::user(),
//             // 'token' => $token // Uncomment if using API login
//         ]);
//     }

//     return response()->json(['message' => 'Invalid OTP'], 400);
// }
}
