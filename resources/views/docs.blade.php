@extends('layouts.app')

@section('title', 'User Guide & System Manual - Coating Case Management')

@push('styles')
<style>
  .doc-hero {
    background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
    border-radius: 16px;
    color: #ffffff;
    padding: 2.5rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
    position: relative;
    overflow: hidden;
  }
  .doc-hero::after {
    content: '';
    position: absolute;
    top: -50px;
    right: -50px;
    width: 250px;
    height: 250px;
    background: radial-gradient(circle, rgba(37, 99, 235, 0.3) 0%, rgba(255, 255, 255, 0) 70%);
    border-radius: 50%;
  }
  .doc-search-box {
    max-width: 600px;
    position: relative;
  }
  .doc-search-box input {
    height: 52px;
    border-radius: 26px;
    padding-left: 3rem;
    font-size: 1rem;
    box-shadow: 0 4px 14px rgba(0,0,0,0.1);
  }
  .doc-search-box .search-icon {
    position: absolute;
    left: 1.25rem;
    top: 50%;
    transform: translateY(-50%);
    color: #64748b;
    font-size: 1.25rem;
  }
  .doc-nav-pills .nav-link {
    border-radius: 30px;
    padding: 0.6rem 1.25rem;
    font-weight: 600;
    color: #475569;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    transition: all 0.25s ease;
  }
  .doc-nav-pills .nav-link.active, .doc-nav-pills .nav-link:hover {
    background: var(--bs-primary);
    color: #ffffff;
    border-color: var(--bs-primary);
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
  }
  .workflow-card {
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
    background: #ffffff;
  }
  .workflow-card:hover, .workflow-card.active {
    border-color: var(--bs-primary);
    transform: translateY(-4px);
    box-shadow: 0 12px 24px rgba(37, 99, 235, 0.12);
  }
  .workflow-badge {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 1.1rem;
  }
  .feature-box {
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    padding: 1.75rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
  }
  .interactive-demo-card {
    background: #f8fafc;
    border: 2px dashed #cbd5e1;
    border-radius: 12px;
    padding: 1.25rem;
  }
  .step-number {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    background: var(--bs-primary);
    color: #ffffff;
    border-radius: 50%;
    font-weight: 700;
    font-size: 0.85rem;
    margin-right: 0.5rem;
  }
  .highlight-tag {
    background: #eff6ff;
    color: #1d4ed8;
    border: 1px solid #bfdbfe;
    padding: 0.2rem 0.6rem;
    border-radius: 6px;
    font-size: 0.85rem;
    font-weight: 600;
  }
</style>
@endpush

@section('content')
<div class="row">
  <div class="col-12">
    <!-- Interactive Hero Banner -->
    <div class="doc-hero mb-4">
      <div class="row align-items-center">
        <div class="col-lg-8">
          <span class="badge bg-primary px-3 py-2 text-uppercase letter-spacing-1 mb-2">Interactive Manual</span>
          <h1 class="display-6 fw-bold mb-2">Coating Case Management Guide</h1>
          <p class="lead opacity-87 mb-4">
            A simple, visual step-by-step guide designed for non-technical users to easily understand workflow approvals, document uploads, unique OA generation, and case reopening.
          </p>

          <!-- Live Instant Search -->
          <div class="doc-search-box">
            <i class="ti ti-search search-icon"></i>
            <input type="text" id="docSearchInput" class="form-control" placeholder="Search guide (e.g., 'OA Number', 'Approve', 'Reopen', 'Upload photos')...">
          </div>
        </div>
        <div class="col-lg-4 text-center d-none d-lg-block">
          <div class="p-3 bg-white bg-opacity-10 backdrop-blur rounded-4 border border-white border-opacity-20 text-center">
            <i class="ti ti-shield-check text-warning display-3 mb-2 d-block"></i>
            <h5 class="text-white mb-1">3-Level Approval System</h5>
            <small class="text-light opacity-75">Quality assurance & complete audit trails for every coating case.</small>
          </div>
        </div>
      </div>
    </div>

    <!-- Sticky Navigation Tabs -->
    <div class="mb-4">
      <div class="d-flex gap-2 flex-wrap doc-nav-pills" id="docCategoryTabs">
        <button class="nav-link active" data-target="all"><i class="ti ti-apps me-1"></i> All Topics</button>
        <button class="nav-link" data-target="workflow"><i class="ti ti-git-merge me-1"></i> 3-Level Workflow</button>
        <button class="nav-link" data-target="oa-generator"><i class="ti ti-sparkles me-1"></i> OA Number Generator</button>
        <button class="nav-link" data-target="file-upload"><i class="ti ti-cloud-upload me-1"></i> Uploading Photos & Files</button>
        <button class="nav-link" data-target="approvals"><i class="ti ti-circle-check me-1"></i> Review & Approvals</button>
        <button class="nav-link" data-target="reopen"><i class="ti ti-rotate-clockwise me-1"></i> Reopening Closed Cases</button>
        <button class="nav-link" data-target="roles"><i class="ti ti-user-check me-1"></i> Roles & Permissions</button>
        <button class="nav-link" data-target="faq"><i class="ti ti-help-circle me-1"></i> FAQ</button>
      </div>
    </div>

    <!-- SECTION: 3-LEVEL WORKFLOW VISUALIZER -->
    <div class="feature-box doc-section" id="section-workflow" data-keywords="workflow level 1 level 2 level 3 pre-coating coating application review approval closed">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="h4 fw-bold text-dark mb-0">
          <i class="ti ti-git-merge text-primary me-2"></i> The 3-Level Coating Approval Workflow
        </h3>
        <span class="highlight-tag">Core Concept</span>
      </div>
      <p class="text-muted">
        Every coating case undergoes a strict 3-stage quality approval lifecycle to ensure all specifications and photos are validated before final closure.
      </p>

      <!-- Interactive Stepper Cards -->
      <div class="row g-3 mb-4">
        <div class="col-md-4">
          <div class="workflow-card p-3 active" id="cardLevel1" onclick="showLevelDetails(1)">
            <div class="d-flex align-items-center gap-3">
              <div class="workflow-badge bg-primary text-white">1</div>
              <div>
                <h5 class="fw-bold mb-0 fs-6">Level 1: Pre-Coating</h5>
                <small class="text-muted">Photos & Initial Specs</small>
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-4">
          <div class="workflow-card p-3" id="cardLevel2" onclick="showLevelDetails(2)">
            <div class="d-flex align-items-center gap-3">
              <div class="workflow-badge bg-info text-white">2</div>
              <div>
                <h5 class="fw-bold mb-0 fs-6">Level 2: Application</h5>
                <small class="text-muted">In-Progress Application Docs</small>
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-4">
          <div class="workflow-card p-3" id="cardLevel3" onclick="showLevelDetails(3)">
            <div class="d-flex align-items-center gap-3">
              <div class="workflow-badge bg-success text-white">3</div>
              <div>
                <h5 class="fw-bold mb-0 fs-6">Level 3: After-Coating</h5>
                <small class="text-muted">Final Review & Case Closure</small>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Dynamic Level Info Card -->
      <div class="card bg-light border-0" id="levelInfoDisplay">
        <div class="card-body p-4">
          <div class="d-flex align-items-center gap-2 mb-2">
            <span class="badge bg-primary text-white fs-6" id="levelDisplayTitle">Level 1: Pre-Coating Stage</span>
            <span class="badge bg-outline-primary" id="levelDisplayBadge">Status: Level 1 Pending</span>
          </div>
          <p class="mb-3" id="levelDisplayDesc">
            In this initial stage, operators or inspectors upload pre-coating photos and specifications for the selected equipment. Once files are attached, an authorized reviewer evaluates the files and approves the case to advance to Level 2.
          </p>
          <div class="row g-2">
            <div class="col-sm-6">
              <div class="bg-white p-2 border rounded">
                <i class="ti ti-check text-success me-1"></i> <strong class="small">Action:</strong> <span class="small text-muted" id="levelDisplayAction">Upload pre-coating photos & click Approve Level 1</span>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="bg-white p-2 border rounded">
                <i class="ti ti-arrow-right text-primary me-1"></i> <strong class="small">Next Step:</strong> <span class="small text-muted" id="levelDisplayNext">Advances case to Level 2 Application stage</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- SECTION: OA NUMBER GENERATOR -->
    <div class="feature-box doc-section" id="section-oa-generator" data-keywords="oa number generate unique code generator auto generate check oa">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="h4 fw-bold text-dark mb-0">
          <i class="ti ti-sparkles text-primary me-2"></i> Automated OA Number Generator
        </h3>
        <span class="highlight-tag">New Feature</span>
      </div>
      <p class="text-muted">
        OA Numbers represent unique Job/Order Assembly identifiers. The system provides an instant generator button to eliminate duplicate numbers and manual typing errors.
      </p>

      <div class="row g-4 align-items-center">
        <div class="col-lg-6">
          <ol class="list-unstyled mb-0">
            <li class="mb-3">
              <span class="step-number">1</span> <strong>Click the "Generate" button</strong> located right next to the OA Number field when creating or editing a case.
            </li>
            <li class="mb-3">
              <span class="step-number">2</span> <strong>Backend Uniqueness Check</strong>: The system generates a formatted code like <code>OA-482019</code> and instantly checks the database to confirm it isn't used.
            </li>
            <li class="mb-3">
              <span class="step-number">3</span> <strong>Instant Validation Badge</strong>: A green badge <span class="badge bg-success"><i class="ti ti-circle-check"></i> OA Number is available</span> appears automatically.
            </li>
          </ol>
        </div>

        <!-- Live Interactive Simulation -->
        <div class="col-lg-6">
          <div class="interactive-demo-card">
            <label class="form-label fw-bold small text-muted">Try the Live Generator Demo:</label>
            <div class="input-group mb-2">
              <input type="text" class="form-control" id="demoOaInput" value="OA-294810" readonly>
              <button class="btn btn-primary" type="button" id="demoGenerateBtn">
                <i class="ti ti-refresh me-1"></i> Generate
              </button>
            </div>
            <div id="demoOaFeedback" class="small text-success fw-semibold">
              <i class="ti ti-circle-check me-1"></i> OA Number is available.
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- SECTION: FILE UPLOADS -->
    <div class="feature-box doc-section" id="section-file-upload" data-keywords="upload file photos drag drop preview download delete documents level 1 level 2 level 3">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="h4 fw-bold text-dark mb-0">
          <i class="ti ti-cloud-upload text-primary me-2"></i> Uploading Photos & Inspection Documents
        </h3>
        <span class="highlight-tag">Drag & Drop</span>
      </div>
      <p class="text-muted">
        Photos and documents are organized neatly under tabs for <strong>Level 1</strong>, <strong>Level 2</strong>, and <strong>Level 3</strong>.
      </p>

      <div class="row g-3">
        <div class="col-md-4">
          <div class="card h-100 border p-3">
            <div class="text-primary fs-3 mb-2"><i class="ti ti-upload"></i></div>
            <h6 class="fw-bold mb-1">1. Easy Drag & Drop</h6>
            <p class="small text-muted mb-0">Simply drag photos or PDF documents directly onto the upload zone under the active level tab.</p>
          </div>
        </div>

        <div class="col-md-4">
          <div class="card h-100 border p-3">
            <div class="text-success fs-3 mb-2"><i class="ti ti-eye"></i></div>
            <h6 class="fw-bold mb-1">2. Instant Photo Preview</h6>
            <p class="small text-muted mb-0">Click on any image thumbnail to view full high-resolution photo popups right inside your browser.</p>
          </div>
        </div>

        <div class="col-md-4">
          <div class="card h-100 border p-3">
            <div class="text-danger fs-3 mb-2"><i class="ti ti-trash"></i></div>
            <h6 class="fw-bold mb-1">3. File Deletion & Download</h6>
            <p class="small text-muted mb-0">Download attached files anytime, or delete incorrect photos (requires confirmation before deletion).</p>
          </div>
        </div>
      </div>
    </div>

    <!-- SECTION: REVIEWS & APPROVALS -->
    <div class="feature-box doc-section" id="section-approvals" data-keywords="approve rejection reset sweetalert level 1 approve level 2 approve level 3 review remarks">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="h4 fw-bold text-dark mb-0">
          <i class="ti ti-circle-check text-primary me-2"></i> Reviewing, Approving & Rejecting Cases
        </h3>
        <span class="highlight-tag">Safety Confirmation</span>
      </div>
      <p class="text-muted">
        Authorized reviewers can approve or reject coating cases from the right-hand action panel on the Case Details page.
      </p>

      <div class="row g-4">
        <div class="col-md-6">
          <div class="card border-success h-100 p-4">
            <h5 class="fw-bold text-success mb-2"><i class="ti ti-check me-1"></i> Approving a Level</h5>
            <p class="small text-muted mb-3">
              When all photos and documentation for the current level are verified, type optional approval remarks and click <strong>Approve Level X</strong>.
            </p>
            <div class="alert alert-success py-2 px-3 small mb-0">
              <i class="ti ti-info-circle me-1"></i> <strong>SweetAlert Safety Prompt:</strong> Clicking approve triggers a popup asking: <em>"Approve Level X and advance case?"</em> to prevent accidental clicks.
            </div>
          </div>
        </div>

        <div class="col-md-6">
          <div class="card border-danger h-100 p-4">
            <h5 class="fw-bold text-danger mb-2"><i class="ti ti-x me-1"></i> Rejecting a Level</h5>
            <p class="small text-muted mb-3">
              If photos are missing or coating application fails quality control, click <strong>Reject Level X</strong>.
            </p>
            <ul class="small text-muted ps-3 mb-0">
              <li>Specify compulsory rejection reasons.</li>
              <li>Select which level (Level 1, 2, or 3) to reset the case back to for corrections.</li>
            </ul>
          </div>
        </div>
      </div>
    </div>

    <!-- SECTION: REOPENING CLOSED CASES -->
    <div class="feature-box doc-section" id="section-reopen" data-keywords="reopen closed case unlock reset level set level reopened approval status">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="h4 fw-bold text-dark mb-0">
          <i class="ti ti-rotate-clockwise text-warning me-2"></i> Reopening Closed Coating Cases
        </h3>
        <span class="highlight-tag">Manager Option</span>
      </div>
      <p class="text-muted">
        When a case reaches Level 3 final approval, it becomes <strong>Closed & Locked</strong>. If additional coating work or re-inspection is needed later, managers can reopen the case!
      </p>

      <div class="row align-items-center g-4">
        <div class="col-lg-7">
          <ol class="list-unstyled mb-0">
            <li class="mb-3">
              <span class="step-number bg-warning text-dark">1</span> <strong>Click "Reopen Case"</strong>: Located in the top alert banner or right sidebar panel on closed cases.
            </li>
            <li class="mb-3">
              <span class="step-number bg-warning text-dark">2</span> <strong>Select Target Level</strong>: Choose whether to set the case back to <strong>Level 1</strong> (Pre-Coating), <strong>Level 2</strong> (Application), or <strong>Level 3</strong> (Final Review).
            </li>
            <li class="mb-3">
              <span class="step-number bg-warning text-dark">3</span> <strong>Confirm Reopening</strong>: Provide optional remarks and confirm via SweetAlert. Photo upload dropzones and edit controls become active again immediately!
            </li>
          </ol>
        </div>

        <div class="col-lg-5">
          <div class="card border-warning bg-warning bg-opacity-10 p-3 text-center">
            <i class="ti ti-lock-open text-warning display-4 mb-2"></i>
            <h6 class="fw-bold text-dark mb-1">Full Audit Trail Maintained</h6>
            <p class="small text-muted mb-0">Every reopening action is automatically recorded in the Review Audit Log with dates, user names, and level choices.</p>
          </div>
        </div>
      </div>
    </div>

    <!-- SECTION: ROLES & PERMISSIONS -->
    <div class="feature-box doc-section" id="section-roles" data-keywords="roles permissions admin inspector staff operator access permissions list">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="h4 fw-bold text-dark mb-0">
          <i class="ti ti-user-check text-primary me-2"></i> Roles & User Access Summary
        </h3>
        <span class="highlight-tag">Security & Control</span>
      </div>

      <div class="table-responsive">
        <table class="table table-bordered align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>Role</th>
              <th>Create & Edit Cases</th>
              <th>Upload & Delete Files</th>
              <th>Approve / Reject Levels</th>
              <th>Reopen Closed Cases</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><span class="badge bg-primary">Admin User</span></td>
              <td class="text-center text-success"><i class="ti ti-check fs-4"></i></td>
              <td class="text-center text-success"><i class="ti ti-check fs-4"></i></td>
              <td class="text-center text-success"><i class="ti ti-check fs-4"></i></td>
              <td class="text-center text-success"><i class="ti ti-check fs-4"></i></td>
            </tr>
            <tr>
              <td><span class="badge bg-info">Inspector / Reviewer</span></td>
              <td class="text-center text-success"><i class="ti ti-check fs-4"></i></td>
              <td class="text-center text-success"><i class="ti ti-check fs-4"></i></td>
              <td class="text-center text-success"><i class="ti ti-check fs-4"></i></td>
              <td class="text-center text-success"><i class="ti ti-check fs-4"></i></td>
            </tr>
            <tr>
              <td><span class="badge bg-secondary">Operator / Staff</span></td>
              <td class="text-center text-success"><i class="ti ti-check fs-4"></i></td>
              <td class="text-center text-success"><i class="ti ti-check fs-4"></i></td>
              <td class="text-center text-muted"><i class="ti ti-minus fs-4"></i></td>
              <td class="text-center text-muted"><i class="ti ti-minus fs-4"></i></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- SECTION: FREQUENTLY ASKED QUESTIONS -->
    <div class="feature-box doc-section" id="section-faq" data-keywords="faq question answer duplicate oa search sector equipment closed error">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="h4 fw-bold text-dark mb-0">
          <i class="ti ti-help-circle text-primary me-2"></i> Frequently Asked Questions (FAQ)
        </h3>
        <span class="highlight-tag">Help Desk</span>
      </div>

      <div class="accordion" id="docFaqAccordion">
        <div class="accordion-item">
          <h2 class="accordion-header" id="faqHeadingOne">
            <button class="accordion-button fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapseOne">
              How does the OA Number uniqueness check work?
            </button>
          </h2>
          <div id="faqCollapseOne" class="accordion-collapse collapse show" data-bs-parent="#docFaqAccordion">
            <div class="accordion-body text-muted">
              When you type or generate an OA Number, the system instantly sends an asynchronous request to check existing coating cases in the database. If an existing active case uses the same number, an alert appears and form submission is disabled to prevent duplicate records.
            </div>
          </div>
        </div>

        <div class="accordion-item">
          <h2 class="accordion-header" id="faqHeadingTwo">
            <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapseTwo">
              Can I upload multiple photos at once?
            </button>
          </h2>
          <div id="faqCollapseTwo" class="accordion-collapse collapse" data-bs-parent="#docFaqAccordion">
            <div class="accordion-body text-muted">
              Yes! You can select multiple image files or drag a batch of photos into the Dropzone upload area under Level 1, Level 2, or Level 3.
            </div>
          </div>
        </div>

        <div class="accordion-item">
          <h2 class="accordion-header" id="faqHeadingThree">
            <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapseThree">
              What happens when a case is rejected?
            </button>
          </h2>
          <div id="faqCollapseThree" class="accordion-collapse collapse" data-bs-parent="#docFaqAccordion">
            <div class="accordion-body text-muted">
              When a reviewer rejects a level, they specify compulsory rejection remarks and select which level (Level 1, 2, or 3) to reset back to. The status changes to "Level X Rejected", and operators can fix issues or re-upload photos before re-submitting for approval.
            </div>
          </div>
        </div>

        <div class="accordion-item">
          <h2 class="accordion-header" id="faqHeadingFour">
            <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapseFour">
              How do I search or filter coating cases?
            </button>
          </h2>
          <div id="faqCollapseFour" class="accordion-collapse collapse" data-bs-parent="#docFaqAccordion">
            <div class="accordion-body text-muted">
              On the main Coating Cases page, click the <strong>Filter Accordion</strong>. You can filter cases by Sector, Equipment, Level, or Status using searchable Select2 dropdowns, or use the global search box to search by OA Number or Case Number.
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>
@endsection

@push('scripts')
<script>
  function showLevelDetails(level) {
    $('.workflow-card').removeClass('active');
    $('#cardLevel' + level).addClass('active');

    const details = {
      1: {
        title: 'Level 1: Pre-Coating Stage',
        badge: 'Status: Level 1 Pending',
        desc: 'Operators upload initial pre-coating photos, surface preparation specs, and equipment details. Reviewers verify photos before advancing to Level 2.',
        action: 'Upload pre-coating photos & click Approve Level 1',
        next: 'Advances case to Level 2 Coating Application stage'
      },
      2: {
        title: 'Level 2: Coating Application Stage',
        badge: 'Status: Level 2 Pending',
        desc: 'In-progress coating thickness logs, temperature checks, and application photos are uploaded here. Evaluators inspect application quality.',
        action: 'Upload application logs & click Approve Level 2',
        next: 'Advances case to Level 3 After-Coating Review stage'
      },
      3: {
        title: 'Level 3: After-Coating Review & Closure',
        badge: 'Status: Level 3 Pending',
        desc: 'Final inspection, dry film thickness verification, and post-coating photos are reviewed. Approving Level 3 grants final closure and locks the case.',
        action: 'Perform final review & click Approve Level 3 & Close Case',
        next: 'Marks coating case as Closed & Approved'
      }
    };

    const item = details[level];
    $('#levelDisplayTitle').text(item.title);
    $('#levelDisplayBadge').text(item.badge);
    $('#levelDisplayDesc').text(item.desc);
    $('#levelDisplayAction').text(item.action);
    $('#levelDisplayNext').text(item.next);
  }

  $(document).ready(function() {
    // Live Search Filter for Manual Sections
    $('#docSearchInput').on('input', function() {
      const query = $(this).val().toLowerCase().trim();

      if (query === '') {
        $('.doc-section').show();
        return;
      }

      $('.doc-section').each(function() {
        const keywords = ($(this).data('keywords') || '').toLowerCase();
        const textContent = $(this).text().toLowerCase();

        if (keywords.includes(query) || textContent.includes(query)) {
          $(this).show();
        } else {
          $(this).hide();
        }
      });
    });

    // Navigation Category Tabs
    $('#docCategoryTabs .nav-link').on('click', function() {
      $('#docCategoryTabs .nav-link').removeClass('active');
      $(this).addClass('active');

      const target = $(this).data('target');
      if (target === 'all') {
        $('.doc-section').show();
      } else {
        $('.doc-section').hide();
        $('#section-' + target).show();
      }
    });

    // Demo OA Generator Button Interactive Simulation
    $('#demoGenerateBtn').on('click', function() {
      const btn = $(this);
      btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Generating...');

      setTimeout(function() {
        const randomNum = Math.floor(100000 + Math.random() * 900000);
        const generated = 'OA-' + randomNum;
        $('#demoOaInput').val(generated);
        $('#demoOaFeedback').html('<i class="ti ti-circle-check me-1"></i> Unique OA Number <strong>' + generated + '</strong> generated & verified!').removeClass('text-muted').addClass('text-success');
        btn.prop('disabled', false).html('<i class="ti ti-refresh me-1"></i> Generate');
      }, 500);
    });
  });
</script>
@endpush
