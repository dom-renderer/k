<?php

namespace App\Http\Controllers;

use App\Models\Sector;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class SectorController extends Controller
{
    /**
     * Display a listing of the sectors or handle DataTables AJAX request.
     */
    public function index(Request $request)
    {
        abort_if(!auth()->user()->can('sector-list'), 403, 'Unauthorized action.');

        if ($request->ajax()) {
            $sectors = Sector::with(['creator', 'updater']);

            return DataTables::of($sectors)
                ->addColumn('title', function ($sector) {
                    return '<div class="d-flex align-items-center gap-2">
                        <span class="badge bg-light-primary text-primary p-2 rounded"><i class="ti ti-category fs-5"></i></span>
                        <div>
                            <div class="fw-semibold text-dark">' . e($sector->title) . '</div>
                            <div class="small text-muted">' . e(\Illuminate\Support\Str::limit($sector->description ?? 'No description', 40)) . '</div>
                        </div>
                    </div>';
                })
                ->addColumn('added_by', function ($sector) {
                    if ($sector->creator) {
                        return '<span class="badge bg-light text-dark border"><i class="ti ti-user me-1"></i>' . e($sector->creator->name) . '</span>';
                    }
                    return '<span class="text-muted small">System</span>';
                })
                ->addColumn('updated_by', function ($sector) {
                    if ($sector->updater) {
                        return '<span class="badge bg-light text-dark border"><i class="ti ti-user me-1"></i>' . e($sector->updater->name) . '</span>';
                    }
                    return '<span class="text-muted small">-</span>';
                })
                ->addColumn('created_at', function ($sector) {
                    return '<span class="small text-secondary">' . $sector->created_at->format('M d, Y h:i A') . '</span>';
                })
                ->addColumn('actions', function ($sector) {
                    $html = '';
                    if (auth()->user()->can('sector-edit')) {
                        $html .= '<a href="' . route('sectors.edit', $sector->id) . '" class="btn btn-sm btn-icon btn-light" title="Edit"><i class="ti ti-edit fs-5"></i></a>';
                    }
                    if (auth()->user()->can('sector-delete')) {
                        $html .= ' <button type="button" class="btn btn-sm btn-icon btn-light text-danger delete-sector-btn" data-id="' . $sector->id . '" data-title="' . e($sector->title) . '" title="Delete"><i class="ti ti-trash fs-5"></i></button>';
                    }
                    return $html ?: '<span class="text-muted small">None</span>';
                })
                ->rawColumns(['title', 'added_by', 'updated_by', 'created_at', 'actions'])
                ->make(true);
        }

        return view('sectors.index');
    }

    /**
     * Show the form for creating a new sector.
     */
    public function create()
    {
        abort_if(!auth()->user()->can('sector-create'), 403, 'Unauthorized action.');

        return view('sectors.create');
    }

    /**
     * Store a newly created sector in storage.
     */
    public function store(Request $request)
    {
        abort_if(!auth()->user()->can('sector-create'), 403, 'Unauthorized action.');

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255', 'unique:sectors,title'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $sector = Sector::create($validated);

        ActivityLogger::log('created', 'Sectors', 'Created new sector: ' . $sector->title, $sector, [
            'title' => $sector->title,
        ]);

        return redirect()->route('sectors.index')->with('success', 'Sector created successfully.');
    }

    /**
     * Show the form for editing the specified sector.
     */
    public function edit(Sector $sector)
    {
        abort_if(!auth()->user()->can('sector-edit'), 403, 'Unauthorized action.');

        return view('sectors.edit', compact('sector'));
    }

    /**
     * Update the specified sector in storage.
     */
    public function update(Request $request, Sector $sector)
    {
        abort_if(!auth()->user()->can('sector-edit'), 403, 'Unauthorized action.');

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255', 'unique:sectors,title,' . $sector->id],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $oldTitle = $sector->title;
        $sector->update($validated);

        ActivityLogger::log('updated', 'Sectors', 'Updated sector: ' . $sector->title, $sector, [
            'old_title' => $oldTitle,
            'new_title' => $sector->title,
        ]);

        return redirect()->route('sectors.index')->with('success', 'Sector updated successfully.');
    }

    /**
     * Remove the specified sector from storage.
     */
    public function destroy(Sector $sector)
    {
        abort_if(!auth()->user()->can('sector-delete'), 403, 'Unauthorized action.');

        $title = $sector->title;
        $sector->delete();

        ActivityLogger::log('deleted', 'Sectors', 'Deleted sector: ' . $title, null, [
            'title' => $title,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Sector deleted successfully.',
        ]);
    }
}
