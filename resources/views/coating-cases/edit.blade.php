@extends('layouts.app')

@section('title', 'Edit Coating Case: ' . $case->oa_number . ' - ' . \App\Models\Setting::get('app_title', 'InApp Inventory Dashboard'))

@section('content')
<div class="row justify-content-center">
  <div class="col-lg-10">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="fs-3 mb-1">Edit Coating Case</h1>
        <p class="text-muted mb-0">Update case information for OA Number: <strong>{{ $case->oa_number }}</strong></p>
      </div>
      <a href="{{ route('cases.show', $case->id) }}" class="btn btn-outline-secondary">
        <i class="ti ti-arrow-left me-1"></i> Back to Case Details
      </a>
    </div>

    @if($case->is_closed)
      <div class="alert alert-danger d-flex align-items-center mb-4">
        <i class="ti ti-lock fs-3 me-3"></i>
        <div>
          <strong>Case is Closed!</strong> This coating case has received final approval and cannot be modified.
        </div>
      </div>
    @endif

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

    <form action="{{ route('cases.update', $case->id) }}" method="POST" id="editCaseForm">
      @csrf
      @method('PUT')

      <div class="card mb-4">
        <div class="card-header bg-light fw-bold">
          <i class="ti ti-file-text me-2"></i> Case Basic Information
        </div>
        <div class="card-body p-4">
          <fieldset {{ $case->is_closed ? 'disabled' : '' }}>
            <div class="row g-3">
              <div class="col-md-6">
                <label for="oa_number" class="form-label fw-semibold">OA Number <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('oa_number') is-invalid @enderror" id="oa_number" name="oa_number" value="{{ old('oa_number', $case->oa_number) }}" required>
                <div id="oaFeedback" class="small mt-1"></div>
                @error('oa_number')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-md-6">
                <label for="sector_id" class="form-label fw-semibold">Sector <span class="text-danger">*</span></label>
                <select class="form-select select2 @error('sector_id') is-invalid @enderror" id="sector_id" name="sector_id" required>
                  <option value="">-- Select Sector --</option>
                  @foreach ($sectors as $sector)
                    <option value="{{ $sector->id }}" {{ old('sector_id', $case->sector_id) == $sector->id ? 'selected' : '' }}>
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
                    <option value="{{ $eq->id }}" {{ old('equipment_id', $case->equipment_id) == $eq->id ? 'selected' : '' }}>
                      {{ $eq->name }} {{ $eq->sku ? '('.$eq->sku.')' : '' }}
                    </option>
                  @endforeach
                </select>
                @error('equipment_id')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-md-6">
                <label class="form-label fw-semibold">Current Workflow Status</label>
                <div>{!! $case->status_badge !!}</div>
              </div>

              <div class="col-12">
                <label for="other_information" class="form-label fw-semibold">Other Information / Remarks</label>
                <textarea class="form-control" id="other_information" name="other_information" rows="4">{{ old('other_information', $case->other_information) }}</textarea>
              </div>
            </div>
          </fieldset>
        </div>
      </div>

      @if(!$case->is_closed)
        <div class="d-flex justify-content-end gap-2 mb-5">
          <a href="{{ route('cases.show', $case->id) }}" class="btn btn-light">Cancel</a>
          <button type="submit" class="btn btn-primary" id="submitBtn">
            <i class="ti ti-check me-1"></i> Update Case Details
          </button>
        </div>
      @endif
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
  $(document).ready(function() {
    $('.select2').select2({ theme: 'bootstrap-5' });

    var isOaDuplicate = false;

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
        data: { oa_number: oaNumber, exclude_id: "{{ $case->id }}" },
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
  });
</script>
@endpush
