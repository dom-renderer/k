@extends('layouts.app')

@section('title', 'Users - ' . \App\Models\Setting::get('app_title', 'InApp Inventory Dashboard'))

@section('content')
<div class="row">
  <div class="col-12">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="fs-3 mb-1">Users</h1>
        <p class="mb-0 text-muted">Manage system users and role permissions</p>
      </div>
      @can('user-create')
      <div>
        <a href="{{ route('users.create') }}" class="btn btn-primary">
          <i class="ti ti-plus me-1"></i> Add User
        </a>
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

<!-- Filter Accordion (Default Collapsed) -->
<div class="accordion mb-4" id="usersFilterAccordion">
  <div class="accordion-item border shadow-sm">
    <h2 class="accordion-header" id="headingUsersFilter">
      <button class="accordion-button collapsed fw-semibold text-dark bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapseUsersFilter" aria-expanded="false" aria-controls="collapseUsersFilter">
        <i class="ti ti-filter me-2 text-primary"></i> Filter Users
      </button>
    </h2>
    <div id="collapseUsersFilter" class="accordion-collapse collapse" aria-labelledby="headingUsersFilter" data-bs-parent="#usersFilterAccordion">
      <div class="accordion-body p-4 bg-white">
        <form id="userFilterForm">
          <div class="row g-3">
            <div class="col-md-6">
              <label for="filter_role" class="form-label fw-semibold small">Filter by Role</label>
              <select class="form-select form-select-sm" id="filter_role" name="role">
                <option value="">All Roles</option>
                @foreach($roles as $role)
                  <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                @endforeach
              </select>
            </div>

            <div class="col-12 d-flex justify-content-end gap-2 mt-3">
              <button type="button" class="btn btn-sm btn-light" id="resetUserFilterBtn">
                <i class="ti ti-rotate-clockwise me-1"></i> Reset
              </button>
              <button type="button" class="btn btn-sm btn-primary" id="applyUserFilterBtn">
                <i class="ti ti-filter me-1"></i> Apply Filter
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-12">
    <div class="card table-responsive p-3">
      <table id="users-table" class="table mb-0 text-nowrap table-hover w-100">
        <thead class="table-light border-light">
          <tr>
            <th>User</th>
            <th>Username</th>
            <th>Email</th>
            <th>Phone Number</th>
            <th>Roles</th>
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
  var table = $('#users-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
      url: "{{ route('users.index') }}",
      data: function(d) {
        d.role = $('#filter_role').val();
      }
    },
    columns: [
      { data: 'name', name: 'first_name' },
      { data: 'username', name: 'username' },
      { data: 'email', name: 'email' },
      { data: 'phone_number', name: 'phone_number' },
      { data: 'roles', name: 'roles', orderable: false, searchable: false },
      { data: 'actions', name: 'actions', orderable: false, searchable: false }
    ],
    language: {
      search: "_INPUT_",
      searchPlaceholder: "Search users..."
    }
  });

  $('#applyUserFilterBtn').on('click', function() {
    table.ajax.reload();
  });

  $('#resetUserFilterBtn').on('click', function() {
    $('#userFilterForm')[0].reset();
    table.ajax.reload();
  });

  // Delete User Handler
  $(document).on('click', '.delete-user-btn', function(e) {
    e.preventDefault();
    var userId = $(this).data('id');
    var userName = $(this).data('name');

    Swal.fire({
      title: 'Are you sure?',
      text: "You are about to delete user '" + userName + "'. This action cannot be undone!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#ea580c',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Yes, delete user'
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: '/users/' + userId,
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
