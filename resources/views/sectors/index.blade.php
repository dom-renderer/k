@extends('layouts.app')

@section('title', 'Sectors - ' . \App\Models\Setting::get('app_title', 'InApp Inventory Dashboard'))

@section('content')
<div class="row">
  <div class="col-12">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="fs-3 mb-1">Sectors</h1>
        <p class="text-muted mb-0">Manage industry sectors, audit trails, and classifications.</p>
      </div>
      @can('sector-create')
      <a href="{{ route('sectors.create') }}" class="btn btn-primary">
        <i class="ti ti-plus me-1"></i> Add Sector
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
          <table id="sectorsTable" class="table table-hover align-middle w-100">
            <thead>
              <tr>
                <th>Sector Name</th>
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
    var table = $('#sectorsTable').DataTable({
      processing: true,
      serverSide: true,
      ajax: "{{ route('sectors.index') }}",
      columns: [
        { data: 'title', name: 'title' },
        { data: 'added_by', name: 'added_by', orderable: false, searchable: false },
        { data: 'updated_by', name: 'updated_by', orderable: false, searchable: false },
        { data: 'created_at', name: 'created_at' },
        { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-end' }
      ],
      order: [[3, 'desc']],
      language: {
        searchPlaceholder: "Search sectors...",
        search: ""
      }
    });

    $(document).on('click', '.delete-sector-btn', function() {
      var sectorId = $(this).data('id');
      var sectorTitle = $(this).data('title');
      
      Swal.fire({
        title: 'Delete Sector?',
        text: 'Are you sure you want to delete "' + sectorTitle + '"? This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, Delete'
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: "{{ url('sectors') }}/" + sectorId,
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
              Swal.fire('Error!', 'An error occurred while deleting the sector.', 'error');
            }
          });
        }
      });
    });
  });
</script>
@endpush
