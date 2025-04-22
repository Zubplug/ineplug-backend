<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\AddressVerification;

class KycComplianceController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->query('filter');
        $query = User::query();

        if ($filter === 'pnd') {
            $query->where('pnd', true);
        } elseif (in_array($filter, ['level_0', 'level_1', 'level_2'])) {
            $level = intval(str_replace('level_', '', $filter));
            $query->where('kyc_level', $level);
        }

        $users = $query->where('kyc_level', '<', 3)->orderByDesc('created_at')->get();

        $summary = [
            'pending' => AddressVerification::where('status', 'pending')->count(),
            'approved' => AddressVerification::where('status', 'approved')->count(),
            'rejected' => AddressVerification::where('status', 'rejected')->count(),
        ];

        return response()->json([
            'users' => $users,
            'summary' => $summary
        ]);
    }

    public function rejectAddress(Request $request, $id)
    {
        $request->validate(['reason' => 'required|string']);

        $verification = AddressVerification::where('id', $id)->where('status', 'pending')->firstOrFail();
        $verification->status = 'rejected';
        $verification->rejection_reason = $request->reason;
        $verification->save();

        return response()->json(['message' => 'Address verification rejected']);
    }

    public function approveAddress($id)
    {
        $verification = AddressVerification::where('id', $id)->where('status', 'pending')->firstOrFail();
        $user = $verification->user;

        $verification->status = 'approved';
        $verification->save();

        $user->address_verified = true;
        if ($user->kyc_level == 2) {
            $user->kyc_level = 3;
            $user->role = 'agent';
        }
        $user->save();

        return response()->json(['message' => 'Address verified and user upgraded to level 3']);
    }
}
