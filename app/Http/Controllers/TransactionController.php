<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Helpers\KycLimitHelper;

class TransactionController extends Controller
{
    public function index()
    {
        return response()->json(Transaction::with('user')->latest()->get());
    }

    public function show($reference)
    {
        $txn = Transaction::with('user')->where('reference', $reference)->firstOrFail();
        return response()->json($txn);
    }

    public function requery(Request $request)
    {
        $reference = $request->reference;

        $response = Http::withHeaders([
            'api-key' => env('VTPASS_PASSWORD'),
            'public-key' => env('VTPASS_EMAIL'),
            'Content-Type' => 'application/json',
        ])->post('https://sandbox.vtpass.com/api/requery', [
            'request_id' => $reference
        ]);

        if ($response->ok()) {
            Transaction::where('reference', $reference)->update([
                'status' => $response['content']['transactions']['status'] ?? 'unknown',
                'metadata' => $response->json(),
            ]);
        }

        return response()->json(['message' => 'Requery completed', 'data' => $response->json()]);
    }

    public function manualRefund(Request $request)
    {
        $txn = Transaction::where('reference', $request->reference)->firstOrFail();

        if ($txn->status === 'Refunded') {
            return response()->json(['message' => 'Already refunded'], 400);
        }

        $user = User::find($txn->user_id);
        $user->wallet_balance += $txn->amount;
        $user->save();

        $txn->update([
            'status' => 'Refunded',
        ]);

        return response()->json(['message' => 'Manual refund completed']);
    }

    // Example debit transaction entry point
    public function performDebit(Request $request)
    {
        $user = $request->user();
        $amount = $request->amount;

        // ✅ KYC Limit Check
        $check = KycLimitHelper::check($user, $amount);
        if (!$check['status']) {
            return response()->json(['message' => $check['message']], 403);
        }

        // ✅ Proceed with transaction logic (e.g., create record, notify, etc.)
        $txn = Transaction::create([
            'user_id' => $user->id,
            'type' => 'debit',
            'category' => $request->category ?? 'general',
            'reference' => uniqid('TXN_'),
            'status' => 'pending',
            'amount' => $amount,
            'recipient' => $request->recipient ?? '',
            'metadata' => $request->metadata ?? [],
        ]);

        return response()->json(['message' => 'Transaction queued', 'txn' => $txn]);
    }
}
