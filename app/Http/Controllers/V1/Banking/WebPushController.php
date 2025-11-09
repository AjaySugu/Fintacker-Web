<?php

namespace App\Http\Controllers\V1\Banking;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WebPushController extends Controller
{
    public function store(Request $request)
    {
        // dd($user = auth()->user()); 
        $user = auth()->user();
        $user->updatePushSubscription(
            $request->endpoint,
            $request->key,
            $request->token
        );

        return response()->json(['success' => true]);
    }

    public function check(Request $request)
    {
        $user = auth()->user();

        $user->updatePushSubscription(
            $request->endpoint,
            $request->keys['p256dh'],
            $request->keys['auth']
        );

        return response()->json(['success' => true]);
    }
}
