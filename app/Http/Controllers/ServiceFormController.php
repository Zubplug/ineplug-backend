<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\ServiceForm;
use App\Models\RequestSubmission;

class ServiceFormController extends Controller
{
    // Optional list all services
    public function listAll()
    {
        return Service::orderBy('created_at', 'desc')->get();
    }

    // ✅ Load form fields for a user based on slug
    public function loadForm($slug)
    {
        $service = Service::where('slug', $slug)->firstOrFail();

        $form = ServiceForm::where('service_id', $service->id)->first();

        return response()->json([
            'title' => $service->name,
            'fields' => $form ? $form->fields : [],
            'price' => $service->price,
        ]);
    }

    // ✅ Handle user submission
    public function submitForm(Request $request)
    {
        $validated = $request->validate([
            'service_slug' => 'required|string|exists:services,slug',
            'user_id' => 'required|exists:users,id',
            'data' => 'required|array',
        ]);

        $service = Service::where('slug', $validated['service_slug'])->firstOrFail();

        // Optionally: Debit wallet here if needed

        $submission = RequestSubmission::create([
            'user_id' => $validated['user_id'],
            'service_id' => $service->id,
            'data' => json_encode($validated['data']),
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Request submitted successfully',
            'request_id' => $submission->id
        ]);
    }
}
