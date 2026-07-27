@extends('layouts.app')

@section('title', 'Equipment - ' . \App\Models\Setting::get('app_title', 'InApp Inventory Dashboard'))

@section('content')
<div class="row">
  <div class="col-12">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="fs-3 mb-1">Equipment</h1>
        <p class="text-muted mb-0">Manage tools, machinery, SKUs, and assets.</p>
      </div>
      @can('equipment-create')
      <a href="{{ route('equipment.create') }}" class="btn btn-primary">
        <i class="ti ti-plus me-1"></i> Add Equipment
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
    <div class="accordion mb-4" id="equipmentFilterAccordion">
      <div class="accordion-item border shadow-sm">
        <h2 class="accordion-header" id="headingEquipmentFilter">
          <button class="accordion-button collapsed fw-semibold text-dark bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEquipmentFilter" aria-expanded="false" aria-controls="collapseEquipmentFilter">
            <i class="ti ti-filter me-2 text-primary"></i> Filter Equipment
          </button>
        </h2>
        <div id="collapseEquipmentFilter" class="accordion-collapse collapse" aria-labelledby="headingEquipmentFilter" data-bs-parent="#equipmentFilterAccordion">
          <div class="accordion-body p-4 bg-white">
            <form id="equipmentFilterForm">
              <div class="row g-3">
                <div class="col-md-6">
                  <label for="filter_has_photo" class="form-label fw-semibold small">Photo Status</label>
                  <select class="form-select form-select-sm" id="filter_has_photo" name="has_photo">
                    <option value="">All Equipment</option>
                    <option value="yes">With Photo Uploaded</option>
                    <option value="no">Without Photo</option>
                  </select>
                </div>

                <div class="col-md-6">
                  <label for="filter_sku_status" class="form-label fw-semibold small">SKU Status</label>
                  <select class="form-select form-select-sm" id="filter_sku_status" name="sku_status">
                    <option value="">All Equipment</option>
                    <option value="has_sku">With SKU Code</option>
                    <option value="no_sku">Without SKU Code</option>
                  </select>
                </div>

                <div class="col-12 d-flex justify-content-end gap-2 mt-3">
                  <button type="button" class="btn btn-sm btn-light" id="resetEquipmentFilterBtn">
                    <i class="ti ti-rotate-clockwise me-1"></i> Reset
                  </button>
                  <button type="button" class="btn btn-sm btn-primary" id="applyEquipmentFilterBtn">
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
          <table id="equipmentTable" class="table table-hover align-middle w-100">
            <thead>
              <tr>
                <th style="width: 60px;">Photo</th>
                <th>Equipment Name</th>
                <th>SKU</th>
                <th>Added By</th>
                <th>Updated By</th>
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
    var table = $('#equipmentTable').DataTable({
      processing: true,
      serverSide: true,
      ajax: {
        url: "{{ route('equipment.index') }}",
        data: function(d) {
          d.has_photo = $('#filter_has_photo').val();
          d.sku_status = $('#filter_sku_status').val();
        }
      },
      columns: [
        { data: 'photo', name: 'photo', orderable: false, searchable: false },
        { data: 'name', name: 'name' },
        { data: 'sku', name: 'sku' },
        { data: 'added_by', name: 'added_by', orderable: false, searchable: false },
        { data: 'updated_by', name: 'updated_by', orderable: false, searchable: false },
        { data: 'created_at', name: 'created_at' },
        { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-end' }
      ],
      order: [[5, 'desc']],
      language: {
        searchPlaceholder: "Search equipment or SKU...",
        search: ""
      }
    });

    $('#applyEquipmentFilterBtn').on('click', function() {
      table.ajax.reload();
    });

    $('#resetEquipmentFilterBtn').on('click', function() {
      $('#equipmentFilterForm')[0].reset();
      table.ajax.reload();
    });

    $(document).on('click', '.delete-equipment-btn', function() {
      var equipmentId = $(this).data('id');
      var equipmentName = $(this).data('name');
      
      Swal.fire({
        title: 'Delete Equipment?',
        text: 'Are you sure you want to delete "' + equipmentName + '"? This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, Delete'
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: "{{ url('equipment') }}/" + equipmentId,
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
              Swal.fire('Error!', 'An error occurred while deleting the equipment.', 'error');
            }
          });
        }
      });
    });
  });
</script>
@endpush
