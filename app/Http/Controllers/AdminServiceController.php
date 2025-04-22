<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\ServiceForm;
use Illuminate\Support\Str;

class AdminServiceController extends Controller
{
    /**
     * Get all top-level services with nested subcategories and sub-subcategories
     */
    public function all()
    {
        return Service::whereNull('parent_id')
            ->with([
                'children' => function ($query) {
                    $query->with('children');
                }
            ])
            ->get();
    }

    /**
     * Create a new service
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'slug' => 'required|string|unique:services,slug',
            'category' => 'nullable|string',
            'subcategory' => 'nullable|string',
            'price' => 'nullable|numeric',
            'commission' => 'nullable|numeric',
            'access_right' => 'nullable|string',
            'service_type' => 'nullable|string',
            'parent_id' => 'nullable|exists:services,id',
        ]);

        $service = Service::create($validated);

        return response()->json([
            'message' => 'Service created successfully',
            'service' => $service
        ], 201);
    }

    /**
     * Load dynamic form for a service
     */
    public function getForm($slug)
    {
        $service = Service::where('slug', $slug)->firstOrFail();
        $form = ServiceForm::where('service_id', $service->id)->first();

        return response()->json([
            'id' => $service->id,
            'name' => $service->name,
            'fields' => $form ? $form->fields : [],
        ]);
    }

    /**
     * Save or update dynamic form fields
     */
    public function saveForm(Request $request)
    {
        $validated = $request->validate([
            'slug' => 'required|string',
            'name' => 'required|string',
            'fields' => 'required|array',
        ]);

        $service = Service::where('slug', $validated['slug'])->first();

        if (!$service) {
            $service = Service::create([
                'name' => $validated['name'],
                'slug' => Str::slug($validated['name']),
                'parent_id' => $request->parent_id ?? null,
            ]);
        }

        ServiceForm::updateOrCreate(
            ['service_id' => $service->id],
            ['fields' => $validated['fields']]
        );

        return response()->json([
            'message' => 'Form saved successfully',
            'service' => $service,
        ]);
    }

    /**
     * Get all sub-sub categories of a service (optional)
     */
    public function getSubSubCategories($id)
    {
        $parent = Service::with('children.children')->findOrFail($id);
        return $parent->children->flatMap(function ($child) {
            return $child->children;
        });
    }
}
