<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Models\User;
use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class FirebaseController extends Controller
{
    public function storeToken(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string',
        ]);

        $user = auth()->user();
        $user->fcm_token = $request->fcm_token;
        $user->save();

        return response()->json(['message' => 'FCM Token saved successfully']);
    }

    // Optional: send test notification
    public function sendTestNotification()
    {
        $user = auth()->user();

        if (!$user || !$user->fcm_token) {
            return response()->json(['message' => 'No FCM token found'], 400);
        }

        $this->sendNotification($user->fcm_token, 'Hello!', 'This is a test notification.');

        return response()->json(['message' => 'Notification sent']);
    }

    // public function sendNotification(Request $request)
    // {
    //     $request->validate([
    //         'title' => 'required|string',
    //         'body'  => 'required|string',
    //     ]);

    //     $title = $request->title;
    //     $body = $request->body;

    //     $users = User::whereNotNull('fcm_token')->get();

    //     if ($users->isEmpty()) {
    //         return response()->json(['message' => 'No users with FCM tokens found'], 400);
    //     }

    //     $serverKey = env('FIREBASE_SERVER_KEY'); // Put your server key in .env

    //     $client = new Client();

    //     foreach ($users as $user) {
    //         try {
    //             $response = $client->post('https://fcm.googleapis.com/fcm/send', [
    //                 'headers' => [
    //                     'Authorization' => 'key=' . $serverKey,
    //                     'Content-Type'  => 'application/json',
    //                 ],
    //                 'json' => [
    //                     'to' => $user->fcm_token,
    //                     'notification' => [
    //                         'title' => $title,
    //                         'body'  => $body,
    //                         'icon'  => url('/favicon.ico')
    //                         // 'click_action' => url('/'),
    //                     ],
    //                     'data' => [
    //                         'extra' => 'Extra data if needed'
    //                     ]
    //                 ]
    //             ]);

    //             // Optional: log success
    //             \Log::info("Notification sent to {$user->id}: ".$response->getBody());

    //         } catch (\Exception $e) {
    //             \Log::error("FCM error for user {$user->id}: ".$e->getMessage());
    //         }
    //     }

    //     return response()->json(['message' => 'Notifications sent']);
    // }

    private function getAccessToken()
    {
        $client = new GoogleClient();
        $client->setAuthConfig(storage_path('app/firebase/firebase-service-account.json'));
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        $token = $client->fetchAccessTokenWithAssertion();

        return $token['access_token'] ?? null;
    }

    public function sendNotification(Request $request)
    {
        $request->validate(['title' => 'required', 'body' => 'required']);

        $user = Auth::user();
        if (!$user->fcm_token) {
            return response()->json(['message' => 'User has no FCM token'], 400);
        }

        $accessToken = $this->getAccessToken();
        if (!$accessToken) {
            return response()->json(['message' => 'Cannot get access token'], 500);
        }

        $fcmUrl = "https://fcm.googleapis.com/v1/projects/xspend-n/messages:send";

        $client = new Client();
        $response = $client->post($fcmUrl, [
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'message' => [
                    'token' => $user->fcm_token,
                    'notification' => [
                        'title' => $request->title,
                        'body'  => $request->body
                    ],
                    'android' => ['priority' => 'high'],
                    'apns' => ['headers' => ['apns-priority' => '10']]
                ]
            ]
        ]);

        return response()->json([
            'message' => 'Notification sent!',
            'fcm_response' => json_decode($response->getBody(), true)
        ]);
    }

    public function saveTokenApp(Request $request)
    {
         $request->validate([
        'token' => 'required|string',
    ]);

    $userId = 2; // example; ideally get from auth
    $user = User::find($userId);

    if (!$user) {
        return response()->json(['message' => 'User not found'], 404);
    }

    $user->fcm_token = $request->token;
    $user->save();

    return response()->json([
        'message' => 'Token saved successfully',
        'data' => $user,
    ]);
}

    public function sendNotificationApp(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'body'  => 'required|string',
            // 'user_id' => 'nullable|integer', // optional, can target a user
        ]);

        // 🔹 Get user’s FCM token
        $user = User::find($request->user_id ?? 2); // default user 2 for testing
        if (!$user || !$user->fcm_token) {
            return response()->json(['error' => 'No FCM token found for user'], 400);
        }

        $expoToken = $user->fcm_token;

        // 🔹 Prepare notification data
        $notificationData = [
            'to' => $expoToken,
            'sound' => 'default',
            'title' => $request->title,
            'body' => $request->body,
            'data' => ['extraData' => 'Any custom payload here'],
        ];

        // 🔹 Send request to Expo Push API
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->post('https://exp.host/--/api/v2/push/send', $notificationData);

        if ($response->failed()) {
            return response()->json(['error' => 'Failed to send notification', 'details' => $response->json()], 500);
        }

        return response()->json([
            'message' => 'Notification sent successfully!',
            'expo_response' => $response->json(),
        ]);
    }

}
