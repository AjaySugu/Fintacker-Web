<?php

namespace App\Http\Controllers\V1\Investments;

use App\Http\Controllers\Controller;
use App\Models\SetuConsent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SetuNotificationController extends Controller
{
    
    public function consentCallBack(Request $request) {
        $payload = $request->all();
        Log::info('Setu Notification:', $payload);

        if (($payload['type'] ?? null) === 'CONSENT_STATUS_UPDATE') {
            $consentId = $payload['consentId'];
            $status = $payload['data']['status'];
        
            if ($consentId && $status) {
                $setuConsent = SetuConsent::where('consent_id', $consentId)->first();
                if ($setuConsent) {
                    $setuConsent->status = $status;
                    $setuConsent->save();
        
                    Log::info('Consent Status Updated', [
                        'consentId' => $consentId,
                        'status'    => $status,
                    ]);
                }
            }
        }
    }
}
