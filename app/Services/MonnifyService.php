<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class MonnifyService
{
    public function authenticate()
    {
        $apiKey = env('MONNIFY_API_KEY');
        $secret = env('MONNIFY_SECRET_KEY');
        $baseUrl = env('MONNIFY_BASE_URL');

        $encoded = base64_encode($apiKey . ':' . $secret);

        $res = Http::withHeaders([
            'Authorization' => 'Basic ' . $encoded
        ])->post($baseUrl . '/api/v1/auth/login');

        return $res['responseBody']['accessToken'] ?? null;
    }

    public function createReservedAccount($user)
    {
        $token = $this->authenticate();
        $baseUrl = env('MONNIFY_BASE_URL');
        $accountRef = 'INEPLUG-' . uniqid();

        $res = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->post($baseUrl . '/api/v2/bank-transfer/reserved-accounts', [
            "accountReference" => $accountRef,
            "accountName" => $user->name,
            "currencyCode" => "NGN",
            "contractCode" => env('MONNIFY_CONTRACT_CODE'),
            "customerEmail" => $user->email,
            "customerName" => $user->name,
            "bvn" => $user->bvn,
            "getAllAvailableBanks" => true
        ]);

        return $res['responseBody'] ?? null;
    }
}
 
