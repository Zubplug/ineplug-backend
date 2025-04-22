<?php

namespace App\Http\Controllers;

use App\Models\AddressVerification;
use App\Models\User;
use Illuminate\Http\Request;

class AddressVerificationController extends Controller
{
    // Admin: View all address verifications
    public function index()
    {
        return AddressVerification::with('user')->latest()->get();
    }

    // Admin: Approve a verification
    public function approve($id)
    {
        $verification = AddressVerification::findOrFail($id);
        $verification->status = 'approved';
        $verification->save();

        $user = $verification->user;
        $user->address_verified = true;

        if ($user->bvn && $user->nin) {
            $user->kyc_level = 3;
            $user->role = 'agent';
        }

        $user->save();

        return response()->json(['message' => 'Verification approved and user upgraded.']);
    }

    // Admin: Reject a verification
    public function reject($id)
    {
        $verification = AddressVerification::findOrFail($id);
        $verification->status = 'rejected';
        $verification->save();

        return response()->json(['message' => 'Verification rejected.']);
    }

    // User: Submit address verification
    public function store(Request $request)
    {
        if ($request->user()->kyc_level < 2) {
            return response()->json(['message' => 'You must complete BVN + NIN verification before submitting address.'], 403);
        }

        $request->validate([
            'address' => 'required|string',
            'document' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048'
        ]);

        $path = $request->file('document')->store('address_verifications', 'public');

        $request->user()->addressVerification()->create([
            'address' => $request->address,
            'document_path' => $path,
            'status' => 'pending'
        ]);

        return response()->json(['message' => 'Submitted for review']);
    }

    // User: View own status
    public function status(Request $request)
    {
        return $request->user()->addressVerification;
    }
}
