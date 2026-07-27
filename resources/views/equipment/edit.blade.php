@extends('layouts.app')

@section('title', 'Edit Equipment - ' . \App\Models\Setting::get('app_title', 'InApp Inventory Dashboard'))

@section('content')
<div class="row">
  <div class="col-lg-8 mx-auto">
    <div class="d-flex align-items-center justify-content-between mb-4">
      <div>
        <h1 class="fs-3 mb-1">Edit Equipment</h1>
        <p class="text-muted mb-0">Update equipment item details and photo.</p>
      </div>
      <a href="{{ route('equipment.index') }}" class="btn btn-outline-secondary">
        <i class="ti ti-arrow-left me-1"></i> Back to Listing
      </a>
    </div>

    <div class="card">
      <div class="card-body p-4">
        <form id="equipmentForm" action="{{ route('equipment.update', $equipment->id) }}" method="POST" enctype="multipart/form-data">
          @csrf
          @method('PUT')

          <!-- Photo Upload & Live Preview -->
          <div class="mb-4 text-center">
            <div class="mb-3">
              <img id="photoPreview" src="{{ $equipment->photo_url ?? '' }}" alt="Photo Preview" class="rounded border shadow-sm style-preview {{ $equipment->photo_url ? '' : 'd-none' }}" style="width: 120px; height: 120px; object-fit: cover;">
              <div id="photoPlaceholder" class="rounded border bg-light d-inline-flex align-items-center justify-content-center text-muted {{ $equipment->photo_url ? 'd-none' : '' }}" style="width: 120px; height: 120px;">
                <i class="ti ti-camera fs-1"></i>
              </div>
            </div>
            <label for="photo" class="btn btn-sm btn-outline-primary">
              <i class="ti ti-upload me-1"></i> Change Photo
            </label>
            <input type="file" class="d-none @error('photo') is-invalid @enderror" id="photo" name="photo" accept="image/*">
            <div class="small text-muted mt-1">Allowed formats: JPG, PNG, WEBP, SVG (Max: 2MB)</div>
            @error('photo')
              <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
          </div>

          <div class="row g-3 mb-4">
            <div class="col-md-6">
              <label for="name" class="form-label fw-semibold">Equipment Name <span class="text-danger">*</span></label>
              <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $equipment->name) }}" placeholder="e.g. Hydraulic Forklift" required>
              @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-6">
              <label for="sku" class="form-label fw-semibold">SKU (Unique)</label>
              <input type="text" class="form-control @error('sku') is-invalid @enderror" id="sku" name="sku" value="{{ old('sku', $equipment->sku) }}" placeholder="e.g. EQ-88421">
              <div class="form-text">Leave blank if no SKU is assigned.</div>
              @error('sku')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div class="mb-4">
            <label for="description" class="form-label fw-semibold">Description</label>
            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4" placeholder="Technical specifications, model, or notes...">{{ old('description', $equipment->description) }}</textarea>
            @error('description')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="d-flex justify-content-between align-items-center pt-2 border-top">
            <div class="small text-muted">
              @if ($equipment->creator)
                Created by <strong>{{ $equipment->creator->name }}</strong> on {{ $equipment->created_at->format('M d, Y') }}
              @endif
              @if ($equipment->updater)
                &bull; Last updated by <strong>{{ $equipment->updater->name }}</strong> on {{ $equipment->updated_at->format('M d, Y') }}
              @endif
            </div>
            <div class="d-flex gap-2">
              <a href="{{ route('equipment.index') }}" class="btn btn-light">Cancel</a>
              <button type="submit" class="btn btn-primary">
                <i class="ti ti-check me-1"></i> Update Equipment
              </button>
            </div>
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
    // Dynamic Image File Preview
    $('#photo').on('change', function(e) {
      const file = e.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
          $('#photoPreview').attr('src', e.target.result).removeClass('d-none');
          $('#photoPlaceholder').addClass('d-none');
        };
        reader.readAsDataURL(file);
      }
    });

    $('#equipmentForm').validate({
      rules: {
        name: {
          required: true,
          maxlength: 255
        },
        sku: {
          maxlength: 255
        },
        description: {
          maxlength: 2000
        }
      },
      messages: {
        name: {
          required: "Please enter the equipment name."
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
