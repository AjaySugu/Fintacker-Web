<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class TestPushController extends Controller
{
    public function send()
    {
        // 1️⃣ Get the first subscription (for testing)
        $subscription = DB::table('push_subscriptions')->first();

        if (!$subscription) {
            return response()->json(['error' => 'No subscription found']);
        }

        // 2️⃣ Prepare payload
        $payload = [
            "title" => "Test Push Notification",
            "body" => "Hello! Desktop push working directly via FCM.",
            "icon" => "/icon.png"
        ];

        // 3️⃣ Send push via FCM
        $response = Http::withHeaders([
            'Authorization' => 'key=<YOUR_FIREBASE_SERVER_KEY>',
            'Content-Type' => 'application/json',
        ])->post($subscription->endpoint, $payload);

        return response()->json([
            'message' => 'Push sent',
            'response' => $response->body()
        ]);
    }
}
