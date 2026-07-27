<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class EquipmentController extends Controller
{
    /**
     * Display a listing of the equipment or handle DataTables AJAX request.
     */
    public function index(Request $request)
    {
        abort_if(!auth()->user()->can('equipment-list'), 403, 'Unauthorized action.');

        if ($request->ajax()) {
            $equipment = Equipment::with(['creator', 'updater']);

            if ($request->input('has_photo') === 'yes') {
                $equipment->whereNotNull('photo')->where('photo', '!=', '');
            } elseif ($request->input('has_photo') === 'no') {
                $equipment->where(function ($q) {
                    $q->whereNull('photo')->orWhere('photo', '');
                });
            }

            if ($request->input('sku_status') === 'has_sku') {
                $equipment->whereNotNull('sku')->where('sku', '!=', '');
            } elseif ($request->input('sku_status') === 'no_sku') {
                $equipment->where(function ($q) {
                    $q->whereNull('sku')->orWhere('sku', '');
                });
            }

            return DataTables::of($equipment)
                ->addColumn('photo', function ($item) {
                    if ($item->photo_url) {
                        return '<img src="' . $item->photo_url . '" alt="' . e($item->name) . '" class="rounded border" style="width: 48px; height: 48px; object-fit: cover;">';
                    }
                    return '<div class="rounded border bg-light d-flex align-items-center justify-content-center text-muted" style="width: 48px; height: 48px;"><i class="ti ti-tools fs-4"></i></div>';
                })
                ->addColumn('name', function ($item) {
                    return '<div>
                        <div class="fw-semibold text-dark">' . e($item->name) . '</div>
                        <div class="small text-muted">' . e(\Illuminate\Support\Str::limit($item->description ?? 'No description', 40)) . '</div>
                    </div>';
                })
                ->addColumn('sku', function ($item) {
                    if ($item->sku) {
                        return '<span class="badge bg-light-primary text-primary font-monospace px-2 py-1">' . e($item->sku) . '</span>';
                    }
                    return '<span class="text-muted small">N/A</span>';
                })
                ->addColumn('added_by', function ($item) {
                    if ($item->creator) {
                        return '<span class="badge bg-light text-dark border"><i class="ti ti-user me-1"></i>' . e($item->creator->name) . '</span>';
                    }
                    return '<span class="text-muted small">System</span>';
                })
                ->addColumn('updated_by', function ($item) {
                    if ($item->updater) {
                        return '<span class="badge bg-light text-dark border"><i class="ti ti-user me-1"></i>' . e($item->updater->name) . '</span>';
                    }
                    return '<span class="text-muted small">-</span>';
                })
                ->addColumn('created_at', function ($item) {
                    return '<span class="small text-secondary">' . $item->created_at->format('M d, Y h:i A') . '</span>';
                })
                ->addColumn('actions', function ($item) {
                    $html = '';
                    if (auth()->user()->can('equipment-edit')) {
                        $html .= '<a href="' . route('equipment.edit', $item->id) . '" class="btn btn-sm btn-icon btn-light" title="Edit"><i class="ti ti-edit fs-5"></i></a>';
                    }
                    if (auth()->user()->can('equipment-delete')) {
                        $html .= ' <button type="button" class="btn btn-sm btn-icon btn-light text-danger delete-equipment-btn" data-id="' . $item->id . '" data-name="' . e($item->name) . '" title="Delete"><i class="ti ti-trash fs-5"></i></button>';
                    }
                    return $html ?: '<span class="text-muted small">None</span>';
                })
                ->rawColumns(['photo', 'name', 'sku', 'added_by', 'updated_by', 'created_at', 'actions'])
                ->make(true);
        }

        return view('equipment.index');
    }

    /**
     * Show the form for creating a new equipment.
     */
    public function create()
    {
        abort_if(!auth()->user()->can('equipment-create'), 403, 'Unauthorized action.');

        return view('equipment.create');
    }

    /**
     * Store a newly created equipment in storage.
     */
    public function store(Request $request)
    {
        abort_if(!auth()->user()->can('equipment-create'), 403, 'Unauthorized action.');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['nullable', 'string', 'max:255', 'unique:equipment,sku'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp,svg', 'max:2048'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('equipments', 'public');
        }

        $equipment = Equipment::create($validated);

        ActivityLogger::log('created', 'Equipment', 'Created new equipment: ' . $equipment->name, $equipment, [
            'name' => $equipment->name,
            'sku' => $equipment->sku,
        ]);

        return redirect()->route('equipment.index')->with('success', 'Equipment created successfully.');
    }

    /**
     * Show the form for editing the specified equipment.
     */
    public function edit(Equipment $equipment)
    {
        abort_if(!auth()->user()->can('equipment-edit'), 403, 'Unauthorized action.');

        return view('equipment.edit', compact('equipment'));
    }

    /**
     * Update the specified equipment in storage.
     */
    public function update(Request $request, Equipment $equipment)
    {
        abort_if(!auth()->user()->can('equipment-edit'), 403, 'Unauthorized action.');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['nullable', 'string', 'max:255', 'unique:equipment,sku,' . $equipment->id],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp,svg', 'max:2048'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);

        if ($request->hasFile('photo')) {
            if ($equipment->photo && Storage::disk('public')->exists($equipment->photo)) {
                Storage::disk('public')->delete($equipment->photo);
            }
            $validated['photo'] = $request->file('photo')->store('equipments', 'public');
        }

        $oldName = $equipment->name;
        $equipment->update($validated);

        ActivityLogger::log('updated', 'Equipment', 'Updated equipment: ' . $equipment->name, $equipment, [
            'old_name' => $oldName,
            'new_name' => $equipment->name,
            'sku' => $equipment->sku,
        ]);

        return redirect()->route('equipment.index')->with('success', 'Equipment updated successfully.');
    }

    /**
     * Remove the specified equipment from storage.
     */
    public function destroy(Equipment $equipment)
    {
        abort_if(!auth()->user()->can('equipment-delete'), 403, 'Unauthorized action.');

        $name = $equipment->name;
        
        if ($equipment->photo && Storage::disk('public')->exists($equipment->photo)) {
            Storage::disk('public')->delete($equipment->photo);
        }

        $equipment->delete();

        ActivityLogger::log('deleted', 'Equipment', 'Deleted equipment: ' . $name, null, [
            'name' => $name,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Equipment deleted successfully.',
        ]);
    }
}
