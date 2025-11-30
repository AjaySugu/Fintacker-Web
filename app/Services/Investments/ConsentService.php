<?php

namespace App\Services\Investments;

use App\Models\SetuConsent;
use App\Models\SetuSession;
use App\Services\Investments\EquityIngestService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ConsentService
{
    protected $tokenService;
    private $instanceID;
    protected $equityService;
    protected $MFService;

    public function __construct(TokenService $tokenService, EquityIngestService $equityService, MFIngestService $MFService)
    {
        $this->tokenService = $tokenService;
        $this->instanceID = config('services.setu.x-product-instance-id');
        $this->equityService = $equityService;
        $this->MFService = $MFService;
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

    public function getSessionId(string $consent_id)
    {
        // Generate token
        $token = $this->tokenService->generateToken();
        if (!$token['success']) {
            return $token;
        }

        $accessToken = $token['token']->token;

        // Session date range (today)
        $dataRangeFrom = now()->startOfDay();
        $dataRangeTo   = now()->endOfDay();
        
        $sessionData = [
            'consentId' => $consent_id,
            'format'    => 'json',
            'dataRange' => [
                "from" => $dataRangeFrom->toIso8601String(),
                "to"   => $dataRangeTo->toIso8601String()
            ],
        ];

        Log::info("sessionData" ,$sessionData);

        // Call Setu API
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
            'Content-Type' => 'application/json',
            'x-product-instance-id' => $this->instanceID
        ])->post('https://fiu-sandbox.setu.co/v2/sessions', $sessionData);

        $data = $response->json();

        Log::info("sessioResponse" ,$data);

        if ($response->successful() && isset($data['id'])) {

            $session = SetuSession::create([
                'consent_id' => $consent_id,         
                'session_id' => $data['id'],         
                'start_date' => $dataRangeFrom->toDateTimeString(), 
                'end_date'   => $dataRangeTo->toDateTimeString(),
            ]);

            return [
                'status' => true,
                'session_id' => $data['id']
            ];
        }

        return [
            'status' => false,
            'message' => $data['errorMsg'] ?? 'Failed to create session'
        ];
    }

    public function fetchUserData(string $sessionId, int $userId)
    {
        // Generate token
        $token = $this->tokenService->generateToken();
        if (!$token['success']) {
            Log::error("Token generation failed", ['sessionId' => $sessionId]);
            return false;
        }

        $accessToken = $token['token']->token;

        try {
            // Call Setu API
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
                'x-product-instance-id' => $this->instanceID
            ])->get("https://fiu-sandbox.setu.co/v2/sessions/{$sessionId}");

            if (!$response->successful()) {
                Log::error("Setu API failed", [
                    'sessionId' => $sessionId,
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return false;
            }

            $data = $response->json();

            // Ingest payload
            $equityResult = $this->equityService->ingestSetuPayload($data, $userId);
            $mfResult = $this->MFService->ingestSetuPayload($data, $userId);

            Log::info("Setu data ingestion completed", [
                'equity' => $equityResult,
                'mutual_fund' => $mfResult
            ]);

            return true;

        } catch (\Throwable $e) {
            Log::error("Error fetching Setu user data", [
                'sessionId' => $sessionId,
                'message' => $e->getMessage()
            ]);
            return false;
        }
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
