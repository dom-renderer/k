<?php

namespace App\Http\Controllers;

use App\Models\CoatingCase;
use App\Models\CoatingCaseFile;
use App\Models\CoatingCaseLevelLog;
use App\Models\Equipment;
use App\Models\Sector;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CoatingCaseController extends Controller
{
    /**
     * Display a listing of coating cases or handle DataTables AJAX request.
     */
    public function index(Request $request)
    {
        abort_if(!auth()->user()->can('case-list'), 403, 'Unauthorized action.');

        if ($request->ajax()) {
            $cases = CoatingCase::with(['sector', 'equipment', 'creator', 'updater']);

            if ($request->filled('sector_id')) {
                $cases->where('sector_id', $request->input('sector_id'));
            }

            if ($request->filled('equipment_id')) {
                $cases->where('equipment_id', $request->input('equipment_id'));
            }

            if ($request->filled('current_level')) {
                $cases->where('current_level', $request->input('current_level'));
            }

            if ($request->filled('status')) {
                $cases->where('status', $request->input('status'));
            }

            return datatables()->of($cases)
                ->addColumn('case_number', function ($case) {
                    return '<a href="' . route('cases.show', $case->id) . '" class="fw-semibold text-primary">' . e($case->case_number) . '</a>';
                })
                ->addColumn('oa_number', function ($case) {
                    return '<span class="badge bg-light text-dark border font-monospace px-2 py-1"><i class="ti ti-hash me-1"></i>' . e($case->oa_number) . '</span>';
                })
                ->addColumn('sector', function ($case) {
                    return e($case->sector->title ?? 'N/A');
                })
                ->addColumn('equipment', function ($case) {
                    return e($case->equipment->name ?? 'N/A');
                })
                ->addColumn('level', function ($case) {
                    if ($case->status === 'closed') {
                        return '<span class="badge bg-success">Closed</span>';
                    }
                    return '<span class="badge bg-light-primary text-primary">Level ' . $case->current_level . '</span>';
                })
                ->addColumn('status', function ($case) {
                    return $case->status_badge;
                })
                ->addColumn('created_at', function ($case) {
                    return '<span class="small text-secondary">' . $case->created_at->format('M d, Y h:i A') . '</span>';
                })
                ->addColumn('actions', function ($case) {
                    $html = '<div class="d-flex align-items-center gap-2">';
                    $html .= '<a href="' . route('cases.show', $case->id) . '" class="btn btn-sm btn-icon btn-light" title="View & Workflow"><i class="ti ti-eye fs-5"></i></a>';

                    if (auth()->user()->can('case-edit') && !$case->is_closed) {
                        $html .= '<a href="' . route('cases.edit', $case->id) . '" class="btn btn-sm btn-icon btn-light" title="Edit"><i class="ti ti-edit fs-5"></i></a>';
                    }

                    if (auth()->user()->can('case-delete')) {
                        $html .= '<button type="button" class="btn btn-sm btn-icon btn-light text-danger delete-case-btn" data-id="' . $case->id . '" data-oa="' . e($case->oa_number) . '" title="Delete"><i class="ti ti-trash fs-5"></i></button>';
                    }

                    $html .= '</div>';
                    return $html;
                })
                ->rawColumns(['case_number', 'oa_number', 'sector', 'equipment', 'level', 'status', 'created_at', 'actions'])
                ->make(true);
        }

        $sectors = Sector::orderBy('title')->get();
        $equipments = Equipment::orderBy('name')->get();

        return view('coating-cases.index', compact('sectors', 'equipments'));
    }

    /**
     * Check if OA Number already exists (AJAX endpoint).
     */
    public function checkOaNumber(Request $request)
    {
        $oaNumber = trim($request->query('oa_number', ''));
        $excludeId = $request->query('exclude_id');

        $query = CoatingCase::where('oa_number', $oaNumber);
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        $exists = $query->exists();

        return response()->json([
            'exists' => $exists,
            'message' => $exists ? 'OA Number "' . e($oaNumber) . '" already exists in the system.' : 'OA Number is available.'
        ]);
    }

    /**
     * Generate a unique OA Number (AJAX endpoint).
     */
    public function generateOaNumber()
    {
        abort_if(!auth()->user()->can('case-create') && !auth()->user()->can('case-edit'), 403, 'Unauthorized action.');

        do {
            $number = 'OA-' . str_pad(mt_rand(100000, 999999), 6, '0', STR_PAD_LEFT);
        } while (CoatingCase::where('oa_number', $number)->exists());

        return response()->json([
            'success' => true,
            'oa_number' => $number,
        ]);
    }

    /**
     * Show the form for creating a new coating case.
     */
    public function create()
    {
        abort_if(!auth()->user()->can('case-create'), 403, 'Unauthorized action.');

        $sectors = Sector::orderBy('title')->get();
        $equipments = Equipment::orderBy('name')->get();

        return view('coating-cases.create', compact('sectors', 'equipments'));
    }

    /**
     * Store a newly created coating case in storage.
     */
    public function store(Request $request)
    {
        abort_if(!auth()->user()->can('case-create'), 403, 'Unauthorized action.');

        $validated = $request->validate([
            'oa_number' => ['required', 'string', 'max:255', 'unique:coating_cases,oa_number'],
            'sector_id' => ['required', 'exists:sectors,id'],
            'equipment_id' => ['required', 'exists:equipment,id'],
            'other_information' => ['nullable', 'string', 'max:5000'],
            'file_paths' => ['nullable', 'array'],
        ]);

        // Generate auto case number
        $lastId = CoatingCase::max('id') ?? 0;
        $caseNumber = 'CASE-' . date('Y') . '-' . str_pad($lastId + 1, 5, '0', STR_PAD_LEFT);

        $case = CoatingCase::create([
            'case_number' => $caseNumber,
            'oa_number' => trim($validated['oa_number']),
            'sector_id' => $validated['sector_id'],
            'equipment_id' => $validated['equipment_id'],
            'other_information' => $validated['other_information'] ?? null,
            'current_level' => 1,
            'status' => 'level_1_pending',
        ]);

        // Attach uploaded Dropzone temp files if present
        if (!empty($request->input('uploaded_files'))) {
            $filesData = is_array($request->input('uploaded_files')) ? $request->input('uploaded_files') : json_decode($request->input('uploaded_files'), true);
            if (is_array($filesData)) {
                foreach ($filesData as $fileItem) {
                    if (isset($fileItem['file_path'])) {
                        CoatingCaseFile::create([
                            'coating_case_id' => $case->id,
                            'level' => 1,
                            'file_category' => $fileItem['category'] ?? 'photo',
                            'file_path' => $fileItem['file_path'],
                            'file_name' => $fileItem['file_name'] ?? basename($fileItem['file_path']),
                            'file_size' => $fileItem['file_size'] ?? 0,
                            'file_type' => $fileItem['file_type'] ?? 'application/octet-stream',
                        ]);
                    }
                }
            }
        }

        // Log level log & activity log
        CoatingCaseLevelLog::create([
            'coating_case_id' => $case->id,
            'level' => 1,
            'action' => 'submitted',
            'remarks' => 'Initial coating case created and Level 1 details submitted.',
            'user_id' => auth()->id(),
        ]);

        ActivityLogger::log('created', 'Coating Cases', 'Created new coating case: ' . $case->oa_number, $case, [
            'case_number' => $case->case_number,
            'oa_number' => $case->oa_number,
        ]);

        return redirect()->route('cases.show', $case->id)->with('success', 'Coating Case created successfully.');
    }

    /**
     * Dropzone AJAX upload handler.
     */
    public function uploadFile(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'max:25600'], // 25MB max
            'case_id' => ['nullable', 'exists:coating_cases,id'],
            'level' => ['nullable', 'integer', 'in:1,2,3'],
            'category' => ['nullable', 'string'],
        ]);

        $file = $request->file('file');
        $path = $file->store('coating_cases/' . date('Y/m'), 'public');

        $data = [
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'file_type' => $file->getClientMimeType(),
            'file_url' => asset('storage/' . $path),
            'category' => $request->input('category', 'photo'),
        ];

        // If uploaded directly within an existing case view
        if ($request->filled('case_id')) {
            $case = CoatingCase::findOrFail($request->input('case_id'));
            abort_if($case->is_closed, 403, 'Closed cases cannot be modified.');

            $level = $request->input('level', $case->current_level);
            $caseFile = CoatingCaseFile::create([
                'coating_case_id' => $case->id,
                'level' => $level,
                'file_category' => $data['category'],
                'file_path' => $path,
                'file_name' => $data['file_name'],
                'file_size' => $data['file_size'],
                'file_type' => $data['file_type'],
            ]);

            $data['file_id'] = $caseFile->id;

            ActivityLogger::log('uploaded_file', 'Coating Cases', 'Uploaded file "' . $data['file_name'] . '" for Level ' . $level . ' in case ' . $case->oa_number, $case);
        }

        return response()->json([
            'success' => true,
            'file' => $data
        ]);
    }

    /**
     * Delete file from case.
     */
    public function deleteFile(CoatingCaseFile $file)
    {
        $case = $file->coatingCase;
        if ($case && $case->is_closed) {
            return response()->json(['success' => false, 'message' => 'Cannot delete files from a closed case.'], 403);
        }

        if (Storage::disk('public')->exists($file->file_path)) {
            Storage::disk('public')->delete($file->file_path);
        }

        $file->delete();

        ActivityLogger::log('deleted_file', 'Coating Cases', 'Deleted file "' . $file->file_name . '" from case ' . ($case->oa_number ?? 'N/A'), $case);

        return response()->json(['success' => true, 'message' => 'File deleted successfully.']);
    }

    /**
     * Download case file.
     */
    public function downloadFile(CoatingCaseFile $file)
    {
        abort_if(!auth()->user()->can('case-download'), 403, 'Unauthorized action.');

        if (!Storage::disk('public')->exists($file->file_path)) {
            abort(404, 'File not found on server.');
        }

        ActivityLogger::log('downloaded_file', 'Coating Cases', 'Downloaded file "' . $file->file_name . '" from case ' . ($file->coatingCase->oa_number ?? 'N/A'), $file->coatingCase);

        return Storage::disk('public')->download($file->file_path, $file->file_name);
    }

    /**
     * Display the specified coating case workflow, details, files, and review timeline.
     */
    public function show(CoatingCase $case)
    {
        abort_if(!auth()->user()->can('case-list'), 403, 'Unauthorized action.');

        $case->load(['sector', 'equipment', 'files.creator', 'logs.user', 'creator', 'updater', 'closer']);

        $level1Files = $case->files->where('level', 1);
        $level2Files = $case->files->where('level', 2);
        $level3Files = $case->files->where('level', 3);

        return view('coating-cases.show', compact('case', 'level1Files', 'level2Files', 'level3Files'));
    }

    /**
     * Review Level (Approve or Reject with Rejection Level Reset logic).
     */
    public function reviewLevel(Request $request, CoatingCase $case)
    {
        abort_if(!auth()->user()->can('case-approve'), 403, 'Unauthorized action.');
        abort_if($case->is_closed, 403, 'Case is already closed.');

        $validated = $request->validate([
            'action' => ['required', 'in:approve,reject'],
            'remarks' => ['nullable', 'string', 'max:2000'],
            'reset_to_level' => ['nullable', 'integer', 'in:1,2,3'],
        ]);

        $currentLevel = $case->current_level;
        $action = $validated['action'];
        $remarks = $validated['remarks'] ?? null;
        $resetToLevel = $validated['reset_to_level'] ?? null;

        if ($action === 'approve') {
            if ($currentLevel == 1) {
                $case->update([
                    'current_level' => 2,
                    'status' => 'level_2_pending',
                ]);
                $logMsg = 'Approved Level 1 and advanced case to Level 2.';
            } elseif ($currentLevel == 2) {
                $case->update([
                    'current_level' => 3,
                    'status' => 'level_3_pending',
                ]);
                $logMsg = 'Approved Level 2 and advanced case to Level 3.';
            } elseif ($currentLevel == 3) {
                $case->update([
                    'status' => 'closed',
                    'closed_at' => now(),
                    'closed_by' => auth()->id(),
                ]);
                $logMsg = 'Approved Level 3. Coating Case successfully CLOSED.';
            }
        } else {
            // Rejection Logic
            if ($currentLevel == 1) {
                $resetTo = 1;
                $case->update([
                    'current_level' => 1,
                    'status' => 'level_1_rejected',
                ]);
                $logMsg = 'Rejected Level 1. Returned for changes at Level 1.';
            } elseif ($currentLevel == 2) {
                $resetTo = in_array($resetToLevel, [1, 2]) ? $resetToLevel : 1;
                $case->update([
                    'current_level' => $resetTo,
                    'status' => 'level_' . $resetTo . '_rejected',
                ]);
                $logMsg = 'Rejected Level 2. Reset back to Level ' . $resetTo . '.';
            } elseif ($currentLevel == 3) {
                $resetTo = in_array($resetToLevel, [1, 2, 3]) ? $resetToLevel : 1;
                $case->update([
                    'current_level' => $resetTo,
                    'status' => 'level_' . $resetTo . '_rejected',
                ]);
                $logMsg = 'Rejected Level 3. Reset back to Level ' . $resetTo . '.';
            }
        }

        CoatingCaseLevelLog::create([
            'coating_case_id' => $case->id,
            'level' => $currentLevel,
            'action' => $action === 'approve' ? 'approved' : 'rejected',
            'reset_to_level' => $action === 'reject' ? ($resetTo ?? $currentLevel) : null,
            'remarks' => $remarks ?: $logMsg,
            'user_id' => auth()->id(),
        ]);

        ActivityLogger::log('review_' . $action, 'Coating Cases', 'Level ' . $currentLevel . ' ' . $action . 'd for case ' . $case->oa_number . '. ' . $logMsg, $case, [
            'action' => $action,
            'remarks' => $remarks,
            'reset_to_level' => $resetTo ?? null,
        ]);

        return redirect()->route('cases.show', $case->id)->with('success', 'Level ' . $currentLevel . ' review recorded successfully.');
    }

    /**
     * Reopen a closed coating case and set to target level.
     */
    public function reopen(Request $request, CoatingCase $case)
    {
        abort_if(!auth()->user()->can('case-approve') && !auth()->user()->can('case-edit'), 403, 'Unauthorized action.');
        abort_if(!$case->is_closed, 400, 'Only closed cases can be reopened.');

        $validated = $request->validate([
            'target_level' => ['required', 'integer', 'in:1,2,3'],
            'remarks' => ['nullable', 'string', 'max:2000'],
        ]);

        $targetLevel = (int) $validated['target_level'];
        $remarks = $validated['remarks'] ?? 'Reopened closed case and set to Level ' . $targetLevel . '.';

        $case->update([
            'current_level' => $targetLevel,
            'status' => 'level_' . $targetLevel . '_pending',
            'closed_at' => null,
            'closed_by' => null,
        ]);

        CoatingCaseLevelLog::create([
            'coating_case_id' => $case->id,
            'level' => $targetLevel,
            'action' => 'reopened',
            'reset_to_level' => $targetLevel,
            'remarks' => $remarks,
            'user_id' => auth()->id(),
        ]);

        ActivityLogger::log('reopened', 'Coating Cases', 'Reopened case ' . $case->oa_number . ' and reset to Level ' . $targetLevel . '.', $case, [
            'target_level' => $targetLevel,
            'remarks' => $remarks,
        ]);

        return redirect()->route('cases.show', $case->id)->with('success', 'Coating Case reopened successfully and set to Level ' . $targetLevel . '.');
    }

    /**
     * Show edit form.
     */
    public function edit(CoatingCase $case)
    {
        abort_if(!auth()->user()->can('case-edit'), 403, 'Unauthorized action.');
        abort_if($case->is_closed, 403, 'Closed cases cannot be edited.');

        $sectors = Sector::orderBy('title')->get();
        $equipments = Equipment::orderBy('name')->get();

        return view('coating-cases.edit', compact('case', 'sectors', 'equipments'));
    }

    /**
     * Update specified coating case.
     */
    public function update(Request $request, CoatingCase $case)
    {
        abort_if(!auth()->user()->can('case-edit'), 403, 'Unauthorized action.');
        abort_if($case->is_closed, 403, 'Closed cases cannot be modified.');

        $validated = $request->validate([
            'oa_number' => ['required', 'string', 'max:255', 'unique:coating_cases,oa_number,' . $case->id],
            'sector_id' => ['required', 'exists:sectors,id'],
            'equipment_id' => ['required', 'exists:equipment,id'],
            'other_information' => ['nullable', 'string', 'max:5000'],
        ]);

        $oldOa = $case->oa_number;
        $case->update([
            'oa_number' => trim($validated['oa_number']),
            'sector_id' => $validated['sector_id'],
            'equipment_id' => $validated['equipment_id'],
            'other_information' => $validated['other_information'] ?? null,
        ]);

        ActivityLogger::log('updated', 'Coating Cases', 'Updated coating case details for OA: ' . $case->oa_number, $case, [
            'old_oa' => $oldOa,
            'new_oa' => $case->oa_number,
        ]);

        return redirect()->route('cases.show', $case->id)->with('success', 'Coating Case updated successfully.');
    }

    /**
     * Delete coating case.
     */
    public function destroy(CoatingCase $case)
    {
        abort_if(!auth()->user()->can('case-delete'), 403, 'Unauthorized action.');

        $oaNumber = $case->oa_number;

        // Delete all associated files on disk
        foreach ($case->files as $file) {
            if (Storage::disk('public')->exists($file->file_path)) {
                Storage::disk('public')->delete($file->file_path);
            }
        }

        $case->delete();

        ActivityLogger::log('deleted', 'Coating Cases', 'Deleted coating case: ' . $oaNumber, null, [
            'oa_number' => $oaNumber,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Coating Case deleted successfully.',
        ]);
    }
}
