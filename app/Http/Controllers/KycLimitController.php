<?php

namespace App\Http\Controllers;

use App\Models\KycLimit;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class KycLimitController extends Controller
{
    public function index()
    {
        return KycLimit::orderBy('kyc_level')->get();
    }

    public function update(Request $request)
    {
        $request->validate([
            'limits' => 'required|array',
        ]);

        foreach ($request->limits as $limit) {
            KycLimit::updateOrCreate(
                ['kyc_level' => $limit['kyc_level']],
                [
                    'daily_limit' => $limit['daily_limit'] ?? 0,
                    'monthly_limit' => $limit['monthly_limit'] ?? 0,
                    'lifetime_limit' => $limit['lifetime_limit'] ?? 0,
                ]
            );
        }

        return response()->json(['message' => 'Limits updated successfully.']);
    }
}
