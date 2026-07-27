@extends('layouts.app')

@section('title', 'Settings - ' . \App\Models\Setting::get('app_title', 'InApp Inventory Dashboard'))

@section('content')
<div class="row">
  <div class="col-12">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
      <div>
        <h1 class="fs-3 mb-1">System Settings</h1>
        <p class="mb-0">Configure application title, logo, favicon, and primary theme colors</p>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-12">
    @if (session('success'))
      <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        <i class="ti ti-circle-check me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    @endif

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

    <form id="settingsForm" action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
      @csrf

      <!-- Branding & Title -->
      <div class="card mb-4">
        <div class="card-header bg-light py-3">
          <h5 class="fw-bold mb-0 text-dark"><i class="ti ti-typography me-2"></i>Application Identity</h5>
        </div>
        <div class="card-body p-4">
          <div class="row">
            <div class="col-md-8 mb-3">
              <label for="app_title" class="form-label fw-bold">Application Title <span class="text-danger">*</span></label>
              <input type="text" class="form-control @error('app_title') is-invalid @enderror" id="app_title" name="app_title" value="{{ old('app_title', $settings['app_title']) }}" placeholder="Enter application title" required>
              <div class="form-text">This title will be displayed in the browser tab and page header bar.</div>
              @error('app_title') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
          </div>
        </div>
      </div>

      <!-- App Logo & Favicon Uploads -->
      <div class="card mb-4">
        <div class="card-header bg-light py-3">
          <h5 class="fw-bold mb-0 text-dark"><i class="ti ti-photo me-2"></i>Logo & Favicon</h5>
        </div>
        <div class="card-body p-4">
          <div class="row g-4">
            <!-- App Logo -->
            <div class="col-md-6">
              <label for="app_logo" class="form-label fw-bold">Application Logo</label>
              <div class="d-flex align-items-center gap-3 mb-2 p-3 border rounded bg-light">
                <div class="bg-white p-2 rounded border text-center" style="min-width: 120px;">
                  <img id="logoPreview" src="{{ $settings['app_logo'] ?? asset('assets/images/logo.svg') }}" alt="Logo Preview" style="max-height: 48px; max-width: 160px; object-fit: contain;" />
                </div>
                <div>
                  <div class="fw-semibold small">Current Logo</div>
                  <div class="text-muted small">Recommended: PNG or SVG (Transparent background)</div>
                </div>
              </div>
              <input type="file" class="form-control @error('app_logo') is-invalid @enderror" id="app_logo" name="app_logo" accept="image/*">
              @error('app_logo') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            <!-- App Favicon -->
            <div class="col-md-6">
              <label for="app_favicon" class="form-label fw-bold">Application Favicon</label>
              <div class="d-flex align-items-center gap-3 mb-2 p-3 border rounded bg-light">
                <div class="bg-white p-2 rounded border text-center" style="min-width: 64px;">
                  <img id="favPreview" src="{{ $settings['app_favicon'] ?? asset('assets/images/favicon_io/favicon-32x32.png') }}" alt="Favicon Preview" style="width: 32px; height: 32px; object-fit: contain;" />
                </div>
                <div>
                  <div class="fw-semibold small">Current Favicon</div>
                  <div class="text-muted small">Recommended: .ico or 32x32 PNG icon</div>
                </div>
              </div>
              <input type="file" class="form-control @error('app_favicon') is-invalid @enderror" id="app_favicon" name="app_favicon" accept="image/*">
              @error('app_favicon') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>
          </div>
        </div>
      </div>

      <!-- Primary Theme Colors -->
      <div class="card mb-4">
        <div class="card-header bg-light py-3">
          <h5 class="fw-bold mb-0 text-dark"><i class="ti ti-palette me-2"></i>Theme Color Customization</h5>
        </div>
        <div class="card-body p-4">
          <div class="row g-4">
            <!-- Primary Background / Accent Color -->
            <div class="col-md-6">
              <label for="primary_color" class="form-label fw-bold">Primary Background Color <span class="text-danger">*</span></label>
              <div class="input-group">
                <input type="color" class="form-control form-control-color" id="primary_color_picker" value="{{ old('primary_color', $settings['primary_color']) }}" title="Choose color">
                <input type="text" class="form-control @error('primary_color') is-invalid @enderror" id="primary_color" name="primary_color" value="{{ old('primary_color', $settings['primary_color']) }}" placeholder="#ea580c" required pattern="^#(?:[0-9a-fA-F]{3}){1,2}$">
              </div>
              <div class="form-text">Used for main brand buttons, primary badges, and active menu indicators.</div>
              @error('primary_color') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            <!-- Primary Text Color -->
            <div class="col-md-6">
              <label for="primary_text_color" class="form-label fw-bold">Primary Button Text Color <span class="text-danger">*</span></label>
              <div class="input-group">
                <input type="color" class="form-control form-control-color" id="primary_text_color_picker" value="{{ old('primary_text_color', $settings['primary_text_color']) }}" title="Choose text color">
                <input type="text" class="form-control @error('primary_text_color') is-invalid @enderror" id="primary_text_color" name="primary_text_color" value="{{ old('primary_text_color', $settings['primary_text_color']) }}" placeholder="#ffffff" required pattern="^#(?:[0-9a-fA-F]{3}){1,2}$">
              </div>
              <div class="form-text">Used for text inside primary buttons and badges for contrast.</div>
              @error('primary_text_color') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>
          </div>

          <!-- Color Live Preview Box -->
          <div class="mt-4 p-3 border rounded d-flex align-items-center justify-content-between" id="themePreviewBox" style="background-color: #f8fafc;">
            <div>
              <span class="fw-bold">Live Theme Preview:</span>
              <span class="ms-2 text-muted">Preview how primary buttons will look</span>
            </div>
            <button type="button" class="btn" id="previewBtn" style="background-color: {{ $settings['primary_color'] }}; color: {{ $settings['primary_text_color'] }}; border: none;">
              Primary Button Preview
            </button>
          </div>
        </div>
      </div>

      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">Save Settings</button>
        <a href="{{ route('dashboard') }}" class="btn btn-secondary">Cancel</a>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
  // Logo live preview
  $('#app_logo').on('change', function(e) {
    var file = e.target.files[0];
    if (file) {
      var reader = new FileReader();
      reader.onload = function(e) {
        $('#logoPreview').attr('src', e.target.result);
      };
      reader.readAsDataURL(file);
    }
  });

  // Favicon live preview
  $('#app_favicon').on('change', function(e) {
    var file = e.target.files[0];
    if (file) {
      var reader = new FileReader();
      reader.onload = function(e) {
        $('#favPreview').attr('src', e.target.result);
      };
      reader.readAsDataURL(file);
    }
  });

  // Sync color pickers with text inputs & live preview
  $('#primary_color_picker').on('input change', function() {
    var val = $(this).val();
    $('#primary_color').val(val);
    updatePreview();
  });

  $('#primary_color').on('input change', function() {
    var val = $(this).val();
    if (/^#(?:[0-9a-fA-F]{3}){1,2}$/.test(val)) {
      $('#primary_color_picker').val(val);
      updatePreview();
    }
  });

  $('#primary_text_color_picker').on('input change', function() {
    var val = $(this).val();
    $('#primary_text_color').val(val);
    updatePreview();
  });

  $('#primary_text_color').on('input change', function() {
    var val = $(this).val();
    if (/^#(?:[0-9a-fA-F]{3}){1,2}$/.test(val)) {
      $('#primary_text_color_picker').val(val);
      updatePreview();
    }
  });

  function updatePreview() {
    var bg = $('#primary_color').val();
    var fg = $('#primary_text_color').val();
    $('#previewBtn').css({
      'background-color': bg,
      'color': fg
    });
  }

  // jQuery Validate
  $("#settingsForm").validate({
    rules: {
      app_title: "required",
      primary_color: "required",
      primary_text_color: "required"
    },
    messages: {
      app_title: "Please enter an application title",
      primary_color: "Please specify a primary background color",
      primary_text_color: "Please specify a primary text color"
    }
  });
});
</script>
@endpush
