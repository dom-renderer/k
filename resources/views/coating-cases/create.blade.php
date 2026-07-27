@extends('layouts.app')

@section('title', 'Create Coating Case - ' . \App\Models\Setting::get('app_title', 'InApp Inventory Dashboard'))

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.css" />
<style>
  .dropzone {
    border: 2px dashed var(--bs-primary);
    border-radius: 8px;
    background: #f8fafc;
    min-height: 180px;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  .dropzone .dz-message {
    font-weight: 500;
    color: #64748b;
  }
  .oa-status-badge {
    font-size: 0.85rem;
    margin-top: 0.25rem;
  }
</style>
@endpush

@section('content')
<div class="row justify-content-center">
  <div class="col-lg-10">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="fs-3 mb-1">Create New Coating Case</h1>
        <p class="text-muted mb-0">Enter OA Number, Sector, Equipment, and upload Level 1 pre-coating photos & documents.</p>
      </div>
      <a href="{{ route('cases.index') }}" class="btn btn-outline-secondary">
        <i class="ti ti-arrow-left me-1"></i> Back to Cases
      </a>
    </div>

    @if ($errors->any())
      <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
        <ul class="mb-0">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    @endif

    <form action="{{ route('cases.store') }}" method="POST" id="createCaseForm">
      @csrf
      <input type="hidden" name="uploaded_files" id="uploadedFilesInput" value="[]">

      <div class="card mb-4">
        <div class="card-header bg-light fw-bold">
          <i class="ti ti-file-text me-2"></i> Case Basic Information
        </div>
        <div class="card-body p-4">
          <div class="row g-3">
            <div class="col-md-6">
              <label for="oa_number" class="form-label fw-semibold">OA Number <span class="text-danger">*</span></label>
              <input type="text" class="form-control @error('oa_number') is-invalid @enderror" id="oa_number" name="oa_number" value="{{ old('oa_number') }}" required placeholder="e.g. OA-994821">
              <div id="oaFeedback" class="oa-status-badge"></div>
              @error('oa_number')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-6">
              <label for="sector_id" class="form-label fw-semibold">Sector <span class="text-danger">*</span></label>
              <select class="form-select select2 @error('sector_id') is-invalid @enderror" id="sector_id" name="sector_id" required>
                <option value="">-- Select Sector --</option>
                @foreach ($sectors as $sector)
                  <option value="{{ $sector->id }}" {{ old('sector_id') == $sector->id ? 'selected' : '' }}>
                    {{ $sector->title }}
                  </option>
                @endforeach
              </select>
              @error('sector_id')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-6">
              <label for="equipment_id" class="form-label fw-semibold">Equipment <span class="text-danger">*</span></label>
              <select class="form-select select2 @error('equipment_id') is-invalid @enderror" id="equipment_id" name="equipment_id" required>
                <option value="">-- Select Equipment --</option>
                @foreach ($equipments as $eq)
                  <option value="{{ $eq->id }}" {{ old('equipment_id') == $eq->id ? 'selected' : '' }}>
                    {{ $eq->name }} {{ $eq->sku ? '('.$eq->sku.')' : '' }}
                  </option>
                @endforeach
              </select>
              @error('equipment_id')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Initial Approval Status</label>
              <input type="text" class="form-control bg-light" value="Level 1 Pending Review" readonly>
            </div>

            <div class="col-12">
              <label for="other_information" class="form-label fw-semibold">Other Information / Remarks</label>
              <textarea class="form-control" id="other_information" name="other_information" rows="3" placeholder="Add any special instructions, substrate condition, or notes...">{{ old('other_information') }}</textarea>
            </div>
          </div>
        </div>
      </div>

      <div class="card mb-4">
        <div class="card-header bg-light fw-bold d-flex justify-content-between align-items-center">
          <span><i class="ti ti-camera me-2"></i> Level 1: Upload Pre-Coating Photos & Documents</span>
          <small class="text-muted fw-normal">Drag & drop files or click to browse</small>
        </div>
        <div class="card-body p-4">
          <div class="dropzone" id="level1Dropzone">
            <div class="dz-message text-center">
              <i class="ti ti-cloud-upload fs-1 text-primary mb-2 d-block"></i>
              <span>Drop Pre-Coating Photos & Documents here to upload</span>
              <div class="small text-muted mt-1">Supports images, PDF, DOCX, ZIP (Max size: 25MB per file)</div>
            </div>
          </div>
        </div>
      </div>

      <div class="d-flex justify-content-end gap-2 mb-5">
        <a href="{{ route('cases.index') }}" class="btn btn-light">Cancel</a>
        <button type="submit" class="btn btn-primary" id="submitBtn">
          <i class="ti ti-check me-1"></i> Save & Generate Case ID
        </button>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.js"></script>
<script>
  Dropzone.autoDiscover = false;

  $(document).ready(function() {
    $('.select2').select2({
      theme: 'bootstrap-5'
    });

    var uploadedFiles = [];
    var isOaDuplicate = false;

    // Instant OA Number Duplicate Check
    $('#oa_number').on('input blur', function() {
      var oaNumber = $(this).val().trim();
      var feedback = $('#oaFeedback');
      
      if (oaNumber.length === 0) {
        feedback.html('');
        isOaDuplicate = false;
        $('#submitBtn').prop('disabled', false);
        return;
      }

      $.ajax({
        url: "{{ route('cases.check-oa') }}",
        type: 'GET',
        data: { oa_number: oaNumber },
        success: function(response) {
          if (response.exists) {
            feedback.removeClass('text-success').addClass('text-danger').html('<i class="ti ti-alert-circle me-1"></i>' + response.message);
            isOaDuplicate = true;
            $('#submitBtn').prop('disabled', true);
          } else {
            feedback.removeClass('text-danger').addClass('text-success').html('<i class="ti ti-circle-check me-1"></i>' + response.message);
            isOaDuplicate = false;
            $('#submitBtn').prop('disabled', false);
          }
        }
      });
    });

    // Dropzone Initialization
    var level1Dropzone = new Dropzone("#level1Dropzone", {
      url: "{{ route('cases.upload-file') }}",
      headers: {
        'X-CSRF-TOKEN': "{{ csrf_token() }}"
      },
      paramName: "file",
      maxFilesize: 25, // MB
      addRemoveLinks: true,
      dictRemoveFile: "Remove File",
      init: function() {
        this.on("sending", function(file, xhr, formData) {
          formData.append("level", 1);
          formData.append("category", file.type.includes('image') ? 'pre_coating' : 'document');
        });
        this.on("success", function(file, response) {
          if (response.success) {
            file.serverData = response.file;
            uploadedFiles.push(response.file);
            $('#uploadedFilesInput').val(JSON.stringify(uploadedFiles));
          }
        });
        this.on("removedfile", function(file) {
          if (file.serverData) {
            uploadedFiles = uploadedFiles.filter(function(item) {
              return item.file_path !== file.serverData.file_path;
            });
            $('#uploadedFilesInput').val(JSON.stringify(uploadedFiles));
          }
        });
      }
    });

    // jQuery Form Validation
    $('#createCaseForm').validate({
      rules: {
        oa_number: { required: true },
        sector_id: { required: true },
        equipment_id: { required: true }
      },
      submitHandler: function(form) {
        if (isOaDuplicate) {
          Swal.fire('Duplicate OA Number!', 'Please enter a unique OA Number before submitting.', 'error');
          return false;
        }
        form.submit();
      }
    });
  });
</script>
@endpush
