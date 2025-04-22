<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Transaction;

class VtpassController extends Controller
{
    private $headers;

    public function __construct()
    {
        $this->headers = [
            'api-key' => env('VTPASS_PASSWORD'),
            'public-key' => env('VTPASS_EMAIL'),
            'Content-Type' => 'application/json'
        ];
    }

    public function airtime(Request $request)
    {
        return $this->pay($request);
    }

    public function data(Request $request)
    {
        return $this->pay($request);
    }

    public function electricity(Request $request)
    {
        return $this->pay($request);
    }

    public function tvSubscription(Request $request)
    {
        return $this->pay($request);
    }

    public function education(Request $request)
    {
        return $this->pay($request);
    }

    public function insurance(Request $request)
    {
        return $this->pay($request);
    }

    private function pay(Request $request)
    {
        $user = $request->user();
        $amount = $request->amount;
        $reference = $request->request_id ?? uniqid('vtu-');

        if ($user->wallet_balance < $amount) {
            return response()->json(['message' => 'Insufficient wallet balance'], 400);
        }

        // Deduct wallet
        $user->wallet_balance -= $amount;
        $user->save();

        // Log transaction
        Transaction::create([
            'user_id' => $user->id,
            'type' => 'vtu',
            'category' => $request->serviceID ?? 'unknown',
            'reference' => $reference,
            'status' => 'pending',
            'amount' => $amount,
            'recipient' => $request->phone ?? $request->smartcard_number ?? $request->meter_number ?? 'N/A',
            'metadata' => $request->all(),
        ]);

        // Send request to VTpass
        $response = Http::withHeaders($this->headers)
            ->post("https://sandbox.vtpass.com/api/pay", array_merge($request->all(), ['request_id' => $reference]));

            $status = ($response['code'] ?? '') === '000' ? 'successful' : 'failed';

            // Refund if failed
            if ($status === 'failed') {
                $user->wallet_balance += $amount;
                $user->save();
            }
            
            // Update transaction
            Transaction::where('reference', $reference)->update([
                'status' => $status,
                'metadata' => json_encode($response->json()),
            ]);
            
        return response()->json($response->json());
    }

    public function serviceVariations($serviceID)
    {
        $response = Http::withHeaders($this->headers)
            ->get("https://sandbox.vtpass.com/api/service-variations?serviceID={$serviceID}");

        return response()->json($response->json());
    }

    public function verifyMeter(Request $request)
    {
        $response = Http::withHeaders($this->headers)
            ->post("https://sandbox.vtpass.com/api/merchant-verify", [
                "billersCode" => $request->billersCode,
                "serviceID" => $request->serviceID,
                "type" => $request->type,
            ]);

        return response()->json($response->json());
    }

    public function verifySmartCard(Request $request)
    {
        $response = Http::withHeaders($this->headers)
            ->post("https://sandbox.vtpass.com/api/merchant-verify", [
                "billersCode" => $request->billersCode,
                "serviceID" => $request->serviceID,
            ]);

        return response()->json($response->json());
    }

    public function requery(Request $request)
    {
        $response = Http::withHeaders($this->headers)->post("https://sandbox.vtpass.com/api/requery", [
            'request_id' => $request->request_id,
        ]);

        // Update transaction if found
        if ($response->ok()) {
            Transaction::where('reference', $request->request_id)->update([
                'status' => $response['content']['transactions']['status'] ?? 'unknown',
                'metadata' => json_encode($response->json()),
            ]);
        }

        return response()->json($response->json());
    }

    public function callback(Request $request)
    {
        Log::info('VTpass Webhook:', [$request->all()]);

        Transaction::where('reference', $request->request_id)->update([
            'status' => $request->status,
            'metadata' => json_encode($request->all()),
        ]);

        return response()->json(['status' => 'received'], 200);
    }
}
