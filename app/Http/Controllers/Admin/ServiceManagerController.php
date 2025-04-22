<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MainService;
use App\Models\Subcategory;
use App\Models\SubSubcategory;
use Illuminate\Support\Str;

class ServiceManagerController extends Controller
{
    public function index()
    {
        $services = MainService::with('subcategories.subSubcategories')->get();
        return response()->json($services);
    }

    public function store(Request $request)
    {
        $type = $request->type;

        if ($type === 'main') {
            $service = MainService::create([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'icon' => $request->icon,
                'status' => true
            ]);
        }

        if ($type === 'sub') {
            $service = Subcategory::create([
                'main_service_id' => $request->main_service_id,
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'type' => $request->type_mode,
                'is_standalone' => $request->is_standalone,
                'price' => $request->price,
                'fee_type' => $request->fee_type,
                'commissions' => $request->commissions,
                'status' => true
            ]);
        }

        if ($type === 'subsub') {
            $service = SubSubcategory::create([
                'subcategory_id' => $request->subcategory_id,
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'type' => $request->type_mode,
                'price' => $request->price,
                'fee_type' => $request->fee_type,
                'commissions' => $request->commissions,
                'status' => true
            ]);
        }

        return response()->json(['success' => true, 'data' => $service]);
    }

    public function update(Request $request, $type, $id)
    {
        $model = $this->getModel($type)::findOrFail($id);
        $model->update($request->all());
        return response()->json(['success' => true]);
    }

    public function destroy($type, $id)
    {
        $model = $this->getModel($type)::findOrFail($id);
        $model->delete();
        return response()->json(['success' => true]);
    }

    public function toggleStatus($type, $id)
    {
        $model = $this->getModel($type)::findOrFail($id);
        $model->status = !$model->status;
        $model->save();
        return response()->json(['success' => true, 'status' => $model->status]);
    }

    private function getModel($type)
    {
        return match ($type) {
            'main' => MainService::class,
            'sub' => Subcategory::class,
            'subsub' => SubSubcategory::class,
        };
    }
}
