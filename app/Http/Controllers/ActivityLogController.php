<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ActivityLogController extends Controller
{
    /**
     * Display a listing of system activity logs.
     */
    public function index(Request $request)
    {
        abort_if(!auth()->user()->can('activity-log-list'), 403, 'Unauthorized action.');

        if ($request->ajax()) {
            $logs = ActivityLog::with('user');

            if ($request->filled('user_id')) {
                $logs->where('user_id', $request->input('user_id'));
            }

            if ($request->filled('module')) {
                $logs->where('module', $request->input('module'));
            }

            if ($request->filled('action')) {
                $logs->where('action', $request->input('action'));
            }

            return DataTables::of($logs)
                ->addColumn('user', function ($log) {
                    if ($log->user) {
                        return '<div class="d-flex align-items-center gap-2">
                            <span class="badge bg-light text-dark border"><i class="ti ti-user me-1"></i>' . e($log->user->name) . '</span>
                        </div>';
                    }
                    return '<span class="text-muted small">System / Guest</span>';
                })
                ->addColumn('module', function ($log) {
                    return '<span class="badge bg-light-primary text-primary font-monospace">' . e($log->module) . '</span>';
                })
                ->addColumn('action', function ($log) {
                    $badgeClass = 'bg-secondary';
                    if (str_contains($log->action, 'create')) $badgeClass = 'bg-success';
                    if (str_contains($log->action, 'update')) $badgeClass = 'bg-warning text-dark';
                    if (str_contains($log->action, 'delete')) $badgeClass = 'bg-danger';
                    if (str_contains($log->action, 'review') || str_contains($log->action, 'approve')) $badgeClass = 'bg-info';

                    return '<span class="badge ' . $badgeClass . '">' . e(ucfirst($log->action)) . '</span>';
                })
                ->addColumn('description', function ($log) {
                    return '<div class="fw-medium text-dark">' . e($log->description) . '</div>';
                })
                ->addColumn('created_at', function ($log) {
                    return '<span class="small text-secondary">' . $log->created_at->format('M d, Y h:i A') . '</span>';
                })
                ->rawColumns(['user', 'module', 'action', 'description', 'created_at'])
                ->make(true);
        }

        $users = User::orderBy('first_name')->orderBy('last_name')->get();
        $modules = ActivityLog::select('module')->distinct()->pluck('module');
        $actions = ActivityLog::select('action')->distinct()->pluck('action');

        return view('activity-logs.index', compact('users', 'modules', 'actions'));
    }
}
