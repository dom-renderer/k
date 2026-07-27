@extends('layouts.app')

@section('title', 'Add New Sector - ' . \App\Models\Setting::get('app_title', 'InApp Inventory Dashboard'))

@section('content')
<div class="row">
  <div class="col-lg-8 mx-auto">
    <div class="d-flex align-items-center justify-content-between mb-4">
      <div>
        <h1 class="fs-3 mb-1">Add New Sector</h1>
        <p class="text-muted mb-0">Create a new sector title and description.</p>
      </div>
      <a href="{{ route('sectors.index') }}" class="btn btn-outline-secondary">
        <i class="ti ti-arrow-left me-1"></i> Back to Listing
      </a>
    </div>

    <div class="card">
      <div class="card-body p-4">
        <form id="sectorForm" action="{{ route('sectors.store') }}" method="POST">
          @csrf

          <div class="mb-4">
            <label for="title" class="form-label fw-semibold">Sector Title <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" placeholder="e.g. Information Technology" required>
            @error('title')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-4">
            <label for="description" class="form-label fw-semibold">Description</label>
            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4" placeholder="Brief details or scope of this sector...">{{ old('description') }}</textarea>
            @error('description')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="d-flex justify-content-end gap-2 pt-2 border-top">
            <a href="{{ route('sectors.index') }}" class="btn btn-light">Cancel</a>
            <button type="submit" class="btn btn-primary">
              <i class="ti ti-check me-1"></i> Save Sector
            </button>
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
    $('#sectorForm').validate({
      rules: {
        title: {
          required: true,
          maxlength: 255
        },
        description: {
          maxlength: 1000
        }
      },
      messages: {
        title: {
          required: "Please enter the sector title."
        }
      },
      errorElement: 'div',
      errorClass: 'invalid-feedback',
      highlight: function(element) {
        $(element).addClass('is-invalid');
      },
      unhighlight: function(element) {
        $(element).removeClass('is-invalid');
      },
      errorPlacement: function(error, element) {
        error.insertAfter(element);
      }
    });
  });
</script>
@endpush
