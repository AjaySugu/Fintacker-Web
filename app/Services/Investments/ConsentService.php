<?php

namespace App\Services\Investments;

use App\Models\SetuConsent;
use App\Models\SetuSession;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class ConsentService
{
    protected $tokenService;
    private $instanceID;

    public function __construct(TokenService $tokenService)
    {
        $this->tokenService = $tokenService;
        $this->instanceID = config('services.setu.x-product-instance-id');
    }

    public function createConsent(int $userId, array $consentData): array
    {
        $token = $this->tokenService->generateToken();

        if (!$token['success']) return $token;
        $accessToken = $token['token']->token;

        // Create consent in Setu
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
            'Content-Type' => 'application/json',
            'x-product-instance-id' => $this->instanceID
        ])->post('https://fiu-sandbox.setu.co/v2/consents', $consentData);

        $data = $response->json();
        $dataRangeFrom = now()->startOfDay();
        $dataRangeTo   = now()->addYear()->subDay()->endOfDay();
        if ($response->successful() && isset($data['id'])) {
            $consent = SetuConsent::create([
                'user_id' => $userId,
                'consent_id' => $data['id'],
                'start_date' => $dataRangeFrom->toDateTimeString(), 
                'end_date' => $dataRangeTo->toDateTimeString(),
                'status' => $data['status'] ?? 'PENDING',
                'redirect_url' => $data['url'] ?? null
            ]);

            if ($consent) {
                return (['status' => true, 'consent' => $consent]);
            }
        }
        return (['status' => false, 'message' => 'Failed to create consent', 'data' => $data]);
    }

    public function getSessionId(string $consent_id) {

        $token = $this->tokenService->generateToken();
        if (!$token['success']) return $token;

        $accessToken = $token['token']->token;

        $sessionData = [
            'consentId' => $consent_id,
            'dataRange' => [
                'from'=> Carbon::now()->toIso8601String(),
                'to'  => Carbon::now()->toIso8601String()
            ],
            'format' => 'json',
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
            'Content-Type' => 'application/json',
            'x-product-instance-id' => $this->instanceID
        ])->post('https://fiu-sandbox.setu.co/v2/sessions', $sessionData);

        $data = $response->json();
            dd($data);
        if ($response->successful() && isset($data['id'])) {
            $session = SetuSession::create([
                'consent_id' => $data['consentId'],
                'session_id' => $data['id'],
                'start_date' => Carbon::now()->toIso8601String(),
                'end_date' => Carbon::now()->toIso8601String(),
            ]);

            if ($session) {
                return (['status' => true,]);
            } else {
                return (['message' => $session]);
            }
        }
        return (['status' => false, 'message' => 'Failed to create session']);
    }

    public function fetchMfData(SetuConsent $consent): array
    {
        if ($consent->status !== 'APPROVED') {
            return ['success' => false, 'message' => 'Consent not approved yet'];
        }

        $token = $consent->user->tokens()
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (!$token) return ['success' => false, 'message' => 'No valid token found'];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token->token,
            'Content-Type' => 'application/json'
        ])->get('https://fiu-sandbox.setu.co/v2/mf/fetch', [
            'consentId' => $consent->consent_id
        ]);

        return $response->successful()
            ? ['success' => true, 'data' => $response->json()]
            : ['success' => false, 'data' => $response->json()];
    }
}
