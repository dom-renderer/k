@extends('layouts.app')

@section('title', 'Activity Audit Logs - ' . \App\Models\Setting::get('app_title', 'InApp Inventory Dashboard'))

@section('content')
<div class="row">
  <div class="col-12">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="fs-3 mb-1">System Activity Audit Logs</h1>
        <p class="text-muted mb-0">Centralized log of user actions, file uploads, reviews, and administrative changes.</p>
      </div>
    </div>

    <!-- Filter Accordion (Default Collapsed) -->
    <div class="accordion mb-4" id="activityLogsFilterAccordion">
      <div class="accordion-item border shadow-sm">
        <h2 class="accordion-header" id="headingFilter">
          <button class="accordion-button collapsed fw-semibold text-dark bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFilter" aria-expanded="false" aria-controls="collapseFilter">
            <i class="ti ti-filter me-2 text-primary"></i> Filter Audit Logs
          </button>
        </h2>
        <div id="collapseFilter" class="accordion-collapse collapse" aria-labelledby="headingFilter" data-bs-parent="#activityLogsFilterAccordion">
          <div class="accordion-body p-4 bg-white">
            <form id="filterForm">
              <div class="row g-3">
                <div class="col-md-4">
                  <label for="filter_user_id" class="form-label fw-semibold small">Filter by User</label>
                  <select class="form-select form-select-sm" id="filter_user_id" name="user_id">
                    <option value="">All Users</option>
                    @foreach($users as $user)
                      <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->username }})</option>
                    @endforeach
                  </select>
                </div>

                <div class="col-md-4">
                  <label for="filter_module" class="form-label fw-semibold small">Filter by Module</label>
                  <select class="form-select form-select-sm" id="filter_module" name="module">
                    <option value="">All Modules</option>
                    @foreach($modules as $module)
                      <option value="{{ $module }}">{{ $module }}</option>
                    @endforeach
                  </select>
                </div>

                <div class="col-md-4">
                  <label for="filter_action" class="form-label fw-semibold small">Filter by Action</label>
                  <select class="form-select form-select-sm" id="filter_action" name="action">
                    <option value="">All Actions</option>
                    @foreach($actions as $act)
                      <option value="{{ $act }}">{{ ucfirst($act) }}</option>
                    @endforeach
                  </select>
                </div>

                <div class="col-12 d-flex justify-content-end gap-2 mt-3">
                  <button type="button" class="btn btn-sm btn-light" id="resetFilterBtn">
                    <i class="ti ti-rotate-clockwise me-1"></i> Reset
                  </button>
                  <button type="button" class="btn btn-sm btn-primary" id="applyFilterBtn">
                    <i class="ti ti-filter me-1"></i> Apply Filter
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-body p-4">
        <div class="table-responsive">
          <table id="activityLogsTable" class="table table-hover align-middle w-100">
            <thead>
              <tr>
                <th>User</th>
                <th>Module</th>
                <th>Action</th>
                <th>Description</th>
                <th>Timestamp</th>
              </tr>
            </thead>
            <tbody>
              <!-- DataTables AJAX -->
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  $(document).ready(function() {
    var table = $('#activityLogsTable').DataTable({
      processing: true,
      serverSide: true,
      ajax: {
        url: "{{ route('activity-logs.index') }}",
        data: function(d) {
          d.user_id = $('#filter_user_id').val();
          d.module = $('#filter_module').val();
          d.action = $('#filter_action').val();
        }
      },
      columns: [
        { data: 'user', name: 'user.name', orderable: false },
        { data: 'module', name: 'module' },
        { data: 'action', name: 'action' },
        { data: 'description', name: 'description' },
        { data: 'created_at', name: 'created_at' }
      ],
      order: [[4, 'desc']],
      language: {
        searchPlaceholder: "Search logs...",
        search: ""
      }
    });

    $('#applyFilterBtn').on('click', function() {
      table.ajax.reload();
    });

    $('#resetFilterBtn').on('click', function() {
      $('#filterForm')[0].reset();
      table.ajax.reload();
    });
  });
</script>
@endpush
