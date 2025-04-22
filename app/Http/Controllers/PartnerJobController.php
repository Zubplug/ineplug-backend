<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RequestSubmission;
use App\Models\PartnerAssignment;
use Illuminate\Support\Facades\Auth;

class PartnerJobController extends Controller
{
    public function availableJobs()
    {
        // Show unassigned requests
        $requests = RequestSubmission::where('status', 'pending')
            ->whereNull('assigned_partner_id')
            ->with('service')
            ->get();

        return response()->json($requests);
    }

    public function requestAssignment(Request $request)
    {
        $partnerId = Auth::id(); // Or pass from frontend

        $submission = RequestSubmission::findOrFail($request->request_id);

        // Ensure it's not already assigned
        if ($submission->assigned_partner_id) {
            return response()->json(['message' => 'Job already assigned'], 400);
        }

        // Check partner performance (dummy logic for now)
        $hasGoodPerformance = true;

        if ($hasGoodPerformance) {
            $submission->assigned_partner_id = $partnerId;
            $submission->status = 'in-progress';
            $submission->save();

            return response()->json(['message' => 'Job assigned successfully']);
        }

        return response()->json(['message' => 'You are not eligible to pick this job at the moment.'], 403);
    }
}

