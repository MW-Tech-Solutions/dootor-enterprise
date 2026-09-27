@extends('layouts.dashboard')

@section('title', 'Staff Work Queue & Action Center')

@section('content')
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <h1 class="h3 fw-bold text-dark mb-1">Staff Work Queue &amp; Action Center</h1>
        <p class="text-secondary small mb-0">Authoritative workspace to process assigned service applications, verify client documents, update stages, and log operational actions.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <span class="badge text-white px-3 py-2 fs-6 rounded-pill" style="background-color: #004225;">
            <i class="bi bi-person-workspace me-1"></i> {{ $myTasks->total() }} Active Tasks
        </span>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle p-3 text-white d-flex align-items-center justify-content-center" style="background-color: #004225; width: 48px; height: 48px;">
                    <i class="bi bi-folder-check fs-4"></i>
                </div>
                <div>
                    <h6 class="text-secondary small mb-0 fw-medium">Assigned Tasks Queue</h6>
                    <h4 class="fw-bold text-dark mb-0">{{ $myTasks->total() }}</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle p-3 text-dark d-flex align-items-center justify-content-center" style="background-color: #d4af37; width: 48px; height: 48px;">
                    <i class="bi bi-file-earmark-check fs-4"></i>
                </div>
                <div>
                    <h6 class="text-secondary small mb-0 fw-medium">Pending Document Reviews</h6>
                    <h4 class="fw-bold text-dark mb-0">{{ $pendingDocVerifications }}</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle p-3 bg-info text-dark d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <i class="bi bi-person-badge fs-4"></i>
                </div>
                <div>
                    <h6 class="text-secondary small mb-0 fw-medium">Logged-In Officer</h6>
                    <h6 class="fw-bold text-dark mb-0">{{ auth()->user()->name }}</h6>
                    <span class="badge bg-success-subtle text-success border border-success-subtle font-monospace" style="font-size: 10px; color: #004225 !important; background-color: #e6f4ea !important;">
                        File #: {{ auth()->user()->staff_file_number ?? 'DE/STF/2026/000001' }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
    <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
        <h5 class="card-title fw-bold text-dark mb-0 fs-6">Assigned Work Queue Registry</h5>
        <span class="badge bg-light text-dark font-monospace small">Total Tasks: {{ $myTasks->total() }}</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light text-secondary small">
                    <tr>
                        <th class="ps-4">Reference #</th>
                        <th>Applicant</th>
                        <th>Requested Service</th>
                        <th>Method</th>
                        <th>Current Stage</th>
                        <th>Status</th>
                        <th>Submitted Date</th>
                        <th class="text-end pe-4">Processing Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($myTasks as $req)
                        <tr>
                            <td class="ps-4">
                                <span class="font-monospace fw-bold text-dark d-block" style="font-size: 13px;">{{ $req->reference_number }}</span>
                            </td>
                            <td>
                                <span class="d-block fw-semibold text-dark small mb-0">{{ $req->client_name }}</span>
                                <span class="d-block text-secondary" style="font-size: 11px;">{{ $req->client_email }}</span>
                            </td>
                            <td class="fw-medium text-dark small">{{ $req->service_name }}</td>
                            <td>
                                <span class="badge {{ $req->application_method === 'manual' ? 'bg-warning text-dark' : 'bg-info text-dark' }}" style="font-size: 10px;">
                                    {{ ucfirst($req->application_method ?? 'online') }}
                                </span>
                            </td>
                            <td class="fw-semibold text-dark small">{{ $req->current_stage_name ?? $req->status }}</td>
                            <td>
                                @php
                                    $statusBadge = match($req->status) {
                                        'Completed' => 'bg-success text-white',
                                        'Processing', 'Documents Under Review', 'Assigned' => 'bg-info text-dark',
                                        'Action Required', 'Returned' => 'bg-warning text-dark',
                                        'Rejected', 'Cancelled' => 'bg-danger text-white',
                                        default => 'bg-secondary text-white'
                                    };
                                @endphp
                                <span class="badge {{ $statusBadge }} rounded-pill px-2.5 py-1 small fw-semibold">
                                    {{ $req->status }}
                                </span>
                            </td>
                            <td class="small text-secondary" style="font-size: 12px;">{{ $req->created_at->format('M d, Y') }}</td>
                            <td class="text-end pe-4">
                                <a href="{{ route('admin.work-queue.process', $req->id) }}" class="btn text-white btn-sm rounded-pill px-3 shadow-2xs fw-semibold" style="background-color: #004225;">
                                    <i class="bi bi-gear-fill me-1"></i> Process Application
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary opacity-50"></i>
                                No applications are currently waiting in your work queue.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($myTasks->hasPages())
        <div class="card-footer bg-white py-3 border-top">
            {{ $myTasks->links() }}
        </div>
    @endif
</div>
@endsection
