@extends('layouts.app')

@section('title', 'Create User - InApp Inventory Dashboard')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
      <div>
        <h1 class="fs-3 mb-1">Add User</h1>
        <p class="mb-0">Manage system user details and roles</p>
      </div>
      <div>
        <a href="{{ route('users.index') }}" class="btn btn-primary">Go to User List</a>
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

    <div class="card">
      <div class="card-body p-4">
        <form id="userForm" action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
          @csrf

          <!-- 1. Profile in center -->
          <div class="row mb-4">
            <div class="col-12 text-center">
              <div class="position-relative d-inline-block">
                <img id="avatarPreview" src="{{ asset('assets/images/avatar/avatar-1.jpg') }}" class="avatar avatar-xl rounded-circle border shadow-sm" alt="Preview" style="width: 100px; height: 100px; object-fit: cover;" />
                <label for="avatar" class="btn btn-sm btn-primary rounded-circle position-absolute bottom-0 end-0 d-flex align-items-center justify-content-center p-0" style="width: 32px; height: 32px; cursor: pointer;" title="Upload Photo">
                  <i class="ti ti-camera fs-6"></i>
                </label>
                <input type="file" class="d-none @error('avatar') is-invalid @enderror" id="avatar" name="avatar" accept="image/*">
              </div>
              <div class="form-text mt-1">Click camera icon to upload profile photo</div>
              @error('avatar') <div class="invalid-feedback d-block text-center">{{ $message }}</div> @enderror
            </div>
          </div>

          <!-- 2. First name and Last name -->
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
              <input type="text" class="form-control @error('first_name') is-invalid @enderror" id="first_name" name="first_name" value="{{ old('first_name') }}" placeholder="Enter first name" required>
              @error('first_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6 mb-3">
              <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
              <input type="text" class="form-control @error('last_name') is-invalid @enderror" id="last_name" name="last_name" value="{{ old('last_name') }}" placeholder="Enter last name" required>
              @error('last_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
          </div>

          <!-- 3. Email and Phone number and Username -->
          <div class="row">
            <div class="col-md-4 mb-3">
              <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
              <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="Enter email address" required>
              @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-4 mb-3">
              <label for="phone_input" class="form-label">Phone Number <span class="text-danger">*</span></label>
              <input type="tel" class="form-control @error('phone_number') is-invalid @enderror" id="phone_input" placeholder="Enter phone number" value="{{ old('phone_number') }}" required>
              <input type="hidden" id="phone_number" name="phone_number" value="{{ old('phone_number') }}">
              <div id="phone_error" class="error d-none">Please enter a valid phone number.</div>
              @error('phone_number') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-4 mb-3">
              <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
              <input type="text" class="form-control @error('username') is-invalid @enderror" id="username" name="username" value="{{ old('username') }}" placeholder="Enter username" required>
              @error('username') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
          </div>

          <!-- 4. Password and Role -->
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
              <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Enter password (min 6 characters)" required minlength="6">
              @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6 mb-3">
              <label for="roles" class="form-label">Role <span class="text-danger">*</span></label>
              <select class="form-select select2 @error('roles') is-invalid @enderror" id="roles" name="roles[]" multiple required data-placeholder="Select role(s)">
                @foreach ($roles as $role)
                  <option value="{{ $role->name }}" {{ is_array(old('roles')) && in_array($role->name, old('roles')) ? 'selected' : ($role->name === 'user' ? 'selected' : '') }}>
                    {{ ucfirst($role->name) }}
                  </option>
                @endforeach
              </select>
              @error('roles') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>
          </div>

          <div class="d-flex gap-2 mt-3">
            <button type="submit" class="btn btn-primary">Add User</button>
            <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
  // Initialize Select2 for Roles Selection
  $('#roles').select2({
    placeholder: 'Select role(s)',
    width: '100%'
  });

  // Initialize intl-tel-input
  var phoneInput = document.querySelector("#phone_input");
  var iti = window.intlTelInput(phoneInput, {
    separateDialCode: true,
    initialCountry: "in",
    utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js"
  });

  // Photo live preview on select
  $('#avatar').on('change', function(e) {
    var file = e.target.files[0];
    if (file) {
      var reader = new FileReader();
      reader.onload = function(e) {
        $('#avatarPreview').attr('src', e.target.result);
      };
      reader.readAsDataURL(file);
    }
  });

  // jQuery Validate for Client-side Validation
  $("#userForm").validate({
    ignore: [],
    rules: {
      first_name: "required",
      last_name: "required",
      username: {
        required: true,
        minlength: 3
      },
      email: {
        required: true,
        email: true
      },
      phone_input: "required",
      "roles[]": "required",
      password: {
        required: true,
        minlength: 6
      }
    },
    messages: {
      first_name: "Enter first name",
      last_name: "Enter last name",
      username: {
        required: "Enter username",
        minlength: "Minimum 3 characters"
      },
      email: "Enter a valid email",
      phone_input: "Enter phone number",
      "roles[]": "Select at least one role",
      password: {
        required: "Enter password",
        minlength: "Minimum 6 characters"
      }
    },
    errorPlacement: function(error, element) {
      if (element.hasClass('select2-hidden-accessible')) {
        error.insertAfter(element.next('.select2-container'));
      } else if (element.attr("id") == "phone_input") {
        error.insertAfter(element.closest('.iti'));
      } else {
        error.insertAfter(element);
      }
    },
    submitHandler: function(form) {
      if (iti.isValidNumber()) {
        $('#phone_error').addClass('d-none');
        $('#phone_number').val(iti.getNumber());
        form.submit();
      } else {
        $('#phone_error').removeClass('d-none');
        return false;
      }
    }
  });
});
</script>
@endpush
