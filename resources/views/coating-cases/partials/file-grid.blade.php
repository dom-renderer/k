<div class="row g-3 mb-4">
  @forelse($files as $file)
    <div class="col-sm-6 col-md-4 col-xl-3">
      <div class="card h-100 border file-card">
        @if($file->is_image)
          <img src="{{ $file->file_url }}" class="card-img-top preview-thumb preview-btn" style="cursor: pointer;" data-url="{{ $file->file_url }}" data-name="{{ $file->file_name }}" alt="{{ $file->file_name }}">
        @else
          <div class="card-img-top preview-thumb bg-light d-flex align-items-center justify-content-center text-primary">
            <i class="ti ti-file-text fs-1"></i>
          </div>
        @endif
        <div class="card-body p-3 d-flex flex-column justify-content-between">
          <div>
            <div class="fw-semibold text-truncate small mb-1" title="{{ $file->file_name }}">{{ $file->file_name }}</div>
            <div class="small text-muted mb-2">
              <span class="badge bg-light text-dark border">{{ number_format($file->file_size / 1024, 1) }} KB</span>
            </div>
          </div>
          <div class="d-flex justify-content-between align-items-center pt-2 border-top">
            <div class="small text-muted">{{ $file->created_at->format('M d, H:i') }}</div>
            <div class="btn-group btn-group-sm">
              @if($file->is_image)
                <button type="button" class="btn btn-light btn-icon preview-btn" data-url="{{ $file->file_url }}" data-name="{{ $file->file_name }}" title="Preview">
                  <i class="ti ti-eye"></i>
                </button>
              @endif

              @can('case-download')
                <a href="{{ route('cases.download-file', $file->id) }}" class="btn btn-light btn-icon" title="Download">
                  <i class="ti ti-download"></i>
                </a>
              @endcan

              @can('case-edit')
                @if(!$case->is_closed)
                  <button type="button" class="btn btn-light btn-icon text-danger delete-file-btn" data-id="{{ $file->id }}" data-name="{{ $file->file_name }}" title="Delete">
                    <i class="ti ti-trash"></i>
                  </button>
                @endif
              @endcan
            </div>
          </div>
        </div>
      </div>
    </div>
  @empty
    <div class="col-12 text-center text-muted py-4">
      <i class="ti ti-file-off fs-1 mb-2 d-block"></i>
      No files uploaded for Level {{ $level }} yet.
    </div>
  @endforelse
</div>

@can('case-edit')
  @if(!$case->is_closed)
    <div class="card border bg-light-subtle">
      <div class="card-body p-3">
        <h6 class="fw-bold mb-2"><i class="ti ti-cloud-upload me-1"></i> Upload Additional Level {{ $level }} Files & Photos</h6>
        <div class="dropzone dropzone-level" id="dropzoneLevel{{ $level }}">
          <div class="dz-message text-center">
            <i class="ti ti-cloud-upload fs-2 text-primary d-block mb-1"></i>
            <span class="small font-medium">Drop Level {{ $level }} files here or click to browse</span>
          </div>
        </div>
      </div>
    </div>

    @push('scripts')
    <script>
      $(document).ready(function() {
        if ($("#dropzoneLevel{{ $level }}").length) {
          new Dropzone("#dropzoneLevel{{ $level }}", {
            url: "{{ route('cases.upload-file') }}",
            headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
            paramName: "file",
            maxFilesize: 25,
            init: function() {
              this.on("sending", function(file, xhr, formData) {
                formData.append("case_id", "{{ $case->id }}");
                formData.append("level", "{{ $level }}");
                formData.append("category", file.type.includes('image') ? 'photo' : 'document');
              });
              this.on("success", function() {
                location.reload();
              });
            }
          });
        }
      });
    </script>
    @endpush
  @endif
@endcan
