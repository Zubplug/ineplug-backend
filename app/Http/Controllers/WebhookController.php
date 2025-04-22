<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Transaction;

class WebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $monnifySignature = $request->header('monnify-signature');
        $secretKey = env('MONNIFY_API_SECRET');

        $computedSignature = base64_encode(hash_hmac('sha512', $payload, $secretKey, true));
        if ($monnifySignature !== $computedSignature) {
            Log::channel('webhook')->warning("Invalid Monnify signature");
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $data = json_decode($payload, true);
        Log::channel('webhook')->info("✅ Monnify Webhook Received", $data);

        if (!isset($data['eventType']) || $data['eventType'] !== 'SUCCESSFUL_TRANSACTION') {
            return response()->json(['message' => 'Event ignored'], 200);
        }

        $eventData = $data['eventData'];
        $accountReference = $eventData['product']['reference'] ?? null;
        $amountPaid = $eventData['amountPaid'] ?? 0;
        $paymentReference = $eventData['paymentReference'] ?? null;

        if (!$accountReference || !$paymentReference) {
            return response()->json(['message' => 'Missing fields'], 422);
        }

        if (Transaction::where('reference', $paymentReference)->exists()) {
            Log::channel('webhook')->info("Duplicate transaction: {$paymentReference}");
            return response()->json(['message' => 'Duplicate transaction'], 200);
        }

        $user = User::where('account_reference', $accountReference)->first();
        if (!$user) {
            Log::channel('webhook')->error("User not found: {$accountReference}");
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->wallet_balance += $amountPaid;
        $user->save();

        // ✅ Auto-PND enforcement after credit
        $roleLimits = config("role_limits.{$user->role}");
        if (!is_null($roleLimits['daily']) && $user->wallet_balance > $roleLimits['daily']) {
            $user->pnd = true;
            $user->save();
            Log::channel('webhook')->warning("User {$user->id} auto-PND due to wallet balance exceeding daily role limit.");
        }

        Transaction::create([
            'user_id' => $user->id,
            'type' => 'funding',
            'category' => null,
            'reference' => $paymentReference,
            'status' => 'success',
            'amount' => $amountPaid,
            'recipient' => null,
            'gateway' => 'monnify',
            'narration' => 'Monnify Wallet Funding',
            'metadata' => $data
        ]);

        Log::channel('webhook')->info("Wallet funded successfully for user ID {$user->id}");

        return response()->json(['message' => 'Wallet funded'], 200);
    }
}
