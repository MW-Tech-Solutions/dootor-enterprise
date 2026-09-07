@extends('layouts.dashboard')

@section('title', 'My Work Queue & Task Center')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 fw-bold text-dark mb-1">Staff Work Queue & Action Center</h1>
        <p class="text-secondary small mb-0">Applications and document verification tasks assigned to your account or role.</p>
    </div>
    <span class="badge bg-success px-3 py-2 fs-6" style="background-color: #004225 !important;">
        <i class="bi bi-clock-history me-1"></i> {{ $myTasks->total() }} Active Assigned Tasks
    </span>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle p-3 text-white d-flex align-items-center justify-content-center" style="background-color: #004225; width: 48px; height: 48px;">
                    <i class="bi bi-person-workspace fs-4"></i>
                </div>
                <div>
                    <h6 class="text-secondary small mb-0">Assigned Directly to You</h6>
                    <h4 class="fw-bold text-dark mb-0">{{ $myTasks->total() }}</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle p-3 text-dark d-flex align-items-center justify-content-center" style="background-color: #d4af37; width: 48px; height: 48px;">
                    <i class="bi bi-file-earmark-check fs-4"></i>
                </div>
                <div>
                    <h6 class="text-secondary small mb-0">Pending Document Verifications</h6>
                    <h4 class="fw-bold text-dark mb-0">{{ $pendingDocVerifications }}</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle p-3 bg-info text-dark d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <i class="bi bi-check2-circle fs-4"></i>
                </div>
                <div>
                    <h6 class="text-secondary small mb-0">Logged In Staff</h6>
                    <h6 class="fw-bold text-dark mb-0">{{ auth()->user()->name }}</h6>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-3">
    <div class="card-header bg-white py-3">
        <h5 class="card-title fw-bold text-dark mb-0 fs-6">My Assigned Applications</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Reference #</th>
                        <th>Applicant</th>
                        <th>Service</th>
                        <th>Method</th>
                        <th>Current Stage</th>
                        <th>Status</th>
                        <th>Submitted Date</th>
                        <th class="text-end pe-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($myTasks as $req)
                        <tr>
                            <td class="ps-4 fw-bold text-success">{{ $req->reference_number }}</td>
                            <td>
                                <span class="d-block fw-semibold text-dark">{{ $req->client_name }}</span>
                                <span class="d-block text-secondary small">{{ $req->client_email }}</span>
                            </td>
                            <td class="fw-medium text-dark">{{ $req->service_name }}</td>
                            <td>
                                <span class="badge {{ $req->application_method === 'manual' ? 'bg-warning text-dark' : 'bg-info text-dark' }}">
                                    {{ ucfirst($req->application_method ?? 'online') }}
                                </span>
                            </td>
                            <td class="fw-semibold text-dark">{{ $req->current_stage_name ?? $req->status }}</td>
                            <td>
                                <span class="badge bg-success" style="background-color: #004225 !important;">{{ $req->status }}</span>
                            </td>
                            <td class="small text-secondary">{{ $req->created_at->format('M d, Y') }}</td>
                            <td class="text-end pe-4">
                                <a href="{{ route('admin.subscriptions', ['search' => $req->reference_number]) }}" class="btn btn-outline-success btn-sm">
                                    <i class="bi bi-search me-1"></i> Review
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                                No applications currently assigned to your queue.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($myTasks->hasPages())
        <div class="card-footer bg-white py-3">
            {{ $myTasks->links() }}
        </div>
    @endif
</div>
@endsection
