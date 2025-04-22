<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\VtuService;

class VtuAdminSyncController extends Controller
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

    public function sync(Request $request)
    {
        $services = ["mtn", "airtel", "etisalat", "glo", "gotv", "dstv", "startimes", "waec", "abuja-electric"];
        $synced = [];

        foreach ($services as $serviceId) {
            $response = Http::withHeaders($this->headers)
                ->get("https://sandbox.vtpass.com/api/service-variations?serviceID={$serviceId}");

            if ($response->successful()) {
                $variations = $response->json("content.variations") ?? [];

                foreach ($variations as $item) {
                    $vtu = VtuService::updateOrCreate(
                        [
                            'service_id' => $serviceId,
                            'variation_code' => $item['variation_code'] ?? null
                        ],
                        [
                            'name' => $item['name'] ?? '',
                            'amount' => $item['amount'] ?? 0,
                            'active' => true,
                            'agent_commission' => 0,
                            'aggregator_commission' => 0,
                        ]
                    );
                    $synced[] = $vtu;
                }
            } else {
                Log::warning("Failed to fetch variations for {$serviceId}", [$response->body()]);
            }
        }

        return response()->json([
            'message' => 'VTU Services synced successfully',
            'count' => count($synced),
        ]);
    }
}
