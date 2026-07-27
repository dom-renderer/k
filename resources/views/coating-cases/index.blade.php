@extends('layouts.app')

@section('title', 'Coating Cases - ' . \App\Models\Setting::get('app_title', 'InApp Inventory Dashboard'))

@section('content')
<div class="row">
  <div class="col-12">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="fs-3 mb-1">Coating Cases</h1>
        <p class="text-muted mb-0">Track coating work documents, photos, and 3-level approval workflows.</p>
      </div>
      @can('case-create')
      <a href="{{ route('cases.create') }}" class="btn btn-primary">
        <i class="ti ti-plus me-1"></i> New Coating Case
      </a>
      @endcan
    </div>

    @if (session('success'))
      <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        <i class="ti ti-check me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    @endif

    <!-- Filter Accordion (Default Collapsed) -->
    <div class="accordion mb-4" id="casesFilterAccordion">
      <div class="accordion-item border shadow-sm">
        <h2 class="accordion-header" id="headingCasesFilter">
          <button class="accordion-button collapsed fw-semibold text-dark bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCasesFilter" aria-expanded="false" aria-controls="collapseCasesFilter">
            <i class="ti ti-filter me-2 text-primary"></i> Filter Coating Cases
          </button>
        </h2>
        <div id="collapseCasesFilter" class="accordion-collapse collapse" aria-labelledby="headingCasesFilter" data-bs-parent="#casesFilterAccordion">
          <div class="accordion-body p-4 bg-white">
            <form id="caseFilterForm">
              <div class="row g-3">
                <div class="col-md-3">
                  <label for="filter_sector_id" class="form-label fw-semibold small">Sector</label>
                  <select class="form-select select2-filter" id="filter_sector_id" name="sector_id" data-placeholder="All Sectors">
                    <option value=""></option>
                    @foreach($sectors as $sector)
                      <option value="{{ $sector->id }}">{{ $sector->title }}</option>
                    @endforeach
                  </select>
                </div>

                <div class="col-md-3">
                  <label for="filter_equipment_id" class="form-label fw-semibold small">Equipment</label>
                  <select class="form-select select2-filter" id="filter_equipment_id" name="equipment_id" data-placeholder="All Equipment">
                    <option value=""></option>
                    @foreach($equipments as $eq)
                      <option value="{{ $eq->id }}">{{ $eq->name }}</option>
                    @endforeach
                  </select>
                </div>

                <div class="col-md-3">
                  <label for="filter_current_level" class="form-label fw-semibold small">Current Level</label>
                  <select class="form-select form-select-sm" id="filter_current_level" name="current_level">
                    <option value="">All Levels</option>
                    <option value="1">Level 1</option>
                    <option value="2">Level 2</option>
                    <option value="3">Level 3</option>
                  </select>
                </div>

                <div class="col-md-3">
                  <label for="filter_status" class="form-label fw-semibold small">Status</label>
                  <select class="form-select form-select-sm" id="filter_status" name="status">
                    <option value="">All Statuses</option>
                    <option value="level_1_pending">Level 1 Pending</option>
                    <option value="level_1_rejected">Level 1 Rejected</option>
                    <option value="level_2_pending">Level 2 Pending</option>
                    <option value="level_2_rejected">Level 2 Rejected</option>
                    <option value="level_3_pending">Level 3 Pending</option>
                    <option value="level_3_rejected">Level 3 Rejected</option>
                    <option value="closed">Closed & Approved</option>
                  </select>
                </div>

                <div class="col-12 d-flex justify-content-end gap-2 mt-3">
                  <button type="button" class="btn btn-sm btn-light" id="resetCaseFilterBtn">
                    <i class="ti ti-rotate-clockwise me-1"></i> Reset
                  </button>
                  <button type="button" class="btn btn-sm btn-primary" id="applyCaseFilterBtn">
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
          <table id="casesTable" class="table table-hover align-middle w-100">
            <thead>
              <tr>
                <th>Case ID</th>
                <th>OA Number</th>
                <th>Sector</th>
                <th>Equipment</th>
                <th>Current Level</th>
                <th>Status</th>
                <th>Created At</th>
                <th class="text-end">Actions</th>
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
    $('.select2-filter').select2({
      theme: 'bootstrap-5',
      width: '100%',
      placeholder: function() {
        return $(this).data('placeholder');
      },
      allowClear: true
    });

    var table = $('#casesTable').DataTable({
      processing: true,
      serverSide: true,
      ajax: {
        url: "{{ route('cases.index') }}",
        data: function(d) {
          d.sector_id = $('#filter_sector_id').val();
          d.equipment_id = $('#filter_equipment_id').val();
          d.current_level = $('#filter_current_level').val();
          d.status = $('#filter_status').val();
        }
      },
      columns: [
        { data: 'case_number', name: 'case_number' },
        { data: 'oa_number', name: 'oa_number' },
        { data: 'sector', name: 'sector.title' },
        { data: 'equipment', name: 'equipment.name' },
        { data: 'level', name: 'current_level', orderable: false },
        { data: 'status', name: 'status' },
        { data: 'created_at', name: 'created_at' },
        { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-end' }
      ],
      order: [[6, 'desc']],
      language: {
        searchPlaceholder: "Search by OA Number, Case ID...",
        search: ""
      }
    });

    $('#applyCaseFilterBtn').on('click', function() {
      table.ajax.reload();
    });

    $('#resetCaseFilterBtn').on('click', function() {
      $('#caseFilterForm')[0].reset();
      $('.select2-filter').val(null).trigger('change');
      table.ajax.reload();
    });

    $(document).on('click', '.delete-case-btn', function() {
      var caseId = $(this).data('id');
      var oaNumber = $(this).data('oa');

      Swal.fire({
        title: 'Delete Coating Case?',
        text: 'Are you sure you want to delete case with OA Number "' + oaNumber + '"? All attached files and review logs will be permanently deleted.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, Delete Case'
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: "{{ url('cases') }}/" + caseId,
            type: 'DELETE',
            data: {
              _token: "{{ csrf_token() }}"
            },
            success: function(response) {
              if (response.success) {
                Swal.fire('Deleted!', response.message, 'success');
                table.ajax.reload();
              } else {
                Swal.fire('Error!', response.message, 'error');
              }
            },
            error: function(xhr) {
              Swal.fire('Error!', 'An error occurred while deleting the case.', 'error');
            }
          });
        }
      });
    });
  });
</script>
@endpush
