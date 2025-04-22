<?php

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

public function saveForm(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string',
        'slug' => 'required|string',
        'level' => 'required|in:main,sub,sub-sub',
        'parent_id' => 'nullable|exists:services,id',
        'price' => 'nullable|numeric',
        'fee_type' => 'nullable|in:naira,percent',
        'status' => 'required|in:active,inactive',
        'partner_commission' => 'nullable|string',
        'agent_commission' => 'nullable|string',
        'aggregator_commission' => 'nullable|string',
        'fields' => 'nullable',
        'icon' => 'nullable|file|mimes:png,jpg,jpeg,svg',
    ]);

    $service = Service::updateOrCreate(
        ['slug' => $validated['slug']],
        [
            'name' => $validated['name'],
            'level' => $validated['level'],
            'parent_id' => $validated['parent_id'] ?? null,
            'price' => $validated['price'],
            'fee_type' => $validated['fee_type'],
            'status' => $validated['status'],
            'partner_commission' => $validated['partner_commission'],
            'agent_commission' => $validated['agent_commission'],
            'aggregator_commission' => $validated['aggregator_commission'],
            'form_fields' => $validated['level'] === 'sub-sub' ? $validated['fields'] : null,
        ]
    );

    // Handle icon upload if present
    if ($request->hasFile('icon') && $validated['level'] === 'main') {
        $icon = $request->file('icon')->store('service-icons', 'public');
        $service->icon = $icon;
        $service->save();
    }

    return response()->json(['message' => 'Service saved', 'service' => $service]);
}
