@extends('layouts.app')

@section('title', 'Roles - InApp Inventory Dashboard')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="fs-3 mb-1">Roles & Permissions</h1>
        <p class="mb-0">Manage system roles, permissions, and access controls</p>
      </div>
      @can('role-create')
      <div>
        <a href="{{ route('roles.create') }}" class="btn btn-primary">Add Role</a>
      </div>
      @endcan
    </div>
  </div>
</div>

@if (session('success'))
  <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
    <i class="ti ti-circle-check me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
@endif

<div class="row">
  <div class="col-12">
    <div class="card table-responsive p-3">
      <table id="roles-table" class="table mb-0 text-nowrap table-hover w-100">
        <thead class="table-light border-light">
          <tr>
            <th>Role Name</th>
            <th>Permissions</th>
            <th>Assigned Users</th>
            <th width="80">Action</th>
          </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
  var table = $('#roles-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('roles.index') }}",
    columns: [
      { data: 'name', name: 'name' },
      { data: 'permissions_count', name: 'permissions_count', orderable: false, searchable: false },
      { data: 'users_count', name: 'users_count', orderable: false, searchable: false },
      { data: 'actions', name: 'actions', orderable: false, searchable: false }
    ],
    language: {
      search: "_INPUT_",
      searchPlaceholder: "Search roles..."
    }
  });

  // Delete Role Handler
  $(document).on('click', '.delete-role-btn', function(e) {
    e.preventDefault();
    var roleId = $(this).data('id');
    var roleName = $(this).data('name');

    Swal.fire({
      title: 'Are you sure?',
      text: "You are about to delete role '" + roleName + "'. This action cannot be undone!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#ea580c',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Yes, delete role'
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: '/roles/' + roleId,
          type: 'DELETE',
          data: {
            _token: $('meta[name="csrf-token"]').attr('content')
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
            var msg = xhr.responseJSON ? xhr.responseJSON.message : 'An error occurred while deleting.';
            Swal.fire('Error!', msg, 'error');
          }
        });
      }
    });
  });
});
</script>
@endpush
