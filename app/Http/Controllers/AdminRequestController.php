<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RequestSubmission;
use Illuminate\Support\Facades\Storage;

class AdminRequestController extends Controller
{
    public function pending()
{
    $requests = \App\Models\RequestSubmission::where('status', 'awaiting-verification')
                ->with(['service', 'assignedPartner'])
                ->get();

    return response()->json($requests);
}


    public function verify(Request $request)
    {
        $request->validate([
            'request_id' => 'required|exists:request_submissions,id',
            'status' => 'required|in:completed,failed,cancelled',
            'admin_note' => 'nullable|string',
            'upload' => 'nullable|file|max:10240',
        ]);

        $submission = RequestSubmission::find($request->request_id);
        $submission->status = $request->status;
        $submission->admin_note = $request->admin_note;

        if ($request->hasFile('upload')) {
            $path = $request->file('upload')->store('results');
            $submission->admin_upload = $path;
        }

        // Auto-credit partner if completed
        if ($request->status === 'completed') {
            $partnerId = $submission->assigned_partner_id;
            $service = Service::find($submission->service_id);
            $commission = $service->partner_commission ?? 0;
        
            // Credit partner wallet
            $wallet = PartnerWallet::firstOrCreate(['partner_id' => $partnerId]);
            $wallet->balance += $commission;
            $wallet->save();
        }

        $submission->save();

        return response()->json(['message' => 'Request status updated.']);
    }
}

