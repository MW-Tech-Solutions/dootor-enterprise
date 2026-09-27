@extends('layouts.dashboard')

@section('title', 'Service Applications Intake & Assignment')

@section('content')
<div class="mb-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
    <div>
        <h4 class="fw-bold text-dark mb-1">Service Applications Intake &amp; Staff Assignment</h4>
        <p class="text-secondary small mb-0">Central intake portal to monitor submitted applications, manage staff assignments, and track workflow queue status.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('admin.work-queue') }}" class="btn text-white btn-sm rounded-pill px-3 shadow-sm fw-semibold" style="background-color: #004225;">
            <i class="bi bi-person-workspace me-1"></i> Go to Staff Work Queue
        </a>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show mb-4 rounded-3 shadow-sm" role="alert">
    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<!-- Intake Overview Metrics -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <a href="{{ route('admin.subscriptions', ['filter' => 'all']) }}" class="card border-0 shadow-sm rounded-4 p-3 bg-white text-decoration-none h-100 hover-shadow transition">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-secondary small fw-medium d-block mb-1">Total Applications</span>
                    <h3 class="fw-bold text-dark mb-0 fs-4">{{ $stats['total'] }}</h3>
                </div>
                <div class="rounded-circle p-2 bg-light text-dark d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                    <i class="bi bi-files fs-5"></i>
                </div>
            </div>
        </a>
    </div>
    <div class="col-6 col-md-3">
        <a href="{{ route('admin.subscriptions', ['filter' => 'unassigned']) }}" class="card border-0 shadow-sm rounded-4 p-3 bg-white text-decoration-none h-100 hover-shadow transition">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-warning small fw-medium d-block mb-1">Awaiting Assignment</span>
                    <h3 class="fw-bold text-dark mb-0 fs-4">{{ $stats['unassigned'] }}</h3>
                </div>
                <div class="rounded-circle p-2 bg-warning bg-opacity-10 text-warning d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                    <i class="bi bi-person-plus fs-5"></i>
                </div>
            </div>
        </a>
    </div>
    <div class="col-6 col-md-3">
        <a href="{{ route('admin.subscriptions', ['filter' => 'assigned']) }}" class="card border-0 shadow-sm rounded-4 p-3 bg-white text-decoration-none h-100 hover-shadow transition">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-success small fw-medium d-block mb-1" style="color: #004225 !important;">Assigned to Staff</span>
                    <h3 class="fw-bold text-dark mb-0 fs-4">{{ $stats['assigned'] }}</h3>
                </div>
                <div class="rounded-circle p-2 bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                    <i class="bi bi-person-check fs-5"></i>
                </div>
            </div>
        </a>
    </div>
    <div class="col-6 col-md-3">
        <a href="{{ route('admin.subscriptions', ['filter' => 'drafts']) }}" class="card border-0 shadow-sm rounded-4 p-3 bg-white text-decoration-none h-100 hover-shadow transition">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-secondary small fw-medium d-block mb-1">Active Drafts</span>
                    <h3 class="fw-bold text-dark mb-0 fs-4">{{ $stats['drafts'] }}</h3>
                </div>
                <div class="rounded-circle p-2 bg-info bg-opacity-10 text-info d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                    <i class="bi bi-pencil-square fs-5"></i>
                </div>
            </div>
        </a>
    </div>
</div>

<!-- Search & Filter Controls -->
<div class="card border-0 shadow-sm p-3 rounded-4 bg-white mb-4">
    <form action="{{ route('admin.subscriptions') }}" method="GET" class="row g-3 align-items-center">
        <div class="col-md-5">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 border-light"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="search" class="form-control border-start-0 border-light ps-0" placeholder="Search reference # (DE/APP/...), client name, email, or service..." value="{{ $search }}">
            </div>
        </div>
        <div class="col-md-3">
            <select name="filter" class="form-select border-light small" onchange="this.form.submit()">
                <option value="all" {{ ($filter ?? 'all') == 'all' ? 'selected' : '' }}>All Applications Intake</option>
                <option value="unassigned" {{ ($filter ?? '') == 'unassigned' ? 'selected' : '' }}>Unassigned Only</option>
                <option value="assigned" {{ ($filter ?? '') == 'assigned' ? 'selected' : '' }}>Assigned Only</option>
                <option value="submitted" {{ ($filter ?? '') == 'submitted' ? 'selected' : '' }}>Submitted Applications</option>
                <option value="drafts" {{ ($filter ?? '') == 'drafts' ? 'selected' : '' }}>Draft Applications</option>
            </select>
        </div>
        <div class="col-md-2">
            <select name="per_page" class="form-select border-light small" onchange="this.form.submit()">
                <option value="5" {{ ($perPage ?? 10) == 5 ? 'selected' : '' }}>5 / Page</option>
                <option value="10" {{ ($perPage ?? 10) == 10 ? 'selected' : '' }}>10 / Page</option>
                <option value="20" {{ ($perPage ?? 10) == 20 ? 'selected' : '' }}>20 / Page</option>
                <option value="50" {{ ($perPage ?? 10) == 50 ? 'selected' : '' }}>50 / Page</option>
            </select>
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn text-white rounded-pill px-4 flex-grow-1 fw-semibold" style="background-color: #004225;">Filter</button>
            @if($search || $filter !== 'all')
                <a href="{{ route('admin.subscriptions') }}" class="btn btn-outline-secondary rounded-pill px-3" title="Clear Filters"><i class="bi bi-x-lg"></i></a>
            @endif
        </div>
    </form>
</div>

<!-- Intake Applications Table -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
    <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
        <h5 class="fw-bold text-dark mb-0 fs-6">Applications Registry</h5>
        <span class="badge bg-light text-dark font-monospace small">Showing {{ $serviceRequests->count() }} of {{ $serviceRequests->total() }}</span>
    </div>
    <div class="card-body p-0">
        @if($serviceRequests->count() > 0)
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light text-secondary small">
                        <tr>
                            <th class="ps-4">Reference #</th>
                            <th>Applicant</th>
                            <th>Service Requested</th>
                            <th>Location Routing</th>
                            <th>Submitted Date</th>
                            <th>Status</th>
                            <th>Assigned Staff</th>
                            <th class="text-end pe-4">Assignment Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($serviceRequests as $req)
                            @php
                                $client = $req->client;
                                $assignedStaff = $req->assignedStaff;
                                $subService = $req->subService;
                            @endphp
                            <tr>
                                <td class="ps-4">
                                    <span class="font-monospace fw-bold text-dark d-block" style="font-size: 13px;">{{ $req->reference_number ?? ('DE/APP/' . date('Y') . '/' . sprintf('%06d', $req->id)) }}</span>
                                    <span class="badge bg-secondary-subtle text-secondary small" style="font-size: 10px;">{{ ucfirst($req->application_method ?? 'online') }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle text-white d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width: 32px; height: 32px; font-size: 12px; background-color: #004225;">
                                            {{ strtoupper(substr($client->first_name ?? $req->client_name ?? 'C', 0, 1)) }}
                                        </div>
                                        <div>
                                            <span class="fw-semibold text-dark d-block small mb-0">{{ $req->client_name }}</span>
                                            <span class="text-muted d-block" style="font-size: 11px;">{{ $req->client_email }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-semibold text-dark d-block small mb-0">{{ $req->service_name }}</span>
                                    @if($subService)
                                        <span class="badge bg-light text-secondary border border-secondary-subtle small font-monospace" style="font-size: 10px;">
                                            <i class="bi bi-diagram-2 me-1"></i>{{ $subService->name }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="small text-muted" style="font-size: 11px;">
                                        <div><strong>From:</strong> {{ $req->country_applying_from ?? $client?->country_applying_from ?? 'Nigeria' }}</div>
                                        <div><strong>For:</strong> {{ $req->country_service_requested ?? $client?->country_service_requested ?? 'Nigeria' }}</div>
                                    </div>
                                </td>
                                <td class="small text-secondary" style="font-size: 12px;">
                                    {{ $req->created_at->format('M d, Y') }}
                                    <div class="text-muted" style="font-size: 10px;">{{ $req->created_at->format('H:i A') }}</div>
                                </td>
                                <td>
                                    @php
                                        $badgeClass = match($req->status) {
                                            'Completed' => 'bg-success text-white',
                                            'Assigned', 'Processing', 'Documents Under Review' => 'bg-info text-dark',
                                            'Draft', 'In Progress' => 'bg-secondary text-white',
                                            'Awaiting Assignment', 'Application Submitted' => 'bg-warning text-dark',
                                            'Rejected', 'Cancelled' => 'bg-danger text-white',
                                            default => 'bg-light text-dark'
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }} rounded-pill px-2.5 py-1 small fw-semibold">
                                        {{ $req->status }}
                                    </span>
                                </td>
                                <td>
                                    @if($assignedStaff)
                                        <div class="d-flex align-items-center gap-1.5">
                                            <i class="bi bi-person-badge text-success fs-6" style="color: #004225 !important;"></i>
                                            <div>
                                                <span class="fw-bold text-dark d-block small mb-0" style="font-size: 12px;">{{ $assignedStaff->name }}</span>
                                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-1 font-monospace" style="font-size: 10px; color: #004225 !important; background-color: #e6f4ea !important;">
                                                    {{ $assignedStaff->staff_file_number ?? 'DE/STF/2026/000001' }}
                                                </span>
                                            </div>
                                        </div>
                                    @else
                                        <span class="badge bg-warning bg-opacity-10 text-dark border border-warning rounded-pill px-2.5 py-1 small">
                                            <i class="bi bi-exclamation-triangle me-1"></i> Unassigned
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-flex justify-content-end gap-1">
                                        <!-- Assign Staff Trigger -->
                                        <button class="btn btn-outline-dark btn-sm rounded-pill px-3 shadow-2xs fw-medium" style="font-size: 12px;" type="button" data-bs-toggle="modal" data-bs-target="#assignModal-{{ $req->id }}">
                                            <i class="bi bi-person-plus me-1"></i> {{ $assignedStaff ? 'Reassign' : 'Assign Staff' }}
                                        </button>
                                        
                                        <!-- Open Work Queue Processing -->
                                        <a href="{{ route('admin.work-queue.process', $req->id) }}" class="btn text-white btn-sm rounded-pill px-3 fw-medium shadow-2xs" style="font-size: 12px; background-color: #004225;" title="Open in Work Queue">
                                            <i class="bi bi-arrow-right-circle me-1"></i> Open Queue
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-5 text-center text-muted">
                <i class="bi bi-inbox display-4 text-secondary opacity-50 mb-3 d-block"></i>
                <h6 class="fw-bold text-dark mb-1">No Applications Found</h6>
                <p class="small text-secondary mb-0">No client service applications match your intake filter.</p>
            </div>
        @endif
    </div>

    @if($serviceRequests->hasPages())
        <div class="card-footer bg-white py-3 border-top">
            <div class="d-flex flex-column flex-sm-row align-items-center justify-content-between gap-3">
                <div class="small text-secondary">
                    Showing <span class="fw-bold text-dark">{{ $serviceRequests->firstItem() }}</span> to <span class="fw-bold text-dark">{{ $serviceRequests->lastItem() }}</span> of <span class="fw-bold text-dark">{{ $serviceRequests->total() }}</span> applications
                </div>
                {{ $serviceRequests->links() }}
            </div>
        </div>
    @endif
</div>

<!-- Assign Staff Modals -->
@foreach($serviceRequests as $req)
    <div class="modal fade" id="assignModal-{{ $req->id }}" tabindex="-1" aria-labelledby="assignModalLabel-{{ $req->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-bottom py-3 px-4" style="background-color: #004225;">
                    <h5 class="modal-title fw-bold text-white fs-6" id="assignModalLabel-{{ $req->id }}">
                        <i class="bi bi-person-badge-fill me-2"></i> Assign Application #{{ $req->reference_number }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.subscription.assign', $req->id) }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="bg-light p-3 rounded-3 mb-3 border">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted small">Applicant:</span>
                                <span class="fw-bold text-dark small">{{ $req->client_name }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted small">Service:</span>
                                <span class="fw-semibold text-dark small">{{ $req->service_name }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted small">Current Assigned Staff:</span>
                                <span class="fw-bold text-success small" style="color: #004225 !important;">
                                    {{ $req->assignedStaff ? ($req->assignedStaff->name . ' (' . $req->assignedStaff->staff_file_number . ')') : 'Unassigned' }}
                                </span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="staff_select-{{ $req->id }}" class="form-label small fw-bold text-dark">Select Responsible Staff Member</label>
                            <select name="assigned_staff_id" id="staff_select-{{ $req->id }}" class="form-select rounded-3 fw-medium" required>
                                <option value="">-- Choose Processing Staff --</option>
                                @foreach($staffMembers as $staff)
                                    <option value="{{ $staff->id }}" {{ $req->assigned_staff_id == $staff->id ? 'selected' : '' }}>
                                        {{ $staff->name }} (Staff File: {{ $staff->staff_file_number ?? 'DE/STF/2026/000001' }}) - {{ ucfirst($staff->role) }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted d-block mt-1">Assigning an application moves it directly into the staff member's Staff Work Queue.</small>
                        </div>

                        <div class="mb-3">
                            <label for="assignment_notes-{{ $req->id }}" class="form-label small fw-bold text-dark">Assignment Instructions / Notes (Optional)</label>
                            <textarea name="assignment_notes" id="assignment_notes-{{ $req->id }}" class="form-control rounded-3" rows="3" placeholder="e.g. Please verify physical passport booklet photos before submitting to agency."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-top bg-light px-4 py-3">
                        <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn text-white rounded-pill px-4 fw-semibold" style="background-color: #004225;">
                            <i class="bi bi-check-lg me-1"></i> Save Assignment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach

@endsection
