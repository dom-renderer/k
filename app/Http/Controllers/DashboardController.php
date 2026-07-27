<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\CoatingCase;
use App\Models\CoatingCaseFile;
use App\Models\Equipment;
use App\Models\Sector;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display dynamic system metrics & workflow overview dashboard.
     */
    public function index()
    {
        $stats = [
            'total_cases' => CoatingCase::count(),
            'pending_cases' => CoatingCase::where('status', '!=', 'closed')->count(),
            'closed_cases' => CoatingCase::where('status', 'closed')->count(),
            'total_sectors' => Sector::count(),
            'total_equipment' => Equipment::count(),
            'total_users' => User::count(),
            'total_files' => CoatingCaseFile::count(),
            'level_1_cases' => CoatingCase::where('current_level', 1)->where('status', '!=', 'closed')->count(),
            'level_2_cases' => CoatingCase::where('current_level', 2)->where('status', '!=', 'closed')->count(),
            'level_3_cases' => CoatingCase::where('current_level', 3)->where('status', '!=', 'closed')->count(),
        ];

        $recentCases = CoatingCase::with(['sector', 'equipment', 'creator'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $recentLogs = ActivityLog::with('user')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $equipmentsSummary = Equipment::withCount('cases')
            ->orderBy('cases_count', 'desc')
            ->take(5)
            ->get();

        return view('dashboard', compact('stats', 'recentCases', 'recentLogs', 'equipmentsSummary'));
    }
}
