<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\VtuService;

class VtuServiceController extends Controller
{
    private $headers;

    public function __construct()
    {
        $this->headers = [
            'user-id' => env('VTPASS_EMAIL'),
            'api-key' => env('VTPASS_PASSWORD'),
            'Content-Type' => 'application/json',
        ];
    }

    public function fetchVtuServices($serviceId)
    {
        $res = Http::withHeaders($this->headers)
            ->get("https://sandbox.vtpass.com/api/service-variations?serviceID={$serviceId}");
        
        $vtpassData = $res->json();
        $stored = VtuService::where('service_id', $serviceId)->get();

        return response()->json([
            'from_api' => $vtpassData['content']['variations'] ?? [],
            'customized' => $stored,
        ]);
    }

    public function updateService(Request $request)
    {
        $service = VtuService::updateOrCreate(
            [
                'service_id' => $request->service_id,
                'variation_code' => $request->variation_code
            ],
            [
                'name' => $request->name,
                'price' => $request->price,
                'agent_commission' => $request->agent_commission,
                'aggregator_commission' => $request->aggregator_commission,
                'active' => $request->active
            ]
        );

        return response()->json(['status' => 'updated', 'data' => $service]);
    }

    public function toggleService(Request $request)
    {
        $service = VtuService::where('id', $request->id)->first();
        if ($service) {
            $service->active = !$service->active;
            $service->save();
            return response()->json(['status' => 'toggled', 'active' => $service->active]);
        }
        return response()->json(['error' => 'not found'], 404);
    }

    public function getBalance()
    {
        $res = Http::withHeaders($this->headers)->post("https://sandbox.vtpass.com/api/balance");
        return response()->json($res->json());
    }
}

