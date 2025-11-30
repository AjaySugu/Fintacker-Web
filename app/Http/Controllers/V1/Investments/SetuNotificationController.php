<?php

namespace App\Http\Controllers\V1\Investments;

use App\Http\Controllers\Controller;
use App\Jobs\FetchUserDataJob;
use App\Models\SetuConsent;
use App\Services\Investments\ConsentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SetuNotificationController extends Controller
{
    protected $consentService;

    public function __construct(ConsentService $consentService)
    {
        $this->consentService = $consentService;
    }
    
    public function consentCallBack(Request $request) {
        $payload = $request->all();
        Log::info('Setu Notification:', $payload);

        if (($payload['type'] ?? null) === 'CONSENT_STATUS_UPDATE') {
            $consentId = $payload['consentId'];
            $status = $payload['data']['status'];
        
            if ($consentId && $status) {
                $setuConsent = SetuConsent::where('consent_id', $consentId)->first();
                $userId = $setuConsent->user_id;
                if ($setuConsent) {
                    $setuConsent->status = $status;
                    $setuConsent->save();
                    $getSessionId = $this->consentService->getSessionId($setuConsent->consent_id);
                    Log::info('get sessionid', $getSessionId);
                    if ($getSessionId) {
                        // $getUserData = $this->consentService->fetchUserData($getSessionId['session_id']);
                        FetchUserDataJob::dispatch($getSessionId['session_id'], $userId);
                        Log::info('Consent Status Updated', [
                            'consentId' => $consentId,
                            'status'    => $status,
                        ]);
                    }                     
                }
            }
        }
    }
}
