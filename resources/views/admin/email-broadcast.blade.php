@extends('layouts.dashboard')

@section('title', 'Send Broadcast Emails - ' . ($settings->platform_name ?? 'DOOTOR ENTERPRISES'))

@section('content')
<div class="mb-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
    <div>
        <h1 class="h3 fw-bold text-dark mb-1"><i class="bi bi-send-check me-2 text-success"></i>Send Broadcast Emails</h1>
        <p class="text-secondary small mb-0">Dispatch system maintenance notices, announcements, service updates, or custom emails to registered members or external recipients.</p>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center gap-3 p-3">
        <i class="bi bi-check-circle-fill fs-4 text-success"></i>
        <div>
            <strong class="d-block">Success!</strong>
            <span class="small">{{ session('success') }}</span>
        </div>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center gap-3 p-3">
        <i class="bi bi-exclamation-triangle-fill fs-4 text-danger"></i>
        <div>
            <strong class="d-block">Error Sending Email</strong>
            <span class="small">{{ session('error') }}</span>
        </div>
    </div>
@endif

<!-- Quick Presets Toolbar -->
<div class="card border-0 shadow-sm p-3 rounded-4 bg-white mb-4">
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <span class="small fw-bold text-dark me-2"><i class="bi bi-magic me-1"></i> Quick Presets:</span>
        <button type="button" class="btn btn-outline-warning btn-sm rounded-pill px-3 fw-medium" onclick="applyPreset('maintenance')">
            🛠️ Scheduled Maintenance
        </button>
        <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-medium" onclick="applyPreset('announcement')">
            📢 System Announcement
        </button>
        <button type="button" class="btn btn-outline-success btn-sm rounded-pill px-3 fw-medium" onclick="applyPreset('service_update')">
            ⚡ Service Alert
        </button>
        <button type="button" class="btn btn-outline-purple btn-sm rounded-pill px-3 fw-medium" style="color: #7e22ce; border-color: #d8b4fe;" onclick="applyPreset('custom')">
            ✉️ Direct Member Email
        </button>
    </div>
</div>

<div class="row g-4">
    <!-- Form Composer (Left Column) -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
            <h2 class="h5 fw-bold text-dark mb-3"><i class="bi bi-pencil-square me-2"></i> Compose Broadcast Notice</h2>

            <form action="{{ route('admin.email-broadcast.send') }}" method="POST" id="broadcastForm">
                @csrf

                <!-- Notice Category Selection -->
                <div class="mb-4">
                    <label class="form-label small fw-bold text-dark">1. Select Notice Category</label>
                    <div class="row g-2">
                        <div class="col-6 col-sm-3">
                            <input type="radio" class="btn-check" name="notice_category" id="cat_maintenance" value="maintenance" checked onchange="updatePreview()">
                            <label class="btn btn-outline-warning w-100 p-2.5 rounded-3 text-start small fw-semibold d-flex flex-column gap-1" for="cat_maintenance">
                                <span>🛠️ Maintenance</span>
                                <span class="text-muted" style="font-size: 10px; font-weight: normal;">Downtime Notice</span>
                            </label>
                        </div>
                        <div class="col-6 col-sm-3">
                            <input type="radio" class="btn-check" name="notice_category" id="cat_announcement" value="announcement" onchange="updatePreview()">
                            <label class="btn btn-outline-primary w-100 p-2.5 rounded-3 text-start small fw-semibold d-flex flex-column gap-1" for="cat_announcement">
                                <span>📢 Announcement</span>
                                <span class="text-muted" style="font-size: 10px; font-weight: normal;">General News</span>
                            </label>
                        </div>
                        <div class="col-6 col-sm-3">
                            <input type="radio" class="btn-check" name="notice_category" id="cat_service" value="service_update" onchange="updatePreview()">
                            <label class="btn btn-outline-success w-100 p-2.5 rounded-3 text-start small fw-semibold d-flex flex-column gap-1" for="cat_service">
                                <span>⚡ Service Update</span>
                                <span class="text-muted" style="font-size: 10px; font-weight: normal;">Catalog & Pricing</span>
                            </label>
                        </div>
                        <div class="col-6 col-sm-3">
                            <input type="radio" class="btn-check" name="notice_category" id="cat_custom" value="custom" onchange="updatePreview()">
                            <label class="btn btn-outline-dark w-100 p-2.5 rounded-3 text-start small fw-semibold d-flex flex-column gap-1" for="cat_custom">
                                <span>✉️ Custom Mail</span>
                                <span class="text-muted" style="font-size: 10px; font-weight: normal;">Direct Message</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Recipient Target Selection -->
                <div class="mb-4">
                    <label class="form-label small fw-bold text-dark">2. Recipient Audience Group</label>
                    <select name="recipient_target" id="recipientTargetSelect" class="form-select rounded-3 mb-3" onchange="toggleRecipientOptions()">
                        <option value="all_clients">All Registered Clients (Clients Only)</option>
                        <option value="all_vendors">All Registered Vendors / Service Agents</option>
                        <option value="all_users">All System Users (Clients + Vendors + Staff)</option>
                        <option value="specific_user">Specific Registered Member (Select User)</option>
                        <option value="external_emails">External Email Address(es) (Custom Input)</option>
                    </select>

                    <!-- Specific User Selector Dropdown -->
                    <div id="specificUserWrapper" class="mb-3" style="display: none;">
                        <label for="specific_user_id" class="form-label small fw-medium text-dark">Select Registered Member</label>
                        <select name="specific_user_id" id="specific_user_id" class="form-select rounded-3">
                            <option value="">-- Choose User --</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}">{{ $u->first_name }} {{ $u->last_name }} ({{ $u->email }}) - [{{ ucfirst($u->role) }}]</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- External Email Input -->
                    <div id="externalEmailsWrapper" class="mb-3" style="display: none;">
                        <label for="external_emails" class="form-label small fw-medium text-dark">External Recipient Emails (Comma-separated)</label>
                        <input type="text" name="external_emails" id="external_emails" class="form-control rounded-3" placeholder="e.g. client@gmail.com, partner@company.com">
                        <small class="text-muted">You can list multiple external emails separated by commas.</small>
                    </div>
                </div>

                <!-- Subject Line -->
                <div class="mb-3">
                    <label for="email_subject" class="form-label small fw-bold text-dark">3. Email Subject</label>
                    <input type="text" name="email_subject" id="email_subject" class="form-control rounded-3" value="Scheduled System Maintenance Notice" placeholder="Subject line..." required oninput="updatePreview()">
                </div>

                <!-- Banner Headline -->
                <div class="mb-3">
                    <label for="email_headline" class="form-label small fw-medium text-dark">Headline Title (Inside Email)</label>
                    <input type="text" name="email_headline" id="email_headline" class="form-control rounded-3" value="Upcoming Platform Maintenance & Upgrade" placeholder="Banner headline inside email..." oninput="updatePreview()">
                </div>

                <!-- Email Content / Body -->
                <div class="mb-3">
                    <label for="email_body" class="form-label small fw-bold text-dark">4. Email Body Message</label>
                    <textarea name="email_body" id="email_body" class="form-control rounded-3" rows="6" placeholder="Type your email message here..." required oninput="updatePreview()">Dear Member,

Please be informed that we will be performing scheduled platform maintenance to optimize system processing and server infrastructure.

During this window, service processing may experience brief delays. All your application data and uploaded documents remain completely secure.

We apologize for any inconvenience and appreciate your patience as we work to serve you better.</textarea>
                    <small class="text-muted d-block mt-1">Tip: Use <code>{full_name}</code> or <code>{company_name}</code> for auto-formatting.</small>
                </div>

                <!-- Call to Action Button Options -->
                <div class="row g-3 mb-4">
                    <div class="col-sm-6">
                        <label for="button_text" class="form-label small fw-medium text-dark">Action Button Label (Optional)</label>
                        <input type="text" name="button_text" id="button_text" class="form-control rounded-3" value="Go to Dashboard" placeholder="e.g. Go to Dashboard" oninput="updatePreview()">
                    </div>
                    <div class="col-sm-6">
                        <label for="button_url" class="form-label small fw-medium text-dark">Action Button Link (URL)</label>
                        <input type="url" name="button_url" id="button_url" class="form-control rounded-3" value="{{ url('/login') }}" placeholder="https://..." oninput="updatePreview()">
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-dark rounded-pill px-4 py-2.5 fw-semibold shadow-sm" onclick="return confirm('Are you sure you want to dispatch this email broadcast to the selected audience?')">
                        <i class="bi bi-send-fill me-2 text-warning"></i>Dispatch Broadcast Email
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Live Interactive HTML Email Preview (Right Column) -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm p-4 rounded-4 bg-white sticky-top" style="top: 90px;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="h6 fw-bold text-dark mb-0"><i class="bi bi-eye me-2"></i>Live Email Preview</h3>
                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1 small">Real-time</span>
            </div>

            <!-- Email Container Preview Box -->
            <div class="border rounded-4 overflow-hidden shadow-sm bg-light" style="font-family: Arial, sans-serif; font-size: 13px;">
                <!-- Email Header -->
                <div style="background-color: #004225; padding: 18px 20px; text-align: center; border-bottom: 3px solid #d4af37;">
                    @if(!empty($settings->logo_url))
                        <img src="{{ app_file_url($settings->logo_url) }}" alt="Logo" style="max-height: 32px; vertical-align: middle; margin-right: 6px;">
                    @endif
                    <span style="color: #ffffff; font-weight: 700; font-size: 16px; vertical-align: middle;">{{ $settings->platform_name ?? 'DOOTOR ENTERPRISES' }}</span>
                </div>

                <!-- Email Body -->
                <div class="p-3 bg-white">
                    <div id="previewBadge" class="category-badge badge-maintenance mb-2" style="font-size: 10px; padding: 4px 10px; display: inline-block; border-radius: 50px; font-weight: 700; text-transform: uppercase;">
                        🛠️ SYSTEM MAINTENANCE NOTICE
                    </div>

                    <h4 id="previewHeadline" class="fw-bold text-dark mb-2 fs-6" style="line-height: 1.3;">Upcoming Platform Maintenance & Upgrade</h4>

                    <div class="text-dark fw-semibold small mb-2">Hello Valued Member,</div>

                    <div id="previewBody" class="text-secondary small mb-3" style="line-height: 1.5; white-space: pre-line;">
                        Dear Member,

                        Please be informed that we will be performing scheduled platform maintenance to optimize system processing and server infrastructure.
                    </div>

                    <div id="previewBtnWrapper" class="text-center my-3">
                        <a id="previewBtn" href="#" class="btn btn-sm text-white rounded-pill px-4 fw-bold shadow-sm" style="background-color: #004225; font-size: 12px;" target="_blank">
                            Go to Dashboard &rarr;
                        </a>
                    </div>
                </div>

                <!-- Email Footer -->
                <div class="p-2.5 bg-light text-center border-top text-muted" style="font-size: 10px;">
                    <div>&copy; {{ date('Y') }} {{ $settings->platform_name ?? 'DOOTOR ENTERPRISES' }}. All rights reserved.</div>
                    <div>Need assistance? <span class="text-success">support@dootor-enterprises.com</span></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function toggleRecipientOptions() {
        var val = document.getElementById('recipientTargetSelect').value;
        document.getElementById('specificUserWrapper').style.display = (val === 'specific_user') ? 'block' : 'none';
        document.getElementById('externalEmailsWrapper').style.display = (val === 'external_emails') ? 'block' : 'none';
    }

    function updatePreview() {
        var category = document.querySelector('input[name="notice_category"]:checked').value;
        var subject = document.getElementById('email_subject').value;
        var headline = document.getElementById('email_headline').value || subject;
        var body = document.getElementById('email_body').value;
        var btnText = document.getElementById('button_text').value;
        var btnUrl = document.getElementById('button_url').value;

        // Badge update
        var badge = document.getElementById('previewBadge');
        if (category === 'maintenance') {
            badge.className = 'category-badge badge-maintenance mb-2';
            badge.style.backgroundColor = '#fff9db';
            badge.style.color = '#b45309';
            badge.style.border = '1px solid #fde047';
            badge.innerText = '🛠️ SYSTEM MAINTENANCE NOTICE';
        } else if (category === 'announcement') {
            badge.className = 'category-badge badge-announcement mb-2';
            badge.style.backgroundColor = '#dbeafe';
            badge.style.color = '#1d4ed8';
            badge.style.border = '1px solid #93c5fd';
            badge.innerText = '📢 PLATFORM ANNOUNCEMENT';
        } else if (category === 'service_update') {
            badge.className = 'category-badge badge-service_update mb-2';
            badge.style.backgroundColor = '#dcfce7';
            badge.style.color = '#15803d';
            badge.style.border = '1px solid #86efac';
            badge.innerText = '⚡ SERVICE UPDATE & ALERT';
        } else {
            badge.className = 'category-badge badge-custom mb-2';
            badge.style.backgroundColor = '#f3e8ff';
            badge.style.color = '#7e22ce';
            badge.style.border = '1px solid #d8b4fe';
            badge.innerText = '✉️ OFFICIAL DIRECT NOTICE';
        }

        document.getElementById('previewHeadline').innerText = headline;
        document.getElementById('previewBody').innerText = body;

        var btnWrapper = document.getElementById('previewBtnWrapper');
        var btn = document.getElementById('previewBtn');
        if (btnText.trim() !== '') {
            btnWrapper.style.display = 'block';
            btn.innerText = btnText + ' \u2192';
            btn.href = btnUrl || '#';
        } else {
            btnWrapper.style.display = 'none';
        }
    }

    function applyPreset(presetType) {
        if (presetType === 'maintenance') {
            document.getElementById('cat_maintenance').checked = true;
            document.getElementById('email_subject').value = 'Scheduled System Maintenance Notice';
            document.getElementById('email_headline').value = 'Upcoming System Maintenance & Upgrade';
            document.getElementById('email_body').value = "Dear Valued Member,\n\nPlease be informed that we have scheduled platform maintenance to upgrade our system infrastructure and improve document processing speeds.\n\nDuring this maintenance window, service processing may experience brief pauses. All your account details, transactions, and uploaded files remain completely safe.\n\nThank you for your understanding!";
            document.getElementById('button_text').value = 'Check Application Status';
            document.getElementById('button_url').value = "{{ url('/login') }}";
        } else if (presetType === 'announcement') {
            document.getElementById('cat_announcement').checked = true;
            document.getElementById('email_subject').value = 'Exciting New Platform Announcement';
            document.getElementById('email_headline').value = 'New Features & Expedited Processing Available';
            document.getElementById('email_body').value = "Hello,\n\nWe are excited to announce new updates to our official document processing portal!\n\nYou can now fast-track passport approvals, NIN verifications, visa applications, and court affidavits directly from your secure client dashboard with real-time status tracking.\n\nLog in today to explore our expanded services catalog.";
            document.getElementById('button_text').value = 'Explore Services Catalog';
            document.getElementById('button_url').value = "{{ url('/') }}#services";
        } else if (presetType === 'service_update') {
            document.getElementById('cat_service').checked = true;
            document.getElementById('email_subject').value = 'Important Service Catalog & Processing Update';
            document.getElementById('email_headline').value = 'Updated Processing Timelines & Vetting Procedures';
            document.getElementById('email_body').value = "Dear Customer,\n\nWe have updated our service processing guidelines and document checklist requirements.\n\nPlease log into your dashboard to view updated stage requirements and status tracker logs for your ongoing applications.\n\nIf you have any questions, our support team is available 24/7.";
            document.getElementById('button_text').value = 'View My Requests';
            document.getElementById('button_url').value = "{{ url('/client/requests') }}";
        } else if (presetType === 'custom') {
            document.getElementById('cat_custom').checked = true;
            document.getElementById('email_subject').value = 'Direct Notice from Admin';
            document.getElementById('email_headline').value = 'Important Official Communication';
            document.getElementById('email_body').value = "Dear Member,\n\nWe are writing to provide you with an important update regarding your account or service request.\n\nPlease review this notice and contact support if you require any assistance.";
            document.getElementById('button_text').value = 'Login to Account';
            document.getElementById('button_url').value = "{{ url('/login') }}";
        }

        updatePreview();
    }

    document.addEventListener('DOMContentLoaded', function() {
        toggleRecipientOptions();
        updatePreview();
    });
</script>
@endsection
