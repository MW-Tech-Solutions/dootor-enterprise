@extends('layouts.dashboard')

@section('title', 'Process Application - ' . $serviceRequest->reference_number)

@section('content')
<div class="mb-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <a href="{{ route('admin.work-queue') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i> Back to Work Queue
            </a>
            <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle font-monospace px-3 py-1 rounded-pill" style="color: #004225 !important; background-color: #e6f4ea !important;">
                {{ $serviceRequest->reference_number }}
            </span>
        </div>
        <h4 class="fw-bold text-dark mb-0">Processing: {{ $serviceRequest->service_name }}</h4>
        @if($serviceRequest->subService)
            <span class="badge bg-light text-dark border mt-1">Sub-Service: {{ $serviceRequest->subService->name }}</span>
        @endif
    </div>
    <div class="d-flex align-items-center gap-2">
        <span class="badge bg-dark px-3 py-2 fs-6 rounded-pill">
            Status: {{ $serviceRequest->status }}
        </span>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show mb-4 rounded-3 shadow-sm" role="alert">
    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row g-4">
    <!-- Left Column: Applicant Info, Form Data & Uploaded Documents -->
    <div class="col-lg-8">
        
        <!-- 1. Applicant & Location Overview -->
        <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white overflow-hidden">
            <div class="card-header bg-light py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
                <h5 class="fw-bold text-dark mb-0 fs-6"><i class="bi bi-person-vcard text-success me-2" style="color: #004225 !important;"></i> Applicant &amp; Routing Details</h5>
                <span class="badge bg-white border text-dark small font-monospace">Client ID: #{{ $serviceRequest->client_id }}</span>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="text-secondary small fw-medium d-block">Full Name</label>
                        <span class="fw-bold text-dark fs-6">{{ $serviceRequest->client_name }}</span>
                    </div>
                    <div class="col-md-6">
                        <label class="text-secondary small fw-medium d-block">Email Address</label>
                        <span class="fw-semibold text-dark">{{ $serviceRequest->client_email }}</span>
                    </div>
                    <div class="col-md-6">
                        <label class="text-secondary small fw-medium d-block">Phone Number</label>
                        <span class="fw-semibold text-dark">{{ $serviceRequest->client_phone ?? $serviceRequest->client?->phone ?? 'N/A' }}</span>
                    </div>
                    <div class="col-md-6">
                        <label class="text-secondary small fw-medium d-block">Application Method</label>
                        <span class="badge {{ $serviceRequest->application_method === 'manual' ? 'bg-warning text-dark' : 'bg-info text-dark' }}">
                            {{ ucfirst($serviceRequest->application_method ?? 'online') }}
                        </span>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3 border">
                            <label class="text-muted small fw-bold d-block mb-1"><i class="bi bi-geo-alt-fill text-danger me-1"></i> Country Applying From</label>
                            <span class="fw-bold text-dark fs-6">{{ $serviceRequest->country_applying_from ?? $serviceRequest->client?->country_applying_from ?? 'Nigeria' }}</span>
                            <div class="small text-muted mt-1">State/Region: {{ $serviceRequest->client?->state ?? 'N/A' }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3 border">
                            <label class="text-muted small fw-bold d-block mb-1"><i class="bi bi-globe-americas text-primary me-1"></i> Country for Requested Service</label>
                            <span class="fw-bold text-dark fs-6">{{ $serviceRequest->country_service_requested ?? $serviceRequest->client?->country_service_requested ?? 'Nigeria' }}</span>
                            <div class="small text-muted mt-1">City: {{ $serviceRequest->client?->city ?? 'N/A' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Dynamic Form Responses / Application Fields -->
        @if($serviceRequest->form_data || $serviceRequest->manual_form_path)
            <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white overflow-hidden">
                <div class="card-header bg-light py-3 px-4 border-bottom">
                    <h5 class="fw-bold text-dark mb-0 fs-6"><i class="bi bi-file-earmark-text text-primary me-2"></i> Application Form Responses</h5>
                </div>
                <div class="card-body p-4">
                    @if($serviceRequest->manual_form_path)
                        <div class="p-3 bg-warning bg-opacity-10 border border-warning rounded-3 mb-3 d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="fw-bold text-dark mb-1"><i class="bi bi-file-earmark-pdf-fill text-danger me-1"></i> Scanned Application Form Attached</h6>
                                <p class="text-secondary small mb-0">Client submitted a handwritten/printed application scan.</p>
                            </div>
                            <a href="{{ asset($serviceRequest->manual_form_path) }}" target="_blank" class="btn btn-dark btn-sm rounded-pill px-3 fw-semibold">
                                <i class="bi bi-eye me-1"></i> View Scanned Form
                            </a>
                        </div>
                    @endif

                    @if(!empty($serviceRequest->form_data) && is_array($serviceRequest->form_data))
                        <div class="row g-3">
                            @foreach($serviceRequest->form_data as $fieldKey => $fieldValue)
                                <div class="col-md-6">
                                    <div class="p-2.5 bg-light rounded-3 border">
                                        <label class="text-muted small fw-semibold d-block mb-1">{{ ucwords(str_replace(['_', '-'], ' ', $fieldKey)) }}</label>
                                        <span class="fw-bold text-dark">
                                            @if(is_array($fieldValue))
                                                {{ implode(', ', $fieldValue) }}
                                            @else
                                                {{ $fieldValue ?: 'N/A' }}
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- 3. Uploaded Documents Verification Panel -->
        <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white overflow-hidden">
            <div class="card-header bg-light py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
                <h5 class="fw-bold text-dark mb-0 fs-6"><i class="bi bi-folder-check text-success me-2" style="color: #004225 !important;"></i> Uploaded Documents &amp; Verification</h5>
                <span class="badge bg-white border text-dark font-monospace">{{ $serviceRequest->requestDocuments->count() }} Documents Uploaded</span>
            </div>
            <div class="card-body p-4">
                @if($serviceRequest->passport_photo_path)
                    <div class="p-3 mb-4 rounded-3 border bg-light d-flex align-items-center gap-3">
                        <img src="{{ asset($serviceRequest->passport_photo_path) }}" alt="Passport Photo" class="rounded-3 border shadow-sm" style="width: 70px; height: 80px; object-fit: cover;">
                        <div>
                            <h6 class="fw-bold text-dark mb-1">Passport Photograph Uploaded</h6>
                            <p class="text-muted small mb-1">Official passport photo uploaded by client during application step.</p>
                            <a href="{{ asset($serviceRequest->passport_photo_path) }}" target="_blank" class="btn btn-sm btn-outline-dark rounded-pill px-3">
                                <i class="bi bi-zoom-in me-1"></i> Inspect Full Resolution
                            </a>
                        </div>
                    </div>
                @endif

                @if($serviceRequest->requestDocuments->count() > 0)
                    <div class="d-flex flex-column gap-3">
                        @foreach($serviceRequest->requestDocuments as $doc)
                            <div class="p-3 border rounded-3 bg-white shadow-2xs">
                                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-2">
                                    <div>
                                        <h6 class="fw-bold text-dark mb-0">
                                            <i class="bi bi-file-earmark-pdf text-danger me-1"></i> {{ $doc->document_name }}
                                        </h6>
                                        <small class="text-muted">Uploaded {{ $doc->created_at->format('M d, Y H:i') }}</small>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        @php
                                            $docBadge = match($doc->status) {
                                                'Accepted' => 'bg-success text-white',
                                                'Requires Correction' => 'bg-warning text-dark',
                                                'Rejected' => 'bg-danger text-white',
                                                default => 'bg-secondary text-white'
                                            };
                                        @endphp
                                        <span class="badge {{ $docBadge }} rounded-pill px-3 py-1 font-monospace" style="font-size: 11px;">
                                            {{ $doc->status }}
                                        </span>
                                        <a href="{{ asset($doc->file_path) }}" target="_blank" class="btn btn-outline-dark btn-sm rounded-pill px-3" title="View Document">
                                            <i class="bi bi-eye me-1"></i> View
                                        </a>
                                        <a href="{{ asset($doc->file_path) }}" download class="btn btn-outline-secondary btn-sm rounded-pill px-3" title="Download Document">
                                            <i class="bi bi-download me-1"></i> Download
                                        </a>
                                    </div>
                                </div>

                                <!-- Inline Document Verification Form -->
                                <form action="{{ route('admin.work-queue.document.verify', $doc->id) }}" method="POST" class="row g-2 pt-2 border-top mt-2">
                                    @csrf
                                    <div class="col-md-5">
                                        <select name="status" class="form-select form-select-sm rounded-3">
                                            <option value="Pending" {{ $doc->status === 'Pending' ? 'selected' : '' }}>Pending Verification</option>
                                            <option value="Received" {{ $doc->status === 'Received' ? 'selected' : '' }}>Received</option>
                                            <option value="Accepted" {{ $doc->status === 'Accepted' ? 'selected' : '' }}>Accepted ✅</option>
                                            <option value="Requires Correction" {{ $doc->status === 'Requires Correction' ? 'selected' : '' }}>Requires Correction ⚠️</option>
                                            <option value="Rejected" {{ $doc->status === 'Rejected' ? 'selected' : '' }}>Rejected ❌</option>
                                        </select>
                                    </div>
                                    <div class="col-md-5">
                                        <input type="text" name="admin_notes" class="form-control form-control-sm rounded-3" placeholder="Officer notes or correction reason..." value="{{ $doc->admin_notes }}">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" class="btn btn-dark btn-sm w-100 rounded-3 font-monospace">Update</button>
                                    </div>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-4 text-center text-muted bg-light rounded-3">
                        <i class="bi bi-folder-x fs-2 d-block mb-1"></i>
                        No specific document files uploaded for this application yet.
                    </div>
                @endif
            </div>
        </div>

        <!-- 4. Client Safe Progress Timeline -->
        <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
            <div class="card-header bg-light py-3 px-4 border-bottom">
                <h5 class="fw-bold text-dark mb-0 fs-6"><i class="bi bi-diagram-3 text-success me-2" style="color: #004225 !important;"></i> Application Stage History &amp; Audit Logs</h5>
            </div>
            <div class="card-body p-4">
                <div class="timeline position-relative ps-3">
                    @forelse($serviceRequest->stageHistories as $history)
                        <div class="timeline-item pb-3 border-start border-2 ps-3 position-relative">
                            <div class="position-absolute top-0 start-0 translate-middle rounded-circle bg-success" style="width: 10px; height: 10px; background-color: #004225 !important;"></div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-dark small">{{ $history->stage_name }}</span>
                                <span class="text-muted font-monospace" style="font-size: 11px;">{{ $history->created_at->format('M d, Y H:i A') }}</span>
                            </div>
                            <span class="badge bg-light text-dark border small mt-1">{{ $history->status }}</span>
                            @if($history->notes)
                                <p class="text-secondary small mb-0 mt-1 bg-light p-2 rounded-2">{{ $history->notes }}</p>
                            @endif
                        </div>
                    @empty
                        <div class="text-muted small">No stage transition history recorded yet.</div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>

    <!-- Right Column: Status Update, Stage Control & Assignment -->
    <div class="col-lg-4">
        
        <!-- Processing Action Card -->
        <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white overflow-hidden sticky-top" style="top: 90px;">
            <div class="card-header text-white py-3 px-4 border-bottom" style="background-color: #004225;">
                <h5 class="fw-bold mb-0 fs-6"><i class="bi bi-sliders me-2"></i> Update Application Status</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('admin.work-queue.update', $serviceRequest->id) }}" method="POST">
                    @csrf

                    <!-- Current Responsible Staff Display -->
                    <div class="mb-3 p-3 bg-light rounded-3 border">
                        <label class="text-muted small fw-bold d-block mb-1">Assigned Staff Member</label>
                        @if($serviceRequest->assignedStaff)
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-person-badge text-success fs-5" style="color: #004225 !important;"></i>
                                <div>
                                    <span class="fw-bold text-dark d-block small">{{ $serviceRequest->assignedStaff->name }}</span>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle font-monospace" style="font-size: 10px; color: #004225 !important; background-color: #e6f4ea !important;">
                                        File #: {{ $serviceRequest->assignedStaff->staff_file_number ?? 'DE/STF/2026/000001' }}
                                    </span>
                                </div>
                            </div>
                        @else
                            <span class="text-warning fw-bold small"><i class="bi bi-exclamation-circle me-1"></i> Unassigned</span>
                        @endif
                    </div>

                    <!-- Reassign Option -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Reassign Staff (Optional)</label>
                        <select name="assigned_staff_id" class="form-select form-select-sm rounded-3">
                            <option value="">-- Keep Current Assigned Staff --</option>
                            @foreach($staffMembers as $s)
                                <option value="{{ $s->id }}" {{ $serviceRequest->assigned_staff_id == $s->id ? 'selected' : '' }}>
                                    {{ $s->name }} ({{ $s->staff_file_number ?? 'DE/STF/2026/000001' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Main Status Select -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Processing Status</label>
                        <select name="status" class="form-select rounded-3 fw-bold">
                            <option value="Assigned" {{ $serviceRequest->status === 'Assigned' ? 'selected' : '' }}>Assigned</option>
                            <option value="Documents Under Review" {{ $serviceRequest->status === 'Documents Under Review' ? 'selected' : '' }}>Documents Under Review</option>
                            <option value="Verification in Progress" {{ $serviceRequest->status === 'Verification in Progress' ? 'selected' : '' }}>Verification in Progress</option>
                            <option value="Processing" {{ $serviceRequest->status === 'Processing' ? 'selected' : '' }}>Processing</option>
                            <option value="Awaiting External Agency" {{ $serviceRequest->status === 'Awaiting External Agency' ? 'selected' : '' }}>Awaiting External Agency</option>
                            <option value="Action Required" {{ $serviceRequest->status === 'Action Required' ? 'selected' : '' }}>Action Required (Client)</option>
                            <option value="Returned" {{ $serviceRequest->status === 'Returned' ? 'selected' : '' }}>Returned for Info</option>
                            <option value="Escalated" {{ $serviceRequest->status === 'Escalated' ? 'selected' : '' }}>Escalated</option>
                            <option value="Ready for Collection" {{ $serviceRequest->status === 'Ready for Collection' ? 'selected' : '' }}>Ready for Collection</option>
                            <option value="Completed" {{ $serviceRequest->status === 'Completed' ? 'selected' : '' }}>Completed ✅</option>
                            <option value="Rejected" {{ $serviceRequest->status === 'Rejected' ? 'selected' : '' }}>Rejected ❌</option>
                            <option value="Cancelled" {{ $serviceRequest->status === 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>

                    <!-- Workflow Stage Select -->
                    @if(isset($workflowStages) && $workflowStages->count() > 0)
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-dark">Workflow Stage</label>
                            <select name="stage_id" class="form-select form-select-sm rounded-3">
                                <option value="">-- Select Specific Stage --</option>
                                @foreach($workflowStages as $stg)
                                    <option value="{{ $stg->id }}" {{ $serviceRequest->current_stage_id == $stg->id ? 'selected' : '' }}>
                                        Step {{ $stg->sort_order }}: {{ $stg->stage_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <!-- Officer Remarks / Notes -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Officer Remarks &amp; Notes</label>
                        <textarea name="admin_notes" class="form-control rounded-3" rows="4" placeholder="Enter details of action taken, missing documents requested, or processing updates..."></textarea>
                    </div>

                    <!-- Send Client Email Notification Checkbox -->
                    <div class="form-check mb-4 bg-light p-2.5 rounded-3 border">
                        <input class="form-check-input ms-0 me-2" type="checkbox" name="is_user_visible" value="1" id="visCheck" checked>
                        <label class="form-check-label small fw-semibold text-dark" for="visCheck">
                            Send status update &amp; notes notification email to applicant
                        </label>
                    </div>

                    <button type="submit" class="btn text-white w-100 rounded-pill py-2.5 fw-bold shadow-sm" style="background-color: #004225;">
                        <i class="bi bi-save me-1"></i> Commit Status Update
                    </button>
                </form>
            </div>
        </div>

        <!-- Assignment History Card -->
        <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
            <div class="card-header bg-light py-3 px-4 border-bottom">
                <h5 class="fw-bold text-dark mb-0 fs-6"><i class="bi bi-clock-history me-2 text-secondary"></i> Assignment Audit Trail</h5>
            </div>
            <div class="card-body p-4">
                @if($serviceRequest->assignmentHistories->count() > 0)
                    <div class="d-flex flex-column gap-3">
                        @foreach($serviceRequest->assignmentHistories as $assign)
                            <div class="p-2.5 border-start border-3 border-success bg-light rounded-2" style="border-color: #004225 !important;">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-bold text-dark small">Assigned to: {{ $assign->newStaff?->name ?? 'Staff' }}</span>
                                    <span class="text-muted font-monospace" style="font-size: 10px;">{{ $assign->created_at->format('M d, H:i') }}</span>
                                </div>
                                <div class="small text-muted" style="font-size: 11px;">
                                    By: {{ $assign->assignedBy?->name ?? 'System' }} | Staff File #: {{ $assign->newStaff?->staff_file_number ?? 'N/A' }}
                                </div>
                                @if($assign->notes)
                                    <div class="small text-secondary mt-1 fst-italic">"{{ $assign->notes }}"</div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="small text-muted text-center py-3">No reassignment history recorded yet.</div>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection
