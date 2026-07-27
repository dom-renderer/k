@extends('layouts.app')

@section('title', 'Case Details: ' . $case->case_number . ' - ' . \App\Models\Setting::get('app_title', 'InApp Inventory Dashboard'))

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.css" />
<style>
  .stepper-wrapper {
    display: flex;
    justify-content: space-between;
    margin-bottom: 2rem;
    position: relative;
  }
  .stepper-wrapper::before {
    content: '';
    position: absolute;
    top: 24px;
    left: 10%;
    right: 10%;
    height: 3px;
    background: #e2e8f0;
    z-index: 1;
  }
  .stepper-item {
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: center;
    flex: 1;
    z-index: 2;
  }
  .step-counter {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: #ffffff;
    border: 3px solid #cbd5e1;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 1.1rem;
    color: #64748b;
    margin-bottom: 0.5rem;
    transition: all 0.3s ease;
  }
  .stepper-item.completed .step-counter {
    background: var(--bs-success, #10b981);
    border-color: var(--bs-success, #10b981);
    color: #ffffff;
  }
  .stepper-item.active .step-counter {
    background: var(--bs-primary);
    border-color: var(--bs-primary);
    color: #ffffff;
    box-shadow: 0 0 0 5px rgba(37, 99, 235, 0.2);
  }
  .stepper-item.rejected .step-counter {
    background: #ef4444;
    border-color: #ef4444;
    color: #ffffff;
  }
  .step-name {
    font-weight: 600;
    font-size: 0.9rem;
    color: #334155;
  }
  .dropzone {
    border: 2px dashed var(--bs-primary);
    border-radius: 8px;
    background: #f8fafc;
    min-height: 120px;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  .file-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
  }
  .file-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
  }
  .preview-thumb {
    height: 140px;
    object-fit: cover;
    width: 100%;
  }
</style>
@endpush

@section('content')
<div class="row">
  <div class="col-12">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <div class="d-flex align-items-center gap-2 mb-1">
          <h1 class="fs-3 mb-0">{{ $case->case_number }}</h1>
          {!! $case->status_badge !!}
        </div>
        <p class="text-muted mb-0">OA Number: <strong class="text-dark">{{ $case->oa_number }}</strong> | Created {{ $case->created_at->format('M d, Y') }}</p>
      </div>
      <div class="d-flex gap-2">
        <a href="{{ route('cases.index') }}" class="btn btn-outline-secondary">
          <i class="ti ti-arrow-left me-1"></i> Back to Cases
        </a>
        @can('case-edit')
          @if(!$case->is_closed)
            <a href="{{ route('cases.edit', $case->id) }}" class="btn btn-primary">
              <i class="ti ti-edit me-1"></i> Edit Case Details
            </a>
          @endif
        @endcan
      </div>
    </div>

    @if (session('success'))
      <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        <i class="ti ti-check me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    @endif

    <!-- 3-Level Workflow Visual Stepper -->
    <div class="card mb-4">
      <div class="card-body p-4">
        <h5 class="card-title fw-bold mb-4"><i class="ti ti-git-merge me-2"></i> 3-Level Coating Approval Workflow</h5>
        
        <div class="stepper-wrapper">
          <!-- Level 1 Step -->
          @php
            $l1Class = '';
            if ($case->status === 'closed' || $case->current_level > 1) $l1Class = 'completed';
            elseif ($case->current_level == 1 && str_contains($case->status, 'rejected')) $l1Class = 'rejected';
            elseif ($case->current_level == 1) $l1Class = 'active';
          @endphp
          <div class="stepper-item {{ $l1Class }}">
            <div class="step-counter">
              @if($l1Class === 'completed') <i class="ti ti-check fs-4"></i>
              @elseif($l1Class === 'rejected') <i class="ti ti-x fs-4"></i>
              @else 1 @endif
            </div>
            <div class="step-name">Level 1: Pre-Coating</div>
            <small class="text-muted">Photos & Specs</small>
          </div>

          <!-- Level 2 Step -->
          @php
            $l2Class = '';
            if ($case->status === 'closed' || $case->current_level > 2) $l2Class = 'completed';
            elseif ($case->current_level == 2 && str_contains($case->status, 'rejected')) $l2Class = 'rejected';
            elseif ($case->current_level == 2) $l2Class = 'active';
          @endphp
          <div class="stepper-item {{ $l2Class }}">
            <div class="step-counter">
              @if($l2Class === 'completed') <i class="ti ti-check fs-4"></i>
              @elseif($l2Class === 'rejected') <i class="ti ti-x fs-4"></i>
              @else 2 @endif
            </div>
            <div class="step-name">Level 2: Coating Application</div>
            <small class="text-muted">In-Progress Docs</small>
          </div>

          <!-- Level 3 Step -->
          @php
            $l3Class = '';
            if ($case->status === 'closed') $l3Class = 'completed';
            elseif ($case->current_level == 3 && str_contains($case->status, 'rejected')) $l3Class = 'rejected';
            elseif ($case->current_level == 3) $l3Class = 'active';
          @endphp
          <div class="stepper-item {{ $l3Class }}">
            <div class="step-counter">
              @if($l3Class === 'completed') <i class="ti ti-lock fs-4"></i>
              @elseif($l3Class === 'rejected') <i class="ti ti-x fs-4"></i>
              @else 3 @endif
            </div>
            <div class="step-name">Level 3: After-Coating Review</div>
            <small class="text-muted">Closure Approval</small>
          </div>
        </div>

        @if($case->is_closed)
          <div class="alert alert-success d-flex align-items-center mb-0">
            <i class="ti ti-circle-check fs-3 me-3"></i>
            <div>
              <strong>Case Closed & Locked!</strong> Final approval granted on {{ $case->closed_at ? $case->closed_at->format('M d, Y h:i A') : '' }} by {{ $case->closer->name ?? 'Admin' }}. No further edits can be made to this case.
            </div>
          </div>
        @endif
      </div>
    </div>

    <div class="row">
      <!-- Left Column: Case Details & Level Files -->
      <div class="col-lg-8">
        <!-- Details Overview -->
        <div class="card mb-4">
          <div class="card-header bg-light fw-bold">
            <i class="ti ti-info-circle me-2"></i> Details Overview
          </div>
          <div class="card-body p-4">
            <div class="row g-3">
              <div class="col-sm-6">
                <span class="text-muted d-block small">OA Number</span>
                <span class="fw-semibold text-dark fs-6">{{ $case->oa_number }}</span>
              </div>
              <div class="col-sm-6">
                <span class="text-muted d-block small">Case Number</span>
                <span class="fw-semibold text-dark fs-6">{{ $case->case_number }}</span>
              </div>
              <div class="col-sm-6">
                <span class="text-muted d-block small">Sector</span>
                <span class="fw-semibold text-dark">{{ $case->sector->title ?? 'N/A' }}</span>
              </div>
              <div class="col-sm-6">
                <span class="text-muted d-block small">Equipment</span>
                <span class="fw-semibold text-dark">{{ $case->equipment->name ?? 'N/A' }}</span>
              </div>
              <div class="col-sm-6">
                <span class="text-muted d-block small">Added By</span>
                <span class="fw-semibold text-dark">{{ $case->creator->name ?? 'System' }}</span>
              </div>
              <div class="col-sm-6">
                <span class="text-muted d-block small">Last Updated</span>
                <span class="fw-semibold text-dark">{{ $case->updated_at->format('M d, Y h:i A') }}</span>
              </div>
              @if($case->other_information)
                <div class="col-12 border-top pt-3 mt-3">
                  <span class="text-muted d-block small">Other Information / Remarks</span>
                  <p class="mb-0 text-dark">{{ $case->other_information }}</p>
                </div>
              @endif
            </div>
          </div>
        </div>

        <!-- Files Accordion by Level -->
        <div class="card mb-4">
          <div class="card-header bg-light fw-bold">
            <i class="ti ti-files me-2"></i> Case Documents & Photos by Level
          </div>
          <div class="card-body p-4">
            <ul class="nav nav-tabs nav-fill mb-4" id="levelTabs" role="tablist">
              <li class="nav-item" role="presentation">
                <button class="nav-link active fw-semibold" id="level1-tab" data-bs-toggle="tab" data-bs-target="#level1Pane" type="button" role="tab">
                  Level 1 Files <span class="badge bg-secondary ms-1">{{ $level1Files->count() }}</span>
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold" id="level2-tab" data-bs-toggle="tab" data-bs-target="#level2Pane" type="button" role="tab">
                  Level 2 Files <span class="badge bg-secondary ms-1">{{ $level2Files->count() }}</span>
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold" id="level3-tab" data-bs-toggle="tab" data-bs-target="#level3Pane" type="button" role="tab">
                  Level 3 Files <span class="badge bg-secondary ms-1">{{ $level3Files->count() }}</span>
                </button>
              </li>
            </ul>

            <div class="tab-content" id="levelTabsContent">
              <!-- Level 1 Pane -->
              <div class="tab-pane fade show active" id="level1Pane" role="tabpanel">
                @include('coating-cases.partials.file-grid', ['files' => $level1Files, 'level' => 1, 'case' => $case])
              </div>

              <!-- Level 2 Pane -->
              <div class="tab-pane fade" id="level2Pane" role="tabpanel">
                @include('coating-cases.partials.file-grid', ['files' => $level2Files, 'level' => 2, 'case' => $case])
              </div>

              <!-- Level 3 Pane -->
              <div class="tab-pane fade" id="level3Pane" role="tabpanel">
                @include('coating-cases.partials.file-grid', ['files' => $level3Files, 'level' => 3, 'case' => $case])
              </div>
            </div>
          </div>
        </div>

        <!-- Level Review History Log -->
        <div class="card mb-4">
          <div class="card-header bg-light fw-bold">
            <i class="ti ti-history me-2"></i> Review History & Audit Log
          </div>
          <div class="card-body p-4">
            <div class="table-responsive">
              <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                  <tr>
                    <th>Level</th>
                    <th>Action</th>
                    <th>Remarks</th>
                    <th>Reviewer</th>
                    <th>Date</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($case->logs as $log)
                    <tr>
                      <td><span class="badge bg-light-primary text-primary">Level {{ $log->level }}</span></td>
                      <td>
                        @if($log->action === 'approved')
                          <span class="badge bg-success"><i class="ti ti-check me-1"></i>Approved</span>
                        @elseif($log->action === 'rejected')
                          <span class="badge bg-danger"><i class="ti ti-x me-1"></i>Rejected (Reset to Level {{ $log->reset_to_level }})</span>
                        @else
                          <span class="badge bg-info"><i class="ti ti-send me-1"></i>Submitted</span>
                        @endif
                      </td>
                      <td class="small text-muted">{{ $log->remarks ?? 'No remarks' }}</td>
                      <td class="small font-semibold">{{ $log->user->name ?? 'System' }}</td>
                      <td class="small text-secondary">{{ $log->created_at->format('M d, Y h:i A') }}</td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="5" class="text-center text-muted py-3">No review logs recorded yet.</td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Column: Action Card & Review Panel -->
      <div class="col-lg-4">
        @can('case-approve')
          @if(!$case->is_closed)
            <div class="card border-primary shadow-sm mb-4">
              <div class="card-header bg-primary text-white fw-bold d-flex justify-content-between align-items-center">
                <span><i class="ti ti-shield-check me-2"></i> Review & Approve Level {{ $case->current_level }}</span>
                <span class="badge bg-white text-primary">Level {{ $case->current_level }}</span>
              </div>
              <div class="card-body p-4">
                <p class="small text-muted mb-3">
                  Review uploaded files and remarks for <strong>Level {{ $case->current_level }}</strong>. You can approve to advance the case or reject to reset levels.
                </p>

                <!-- Approval Form -->
                <form action="{{ route('cases.review-level', $case->id) }}" method="POST" class="mb-3">
                  @csrf
                  <input type="hidden" name="action" value="approve">
                  
                  <div class="mb-3">
                    <label for="approve_remarks" class="form-label fw-semibold small">Approval Remarks (Optional)</label>
                    <textarea class="form-control form-control-sm" id="approve_remarks" name="remarks" rows="2" placeholder="e.g. All photos verified and meet specifications..."></textarea>
                  </div>

                  <button type="submit" class="btn btn-success w-100 py-2">
                    <i class="ti ti-check me-1"></i> Approve Level {{ $case->current_level }} {{ $case->current_level == 3 ? '& Close Case' : '' }}
                  </button>
                </form>

                <hr class="my-3">

                <!-- Reject Button (Triggers Modal) -->
                <button type="button" class="btn btn-outline-danger w-100 py-2" data-bs-toggle="modal" data-bs-target="#rejectModal">
                  <i class="ti ti-x me-1"></i> Reject Level {{ $case->current_level }}
                </button>
              </div>
            </div>
          @endif
        @endcan

        <!-- Quick Summary Card -->
        <div class="card mb-4">
          <div class="card-header bg-light fw-bold">
            <i class="ti ti-paperclip me-2"></i> Files Summary
          </div>
          <div class="card-body p-4">
            <div class="d-flex justify-content-between mb-2">
              <span class="text-muted">Total Files Uploaded:</span>
              <span class="fw-bold">{{ $case->files->count() }}</span>
            </div>
            <div class="d-flex justify-content-between mb-2">
              <span class="text-muted">Level 1 Files:</span>
              <span class="fw-bold">{{ $level1Files->count() }}</span>
            </div>
            <div class="d-flex justify-content-between mb-2">
              <span class="text-muted">Level 2 Files:</span>
              <span class="fw-bold">{{ $level2Files->count() }}</span>
            </div>
            <div class="d-flex justify-content-between mb-2">
              <span class="text-muted">Level 3 Files:</span>
              <span class="fw-bold">{{ $level3Files->count() }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Rejection Modal -->
@if(!$case->is_closed)
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="{{ route('cases.review-level', $case->id) }}" method="POST">
        @csrf
        <input type="hidden" name="action" value="reject">

        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title text-white" id="rejectModalLabel"><i class="ti ti-alert-triangle me-2"></i> Reject Level {{ $case->current_level }}</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4">
          <p class="text-muted small mb-3">
            Please specify the reason for rejection and select which level the case should be reset to.
          </p>

          <div class="mb-3">
            <label for="reset_to_level" class="form-label fw-semibold">Reset Back To Level <span class="text-danger">*</span></label>
            <select class="form-select" id="reset_to_level" name="reset_to_level" required>
              @if($case->current_level == 1)
                <option value="1">Level 1 (Re-upload & changes at Level 1)</option>
              @elseif($case->current_level == 2)
                <option value="2">Level 2 (Stay at Level 2 for corrections)</option>
                <option value="1">Level 1 (Reset back to Level 1)</option>
              @elseif($case->current_level == 3)
                <option value="3">Level 3 (Stay at Level 3 for corrections)</option>
                <option value="2">Level 2 (Reset back to Level 2)</option>
                <option value="1">Level 1 (Reset back to Level 1)</option>
              @endif
            </select>
          </div>

          <div class="mb-3">
            <label for="reject_remarks" class="form-label fw-semibold">Rejection Reason / Required Changes <span class="text-danger">*</span></label>
            <textarea class="form-control" id="reject_remarks" name="remarks" rows="3" required placeholder="Explain why the level was rejected and what changes are needed..."></textarea>
          </div>
        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger">Confirm Rejection</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endif

<!-- Image Modal Preview -->
<div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="previewFileName">Photo Preview</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center p-0">
        <img src="" id="previewImg" class="img-fluid" style="max-height: 80vh; object-fit: contain;" alt="">
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.js"></script>
<script>
  Dropzone.autoDiscover = false;

  $(document).ready(function() {
    // Image Preview Modal Handler
    $('.preview-btn').on('click', function() {
      var url = $(this).data('url');
      var name = $(this).data('name');
      $('#previewImg').attr('src', url);
      $('#previewFileName').text(name);
      $('#imagePreviewModal').modal('show');
    });

    // Delete File Handler
    $('.delete-file-btn').on('click', function() {
      var fileId = $(this).data('id');
      var fileName = $(this).data('name');

      Swal.fire({
        title: 'Delete File?',
        text: 'Are you sure you want to delete "' + fileName + '"?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        confirmButtonText: 'Yes, Delete'
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: "{{ url('cases/file') }}/" + fileId,
            type: 'DELETE',
            data: { _token: "{{ csrf_token() }}" },
            success: function(response) {
              if (response.success) {
                Swal.fire('Deleted!', response.message, 'success').then(() => {
                  location.reload();
                });
              } else {
                Swal.fire('Error!', response.message, 'error');
              }
            }
          });
        }
      });
    });
  });
</script>
@endpush
