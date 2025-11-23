<?php

namespace App\Services\Investments;
use App\Models\SetuToken;
use Illuminate\Support\Facades\Http;

class TokenService
{

    private $clientId;
    private $clientSecret;

    public function __construct()
    {
        $this->clientId = config('services.setu.client_id');
        $this->clientSecret = config('services.setu.client_secret');
    }

    public function generateToken()
    {
        $token = SetuToken::where('expires_at', '>', now())->first();
        if ($token) {
            return ['success' => 200, 'token' => $token];
        }
        // dd($this->clientSecret);

        $response = Http::withHeaders(['Content-Type' => 'application/json'])
            ->post('https://orgservice-prod.setu.co/v1/users/login', [
                'clientId' => $this->clientId,
                'grant_type' => 'client_credentials',
                'secret' => $this->clientSecret,
            ]);
            
        $data = $response->json();
        
        if ($response->successful() && isset($data['access_token'])) {
            $token = SetuToken::create([
                'token' => $data['access_token'],
                'expires_at' => now()->addMinutes(20)
            ]);

            return ['success' => 200, 'token' => $token];
        }
        return ['success' => 400, 'message' => 'Failed to generate token', 'data' => $data];
    }
}