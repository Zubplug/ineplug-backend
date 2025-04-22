<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Services\MonnifyService;

class VirtualAccountController extends Controller
{
    public function generate(Request $request)
    {
        $user = Auth::user();

        if ($user->virtual_account_number) {
            return response()->json(['message' => 'Account already generated'], 400);
        }

        $request->validate([
            'type' => 'required|in:bvn,nin',
            'value' => 'required|string|size:11'
        ]);

        $identifier = $request->value;
        $type = $request->type;

        // Save BVN or NIN
        if ($type === 'bvn') {
            $user->bvn = $identifier;
        } else {
            $user->nin = $identifier;
        }

        $accountReference = 'INEPLUG-' . uniqid();
        $token = app(MonnifyService::class)->authenticate();

        $payload = [
            "accountReference" => $accountReference,
            "accountName" => $user->name,
            "currencyCode" => "NGN",
            "contractCode" => env('MONNIFY_CONTRACT_CODE'),
            "customerEmail" => $user->email,
            "customerName" => $user->name,
            "bvn" => $identifier,
            "getAllAvailableBanks" => true
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->post(env('MONNIFY_BASE_URL') . '/api/v2/bank-transfer/reserved-accounts', $payload);

        if (!$response->ok() || !isset($response['responseBody']['accounts'][0])) {
            return response()->json(['message' => 'Failed to generate account'], 500);
        }

        $account = $response['responseBody']['accounts'][0];

        $user->virtual_account_number = $account['accountNumber'];
        $user->virtual_account_bank = $account['bankName'];
        $user->account_reference = $accountReference;
        $user->kyc_level = 1;
        $user->save();

        return response()->json([
            'message' => 'Virtual account generated successfully',
            'account' => $account
        ]);
    }
}
