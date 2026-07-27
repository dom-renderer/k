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
      ajax: "{{ route('equipment.index') }}",
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
