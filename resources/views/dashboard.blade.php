@extends('layouts.app')

@section('title', 'Dashboard - ' . \App\Models\Setting::get('app_title', 'InApp Inventory Dashboard'))

@section('content')
<div class="row">
  <div class="col-12">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="fs-3 mb-1">Coating Workflow Dashboard</h1>
        <p class="text-muted mb-0">Overview of active coating work cases, approval stages, and recent activity.</p>
      </div>
      @can('case-create')
        <a href="{{ route('cases.create') }}" class="btn btn-primary">
          <i class="ti ti-plus me-1"></i> New Coating Case
        </a>
      @endcan
    </div>
  </div>
</div>

<!-- Top KPI Cards -->
<div class="row g-3 mb-4">
  <div class="col-sm-6 col-lg-3">
    <div class="card p-3 bg-primary bg-opacity-10 border border-primary border-opacity-25 rounded-3 h-100">
      <div class="d-flex align-items-center gap-3">
        <div class="icon-shape icon-lg bg-primary text-white rounded-3 p-3 d-flex align-items-center justify-content-center">
          <i class="ti ti-folders fs-2"></i>
        </div>
        <div>
          <span class="text-muted fw-semibold small d-block mb-1">Total Cases</span>
          <h2 class="fw-bold mb-0 text-dark">{{ number_format($stats['total_cases']) }}</h2>
          <small class="text-primary fw-medium">{{ $stats['pending_cases'] }} active in workflow</small>
        </div>
      </div>
    </div>
  </div>

  <div class="col-sm-6 col-lg-3">
    <div class="card p-3 bg-warning bg-opacity-10 border border-warning border-opacity-25 rounded-3 h-100">
      <div class="d-flex align-items-center gap-3">
        <div class="icon-shape icon-lg bg-warning text-white rounded-3 p-3 d-flex align-items-center justify-content-center">
          <i class="ti ti-clock fs-2"></i>
        </div>
        <div>
          <span class="text-muted fw-semibold small d-block mb-1">In-Progress Cases</span>
          <h2 class="fw-bold mb-0 text-dark">{{ number_format($stats['pending_cases']) }}</h2>
          <small class="text-warning fw-medium">Pending 3-level review</small>
        </div>
      </div>
    </div>
  </div>

  <div class="col-sm-6 col-lg-3">
    <div class="card p-3 bg-success bg-opacity-10 border border-success border-opacity-25 rounded-3 h-100">
      <div class="d-flex align-items-center gap-3">
        <div class="icon-shape icon-lg bg-success text-white rounded-3 p-3 d-flex align-items-center justify-content-center">
          <i class="ti ti-circle-check fs-2"></i>
        </div>
        <div>
          <span class="text-muted fw-semibold small d-block mb-1">Closed & Approved</span>
          <h2 class="fw-bold mb-0 text-dark">{{ number_format($stats['closed_cases']) }}</h2>
          <small class="text-success fw-medium">Final approval granted</small>
        </div>
      </div>
    </div>
  </div>

  <div class="col-sm-6 col-lg-3">
    <div class="card p-3 bg-info bg-opacity-10 border border-info border-opacity-25 rounded-3 h-100">
      <div class="d-flex align-items-center gap-3">
        <div class="icon-shape icon-lg bg-info text-white rounded-3 p-3 d-flex align-items-center justify-content-center">
          <i class="ti ti-files fs-2"></i>
        </div>
        <div>
          <span class="text-muted fw-semibold small d-block mb-1">Backed Up Files</span>
          <h2 class="fw-bold mb-0 text-dark">{{ number_format($stats['total_files']) }}</h2>
          <small class="text-info fw-medium">Photos & documents</small>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Workflow Stages Distribution & Equipment Summary -->
<div class="row g-3 mb-4">
  <!-- Workflow Levels Breakdown -->
  <div class="col-lg-7">
    <div class="card h-100">
      <div class="card-header bg-light fw-bold d-flex justify-content-between align-items-center">
        <span><i class="ti ti-git-merge me-2"></i> Active Workflow Level Distribution</span>
        @can('case-list')
          <a href="{{ route('cases.index') }}" class="small text-primary">View All Cases</a>
        @endcan
      </div>
      <div class="card-body p-4">
        <div class="row text-center g-3">
          <div class="col-4">
            <div class="p-3 border rounded bg-light">
              <span class="badge bg-primary mb-2">Level 1</span>
              <h3 class="fw-bold mb-1">{{ $stats['level_1_cases'] }}</h3>
              <small class="text-muted">Pre-Coating</small>
            </div>
          </div>
          <div class="col-4">
            <div class="p-3 border rounded bg-light">
              <span class="badge bg-warning text-dark mb-2">Level 2</span>
              <h3 class="fw-bold mb-1">{{ $stats['level_2_cases'] }}</h3>
              <small class="text-muted">In-Progress</small>
            </div>
          </div>
          <div class="col-4">
            <div class="p-3 border rounded bg-light">
              <span class="badge bg-info text-white mb-2">Level 3</span>
              <h3 class="fw-bold mb-1">{{ $stats['level_3_cases'] }}</h3>
              <small class="text-muted">After-Coating</small>
            </div>
          </div>
        </div>

        <hr class="my-4">

        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h6 class="fw-bold mb-1">System Master Data Summary</h6>
            <small class="text-muted">Configured sectors, equipment, and system users</small>
          </div>
          <div class="d-flex gap-3 text-center">
            <div>
              <div class="fw-bold text-dark fs-5">{{ $stats['total_sectors'] }}</div>
              <small class="text-muted">Sectors</small>
            </div>
            <div>
              <div class="fw-bold text-dark fs-5">{{ $stats['total_equipment'] }}</div>
              <small class="text-muted">Equipment</small>
            </div>
            <div>
              <div class="fw-bold text-dark fs-5">{{ $stats['total_users'] }}</div>
              <small class="text-muted">Users</small>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Top Equipment Utilization -->
  <div class="col-lg-5">
    <div class="card h-100">
      <div class="card-header bg-light fw-bold">
        <i class="ti ti-tools me-2"></i> Equipment Utilization
      </div>
      <ul class="list-group list-group-flush">
        @forelse($equipmentsSummary as $eq)
          <li class="list-group-item d-flex justify-content-between align-items-center p-3">
            <div class="d-flex align-items-center gap-3">
              @if($eq->photo_url)
                <img src="{{ $eq->photo_url }}" class="rounded object-fit-cover" width="40" height="40" alt="">
              @else
                <div class="rounded bg-light text-primary d-flex align-items-center justify-content-center" style="width:40px; height:40px;">
                  <i class="ti ti-tools fs-5"></i>
                </div>
              @endif
              <div>
                <h6 class="mb-0 fw-semibold">{{ $eq->name }}</h6>
                <small class="text-muted">{{ $eq->sku ? 'SKU: ' . $eq->sku : 'No SKU' }}</small>
              </div>
            </div>
            <span class="badge bg-light-primary text-primary font-monospace">{{ $eq->cases_count }} Cases</span>
          </li>
        @empty
          <li class="list-group-item text-center text-muted py-4">No equipment data available.</li>
        @endforelse
      </ul>
    </div>
  </div>
</div>

<!-- Recent Cases & Activity Logs -->
<div class="row g-3">
  <!-- Recent Coating Cases -->
  <div class="col-lg-7">
    <div class="card h-100">
      <div class="card-header bg-light fw-bold d-flex justify-content-between align-items-center">
        <span><i class="ti ti-file-text me-2"></i> Recent Coating Cases</span>
        @can('case-list')
          <a href="{{ route('cases.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
        @endcan
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th>OA Number</th>
                <th>Sector</th>
                <th>Equipment</th>
                <th>Status</th>
                <th class="text-end">Action</th>
              </tr>
            </thead>
            <tbody>
              @forelse($recentCases as $case)
                <tr>
                  <td>
                    <a href="{{ route('cases.show', $case->id) }}" class="fw-semibold text-primary font-monospace">
                      {{ $case->oa_number }}
                    </a>
                  </td>
                  <td class="small">{{ $case->sector->title ?? 'N/A' }}</td>
                  <td class="small">{{ $case->equipment->name ?? 'N/A' }}</td>
                  <td>{!! $case->status_badge !!}</td>
                  <td class="text-end">
                    <a href="{{ route('cases.show', $case->id) }}" class="btn btn-sm btn-icon btn-light" title="View Details">
                      <i class="ti ti-eye"></i>
                    </a>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="text-center text-muted py-4">No coating cases created yet.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Recent System Activity Audit Logs -->
  <div class="col-lg-5">
    <div class="card h-100">
      <div class="card-header bg-light fw-bold d-flex justify-content-between align-items-center">
        <span><i class="ti ti-history me-2"></i> Recent Activity Logs</span>
        @can('activity-log-list')
          <a href="{{ route('activity-logs.index') }}" class="small text-primary">View Audit Trail</a>
        @endcan
      </div>
      <ul class="list-group list-group-flush">
        @forelse($recentLogs as $log)
          <li class="list-group-item p-3">
            <div class="d-flex justify-content-between align-items-start mb-1">
              <span class="fw-semibold small text-dark">{{ $log->user->name ?? 'System' }}</span>
              <small class="text-muted fs-xs">{{ $log->created_at->diffForHumans() }}</small>
            </div>
            <p class="small text-muted mb-0 text-truncate" title="{{ $log->description }}">
              {{ $log->description }}
            </p>
          </li>
        @empty
          <li class="list-group-item text-center text-muted py-4">No activity logs recorded yet.</li>
        @endforelse
      </ul>
    </div>
  </div>
</div>
@endsection
