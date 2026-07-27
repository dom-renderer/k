@extends('layouts.app')

@section('title', 'Edit Role - InApp Inventory Dashboard')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
      <div>
        <h1 class="fs-3 mb-1">Edit Role</h1>
        <p class="mb-0">Modify role name and permission assignments for {{ ucfirst($role->name) }}</p>
      </div>
      <div>
        <a href="{{ route('roles.index') }}" class="btn btn-primary">Go to Role List</a>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-12">
    @if ($errors->any())
      <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
        <strong>Please fix the errors below:</strong>
        <ul class="mb-0 mt-1">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    @endif

    <form id="roleEditForm" action="{{ route('roles.update', $role->id) }}" method="POST">
      @csrf
      @method('PUT')

      <!-- Role Name Card -->
      <div class="card mb-4">
        <div class="card-body p-4">
          <div class="row">
            <div class="col-md-6 mb-0">
              <label for="name" class="form-label fw-bold fs-6">Role Name <span class="text-danger">*</span></label>
              <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $role->name) }}" placeholder="e.g. manager, supervisor, auditor" required {{ $role->name === 'admin' ? 'readonly' : '' }}>
              <div class="form-text">Role names are stored in lowercase slug format.</div>
              @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
          </div>
        </div>
      </div>

      <!-- Permissions by Group Header -->
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0">Permissions by Module</h5>
        <div class="form-check form-switch">
          <input class="form-check-input" type="checkbox" id="globalSelectAll">
          <label class="form-check-label fw-semibold" for="globalSelectAll">Select All Permissions</label>
        </div>
      </div>

      <!-- Permissions Group Cards Grid -->
      <div class="row g-4 mb-4">
        @foreach ($groupedPermissions as $groupName => $permissions)
          @php $groupId = Str::slug($groupName); @endphp
          <div class="col-md-6 col-lg-3">
            <div class="card h-100 border border-light">
              <div class="card-header bg-light d-flex justify-content-between align-items-center py-2 px-3">
                <span class="fw-bold text-dark">{{ $groupName }}</span>
                <div class="form-check mb-0">
                  <input class="form-check-input group-select-all" type="checkbox" id="group_{{ $groupId }}" data-group="{{ $groupId }}">
                  <label class="form-check-label small text-muted" for="group_{{ $groupId }}">Select All</label>
                </div>
              </div>
              <div class="card-body p-3">
                @foreach ($permissions as $permission)
                  <div class="form-check mb-2">
                    <input class="form-check-input permission-checkbox perm-group-{{ $groupId }}" type="checkbox" name="permissions[]" id="perm_{{ $permission->id }}" value="{{ $permission->name }}" {{ in_array($permission->name, old('permissions', $rolePermissions)) ? 'checked' : '' }}>
                    <label class="form-check-label" for="perm_{{ $permission->id }}">
                      {{ Str::title(str_replace('-', ' ', $permission->name)) }}
                    </label>
                  </div>
                @endforeach
              </div>
            </div>
          </div>
        @endforeach
      </div>

      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">Update Role</button>
        <a href="{{ route('roles.index') }}" class="btn btn-secondary">Cancel</a>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
  updateGroupCheckboxesState();
  updateGlobalSelectAllState();

  // Group Select All Checkbox Handler
  $('.group-select-all').on('change', function() {
    var groupId = $(this).data('group');
    var isChecked = $(this).is(':checked');
    $('.perm-group-' + groupId).prop('checked', isChecked);
    updateGlobalSelectAllState();
  });

  // Individual Permission Checkbox Handler
  $('.permission-checkbox').on('change', function() {
    updateGroupCheckboxesState();
    updateGlobalSelectAllState();
  });

  // Global Select All Handler
  $('#globalSelectAll').on('change', function() {
    var isChecked = $(this).is(':checked');
    $('.permission-checkbox, .group-select-all').prop('checked', isChecked);
  });

  function updateGroupCheckboxesState() {
    $('.group-select-all').each(function() {
      var groupId = $(this).data('group');
      var totalInGroup = $('.perm-group-' + groupId).length;
      var checkedInGroup = $('.perm-group-' + groupId + ':checked').length;
      $(this).prop('checked', totalInGroup > 0 && totalInGroup === checkedInGroup);
    });
  }

  function updateGlobalSelectAllState() {
    var totalPerms = $('.permission-checkbox').length;
    var checkedPerms = $('.permission-checkbox:checked').length;
    $('#globalSelectAll').prop('checked', totalPerms > 0 && totalPerms === checkedPerms);
  }

  // Form Validation
  $("#roleEditForm").validate({
    rules: {
      name: "required",
      "permissions[]": "required"
    },
    messages: {
      name: "Please enter a role name",
      "permissions[]": "Please select at least one permission"
    },
    errorPlacement: function(error, element) {
      if (element.attr("name") == "permissions[]") {
        error.insertAfter("#globalSelectAll");
      } else {
        error.insertAfter(element);
      }
    }
  });
});
</script>
@endpush
