@extends('layouts.dashboard')

@section('title', 'Service Applications')

@section('content')
<div class="mb-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
    <div>
        <h4 class="fw-bold text-dark mb-1">Service Applications &amp; Upload Review</h4>
        <p class="text-secondary small mb-0">Review online &amp; manual application uploads, inspect scanned forms, verify documents, and update user service status.</p>
    </div>
    @if(isset($serviceRequests) && $serviceRequests->total() > 0)
    <div class="badge bg-white border text-dark p-2 px-3 rounded-pill shadow-sm small">
        <i class="bi bi-file-earmark-text text-success me-1"></i> Showing Application {{ $serviceRequests->firstItem() }} of {{ $serviceRequests->total() }} Total
    </div>
    @endif
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<!-- Search & View Controls Panel -->
<div class="card border-0 shadow-sm p-3 rounded-4 bg-white mb-4">
    <form action="{{ route('admin.subscriptions') }}" method="GET" class="row g-3 align-items-center">
        <div class="col-md-7">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 border-light"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="search" class="form-control border-start-0 border-light ps-0" placeholder="Search by client name, email, phone, application ref (DOOTOR-2026-XXXX), or service name..." value="{{ $search }}">
            </div>
        </div>
        <div class="col-md-3">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 border-light text-muted small"><i class="bi bi-layers me-1"></i> View</span>
                <select name="per_page" class="form-select border-start-0 border-light small" onchange="this.form.submit()">
                    <option value="1" {{ ($perPage ?? 1) == 1 ? 'selected' : '' }}>1 Application / Page</option>
                    <option value="2" {{ ($perPage ?? 1) == 2 ? 'selected' : '' }}>2 Applications / Page</option>
                    <option value="5" {{ ($perPage ?? 1) == 5 ? 'selected' : '' }}>5 Applications / Page</option>
                    <option value="10" {{ ($perPage ?? 1) == 10 ? 'selected' : '' }}>10 Applications / Page</option>
                    <option value="20" {{ ($perPage ?? 1) == 20 ? 'selected' : '' }}>20 Applications / Page</option>
                </select>
            </div>
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn text-white rounded-pill px-4 flex-grow-1 fw-semibold" style="background-color: #004225;">Search</button>
            @if($search)
                <a href="{{ route('admin.subscriptions') }}" class="btn btn-outline-secondary rounded-pill px-3" title="Clear Filter"><i class="bi bi-x-lg"></i></a>
            @endif
        </div>
    </form>
</div>

<!-- Individual Service Applications Listing -->
@if(isset($serviceRequests) && $serviceRequests->count() > 0)
    <div class="d-flex flex-column gap-4">
        @foreach($serviceRequests as $request)
            @php
                $client = $request->client;
                $currencyCode = $settings->default_currency ?? config('services.payment.currency', 'NGN');
            @endphp
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                <!-- Applicant Header Bar -->
                <div class="card-header bg-light border-0 py-3 px-4 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
                    <div class="d-flex align-items-center gap-3">
                        @if($client && $client->avatar_url)
                            <img src="{{ app_file_url($client->avatar_url) }}" alt="avatar" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                        @else
                            <div class="rounded-circle text-white d-flex align-items-center justify-content-center fw-bold" style="width: 40px; height: 40px; font-size: 14px; background-color: #004225;">
                                {{ strtoupper(substr($client->first_name ?? $request->service_name, 0, 1)) }}
                            </div>
                        @endif
                        <div>
                            <h3 class="h6 fw-bold text-dark mb-0">{{ $client->name ?? 'Direct Client' }}</h3>
                            <span class="text-secondary small">{{ $client->email ?? 'No email' }} | {{ $client->phone ?? 'No Phone' }}</span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1.5 fw-semibold small" style="color: #004225 !important; background-color: #e6f4ea !important;">
                            Application #{{ $request->id }}
                        </span>
                    </div>
                </div>
                
                <!-- Individual Service Content Card Body -->
                <div class="card-body p-4">
                    <div class="border rounded-3 p-3 bg-white">
                        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 pb-2 border-bottom gap-2">
                            <div>
                                <span class="font-monospace fw-bold text-success fs-6 me-2" style="color: #004225 !important;">{{ $request->reference_number ?? ('DOOTOR-' . $request->id) }}</span>
                                <span class="fw-bold text-dark fs-6">{{ $request->service_name }}</span>
                                <small class="text-muted ms-2">• Submitted {{ $request->created_at->format('M d, Y H:i') }}</small>
                            </div>
                            <div class="d-flex gap-2 align-items-center flex-wrap">
                                @if($request->application_method === 'manual' || $request->manual_form_path)
                                    <span class="badge bg-warning bg-opacity-10 text-dark border border-warning px-2.5 py-1">
                                        <i class="bi bi-file-earmark-arrow-up me-1"></i> Manual Form Upload
                                    </span>
                                @else
                                    <span class="badge bg-info bg-opacity-10 text-info border border-info px-2.5 py-1">
                                        <i class="bi bi-laptop me-1"></i> Online Application
                                    </span>
                                @endif

                                <span class="badge bg-{{ $request->payment_status === 'Paid' ? 'success' : 'secondary' }} bg-opacity-10 text-{{ $request->payment_status === 'Paid' ? 'success' : 'dark' }} px-2.5 py-1">
                                    {{ $request->payment_status }} ({{ $currencyCode }} {{ number_format($request->amount_paid, 2) }})
                                </span>
                                <span class="badge bg-dark text-white px-2.5 py-1">
                                    {{ $request->status }}
                                </span>
                            </div>
                        </div>

                        <!-- Manual Upload Highlights & File Inspection -->
                        @if($request->manual_form_path || $request->passport_photo_path)
                            <div class="p-3 mb-3 rounded-3 border bg-light border-warning border-opacity-50">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                    <div>
                                        <h6 class="fw-bold text-dark mb-1" style="font-size: 13px;">
                                            <i class="bi bi-file-earmark-pdf-fill text-danger me-1"></i> Client Uploaded Completed Manual Application Form
                                        </h6>
                                        <p class="text-muted small mb-0">Inspect the scanned form to verify handwritten details, signatures, and document cleanliness.</p>
                                    </div>
                                    <div class="d-flex gap-2">
                                        @if($request->manual_form_path)
                                            <a href="{{ asset($request->manual_form_path) }}" target="_blank" class="btn btn-sm btn-outline-dark fw-semibold">
                                                <i class="bi bi-eye me-1"></i> View Scanned Form
                                            </a>
                                        @endif
                                        @if($request->passport_photo_path)
                                            <a href="{{ asset($request->passport_photo_path) }}" target="_blank" class="btn btn-sm btn-outline-secondary fw-semibold">
                                                <i class="bi bi-person-bounding-box me-1"></i> View Passport Photo
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Application Update Form (Status, Stage & Assigned Staff) -->
                        <form action="{{ route('admin.subscription.update', $request->id) }}" method="POST" class="row g-3 mb-3">
                            @csrf
                            @method('PATCH')
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold text-muted mb-1">Update Service Status</label>
                                <select name="status" class="form-select form-select-sm">
                                    <option value="Application Submitted" {{ $request->status === 'Application Submitted' ? 'selected' : '' }}>1. Application Submitted</option>
                                    <option value="Payment Confirmed" {{ $request->status === 'Payment Confirmed' ? 'selected' : '' }}>2. Payment Confirmed</option>
                                    <option value="Documents Under Review" {{ $request->status === 'Documents Under Review' ? 'selected' : '' }}>3. Documents Under Review</option>
                                    <option value="Processing" {{ $request->status === 'Processing' ? 'selected' : '' }}>4. Processing</option>
                                    <option value="Awaiting External Agency" {{ $request->status === 'Awaiting External Agency' ? 'selected' : '' }}>5. Awaiting External Agency</option>
                                    <option value="Action Required" {{ $request->status === 'Action Required' ? 'selected' : '' }}>6. Action Required (Client)</option>
                                    <option value="Ready for Collection" {{ $request->status === 'Ready for Collection' ? 'selected' : '' }}>7. Ready for Collection</option>
                                    <option value="Completed" {{ $request->status === 'Completed' ? 'selected' : '' }}>8. Completed ✅</option>
                                    <option value="Rejected" {{ $request->status === 'Rejected' ? 'selected' : '' }}>9. Rejected ❌</option>
                                    <option value="Cancelled" {{ $request->status === 'Cancelled' ? 'selected' : '' }}>10. Cancelled</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold text-muted mb-1">Assigned Staff</label>
                                <select name="assigned_staff_id" class="form-select form-select-sm">
                                    <option value="">-- Unassigned --</option>
                                    @foreach($staffMembers as $staff)
                                    <option value="{{ $staff->id }}" {{ $request->assigned_staff_id === $staff->id ? 'selected' : '' }}>
                                        {{ $staff->name }} ({{ ucfirst($staff->role) }})
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold text-muted mb-1">Review Notes for Client (Optional)</label>
                                <input type="text" name="admin_notes" class="form-control form-control-sm" placeholder="e.g. Scanned form approved. Processing started.">
                            </div>
                            <div class="col-12 d-flex justify-content-end gap-2">
                                <div class="form-check me-auto align-self-center">
                                    <input class="form-check-input" type="checkbox" name="is_user_visible" value="1" id="visCheck{{ $request->id }}" checked>
                                    <label class="form-check-label small text-muted" for="visCheck{{ $request->id }}">
                                        Send update &amp; note notification to client
                                    </label>
                                </div>
                                <button type="submit" class="btn text-white btn-sm px-4 fw-semibold" style="background-color: #004225;">
                                    <i class="bi bi-save me-1"></i> Save Status &amp; Notify Client
                                </button>
                            </div>
                        </form>

                        <!-- Uploaded Documents Verification Section -->
                        <div class="bg-light p-3 rounded-3 mt-2">
                            <h6 class="fw-bold mb-2 small text-uppercase text-secondary">Document Review &amp; Verification</h6>
                            @if($request->requestDocuments && $request->requestDocuments->count() > 0)
                                <div class="row g-2">
                                    @foreach($request->requestDocuments as $doc)
                                    <div class="col-md-6">
                                        <div class="p-2 border rounded bg-white d-flex justify-content-between align-items-center">
                                            <div class="overflow-hidden me-2">
                                                <a href="{{ asset($doc->file_path) }}" target="_blank" class="fw-bold text-decoration-none text-dark small text-truncate d-block">
                                                    <i class="bi bi-file-earmark-text me-1 text-success"></i> {{ $doc->document_name }}
                                                </a>
                                                @if($doc->admin_notes)
                                                <div class="small text-danger mt-1">Note: {{ $doc->admin_notes }}</div>
                                                @endif
                                            </div>
                                            <form action="{{ route('admin.document.status', $doc->id) }}" method="POST" class="d-flex gap-1 flex-shrink-0">
                                                @csrf
                                                @method('PATCH')
                                                <select name="status" class="form-select form-select-sm" style="font-size: 11px;" onchange="this.form.submit()">
                                                    <option value="Pending" {{ $doc->status === 'Pending' ? 'selected' : '' }}>Pending</option>
                                                    <option value="Received" {{ $doc->status === 'Received' ? 'selected' : '' }}>Received</option>
                                                    <option value="Accepted" {{ $doc->status === 'Accepted' ? 'selected' : '' }}>Accepted ✅</option>
                                                    <option value="Requires Correction" {{ $doc->status === 'Requires Correction' ? 'selected' : '' }}>Requires Correction ⚠️</option>
                                                    <option value="Rejected" {{ $doc->status === 'Rejected' ? 'selected' : '' }}>Rejected ❌</option>
                                                </select>
                                            </form>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            @else
                                <span class="small text-muted">No individual document records submitted for verification yet.</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    
    <!-- Individual Service Application Pagination Controls -->
    @if($serviceRequests->hasPages())
        <div class="card border-0 shadow-sm p-3 rounded-4 bg-white mt-4">
            <div class="d-flex flex-column flex-sm-row align-items-center justify-content-between gap-3">
                <div class="small text-secondary fw-medium">
                    Showing <span class="fw-bold text-dark">{{ $serviceRequests->firstItem() }}</span> to <span class="fw-bold text-dark">{{ $serviceRequests->lastItem() }}</span> of <span class="fw-bold text-dark">{{ $serviceRequests->total() }}</span> applications
                </div>

                <div class="d-flex align-items-center gap-2">
                    @if ($serviceRequests->onFirstPage())
                        <button class="btn btn-outline-secondary btn-sm rounded-pill px-3 opacity-50" disabled>
                            <i class="bi bi-chevron-left me-1"></i> Previous
                        </button>
                    @else
                        <a href="{{ $serviceRequests->previousPageUrl() }}" class="btn btn-outline-dark btn-sm rounded-pill px-3 fw-semibold">
                            <i class="bi bi-chevron-left me-1"></i> Previous
                        </a>
                    @endif

                    <div class="px-2 small font-monospace fw-bold text-dark">
                        Page {{ $serviceRequests->currentPage() }} / {{ $serviceRequests->lastPage() }}
                    </div>

                    @if ($serviceRequests->hasMorePages())
                        <a href="{{ $serviceRequests->nextPageUrl() }}" class="btn text-white btn-sm rounded-pill px-3 fw-semibold" style="background-color: #004225;">
                            Next <i class="bi bi-chevron-right ms-1"></i>
                        </a>
                    @else
                        <button class="btn btn-outline-secondary btn-sm rounded-pill px-3 opacity-50" disabled>
                            Next <i class="bi bi-chevron-right ms-1"></i>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif
@else
    <div class="card border-0 shadow-sm p-5 rounded-4 text-center bg-white">
        <i class="bi bi-inbox display-4 text-muted mb-3"></i>
        <h3 class="h5 fw-bold text-dark mb-1">No Applications Found</h3>
        <p class="text-secondary small mb-0">No client service applications or manual form uploads match your search criteria.</p>
    </div>
@endif
@endsection
