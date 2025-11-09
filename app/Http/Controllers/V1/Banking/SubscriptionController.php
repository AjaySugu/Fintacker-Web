<?php

namespace App\Http\Controllers\V1\Banking;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SubscriptionController extends Controller
{
    /**
     * List all active subscriptions for a user
     */
    public function index($userId)
    {
       $subscriptions = Subscription::with('category')
        ->where('user_id', $userId)
        ->where('status', 'active')
        ->get()
        ->map(function ($sub) {
            return [
                'name' => $sub->name,
                'amount' => $sub->amount,
                'category' => optional($sub->category)->name,
                'next_payment' => $sub->nextPaymentDate()->toDateString(),
                'reminder_message' => $sub->generateReminder(),
            ];
        });

        return response()->json([
            'status' => true,
            'data' => $subscriptions,
        ]);
    }

    /**
     * Get insights — total + breakdown by type
     */
    public function insights($userId)
    {
        $subs = Subscription::with('category:id,name')
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->get();

        $totalMonthly = $subs->sum('amount');

        $byType = $subs->groupBy('category_id')->map(function ($items, $categoryId) {
            $categoryName = optional($items->first()->category)->name ?? 'Unknown';
            $totalAmount = $items->sum('amount');

            return [
                'category_id' => $categoryId,
                'category_name' => $categoryName,
                'total_amount' => $totalAmount,
            ];
        })->values(); 

        return response()->json([
            'status' => true,
            'total_spent' => $totalMonthly,
            'breakdown' => $byType,
        ]);

    }

    /**
     * Optional: Auto-send reminders as push notifications
     */
    public function sendReminders()
    {
        $subscriptions = Subscription::where('status', 'active')->get();

        foreach ($subscriptions as $sub) {
            $message = $sub->generateReminder();

            if ($message) {
                // If Expo push token stored in user table
                $expoToken = $sub->user->expo_token ?? null;

                if ($expoToken) {
                    $this->sendPushNotification($expoToken, "Fin Tracker Reminder", $message);
                }
            }
        }

        return response()->json(['status' => true, 'message' => 'Reminders sent successfully.']);
    }

    /**
     * Helper: Send Expo push notification
     */
    protected function sendPushNotification($expoToken, $title, $body)
    {
        $client = new \GuzzleHttp\Client();

        try {
            $client->post('https://exp.host/--/api/v2/push/send', [
                'headers' => ['Accept' => 'application/json', 'Content-Type' => 'application/json'],
                'json' => [
                    'to' => $expoToken,
                    'sound' => 'default',
                    'title' => $title,
                    'body' => $body,
                ],
            ]);
        } catch (\Exception $e) {
            \Log::error("Expo push failed: " . $e->getMessage());
        }
    }
}
