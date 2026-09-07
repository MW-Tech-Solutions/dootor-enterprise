@extends('layouts.dashboard')

@section('title', 'Vendor Dashboard - ' . ($settings->platform_name ?? 'Dooter Enterprises'))

@section('styles')
<style>
    .stat-card-gradient {
        background: linear-gradient(135deg, #004225 0%, #006637 100%);
        color: #ffffff;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }
    .stat-card-gradient::after {
        content: '';
        position: absolute;
        top: -30%;
        right: -20%;
        width: 160px;
        height: 160px;
        background: rgba(212, 175, 55, 0.15);
        border-radius: 50%;
        pointer-events: none;
    }
    .stat-card-gradient:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0, 66, 37, 0.25) !important;
    }
    .stat-card-white {
        background: #ffffff;
        border: 1px solid rgba(226, 232, 240, 0.8);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .stat-card-white:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(15, 23, 42, 0.08) !important;
    }
    .icon-box-emerald {
        background-color: rgba(16, 185, 129, 0.1);
        color: #10b981;
    }
    .icon-box-amber {
        background-color: rgba(245, 158, 11, 0.1);
        color: #f59e0b;
    }
    .icon-box-blue {
        background-color: rgba(59, 130, 246, 0.1);
        color: #3b82f6;
    }
</style>
@endsection

@section('content')
<div class="mb-4">
    <h1 class="h3 fw-bold text-dark mb-1">Vendor Processing Portal</h1>
    <p class="text-secondary small">Manage assigned service requests, upload completed certificates, and monitor earnings</p>
</div>

<!-- Premium Stats Row -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-4 rounded-4 stat-card-gradient">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="small fw-semibold text-white-50 text-uppercase tracking-wider">Total Assigned Requests</span>
                <div class="rounded-circle p-2 bg-white bg-opacity-20 text-white d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                    <i class="bi bi-inbox-fill fs-5"></i>
                </div>
            </div>
            <div class="d-flex align-items-baseline gap-2">
                <span class="display-6 fw-bold text-white">{{ number_format($total_requests) }}</span>
                <span class="badge bg-warning text-dark rounded-pill px-2 py-0.5 small" style="font-size: 11px;">Queue</span>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-4 rounded-4 stat-card-white">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="small fw-semibold text-secondary text-uppercase tracking-wider">Pending Clearance</span>
                <div class="rounded-circle p-2 icon-box-amber d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                    <i class="bi bi-clock-history fs-5"></i>
                </div>
            </div>
            <div class="d-flex align-items-baseline gap-2">
                <span class="display-6 fw-bold text-dark">{{ number_format($pending_requests) }}</span>
                <span class="text-warning small fw-semibold">In Progress</span>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-4 rounded-4 stat-card-white">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="small fw-semibold text-secondary text-uppercase tracking-wider">Completed Orders</span>
                <div class="rounded-circle p-2 icon-box-emerald d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                    <i class="bi bi-check-circle-fill fs-5"></i>
                </div>
            </div>
            <div class="d-flex align-items-baseline gap-2">
                <span class="display-6 fw-bold text-dark">{{ number_format($completed_requests) }}</span>
                <span class="text-success small fw-semibold"><i class="bi bi-shield-check me-1"></i> Delivered</span>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-4 rounded-4 stat-card-white">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="small fw-semibold text-secondary text-uppercase tracking-wider">Offered Services</span>
                <div class="rounded-circle p-2 icon-box-blue d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                    <i class="bi bi-grid-fill fs-5"></i>
                </div>
            </div>
            <div class="d-flex align-items-baseline gap-2">
                <span class="display-6 fw-bold text-dark">{{ number_format($active_services_count) }}</span>
                <span class="text-primary small fw-semibold">Catalog</span>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h5 fw-bold text-dark mb-0">Assigned Client Applications</h2>
        <a href="{{ route('vendor.requests') }}" class="btn text-white btn-sm rounded-pill px-4 fw-semibold" style="background-color: #004225;">View All Requests</a>
    </div>

    @if(count($recent_requests) > 0)
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr class="text-secondary small">
                        <th>Application Ref</th>
                        <th>Client</th>
                        <th>Service Required</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recent_requests as $request)
                        <tr>
                            <td>
                                <span class="font-monospace fw-bold text-primary small">{{ $request->reference_number ?? ('DE-' . $request->id) }}</span>
                            </td>
                            <td>
                                <span class="fw-semibold text-dark d-block">{{ $request->client_name }}</span>
                                <span class="text-muted small">{{ $request->client_email }}</span>
                            </td>
                            <td class="small fw-bold text-dark">{{ $request->service_name }}</td>
                            <td>
                                @php
                                    $statusClass = match($request->status) {
                                        'Completed' => 'bg-success text-white',
                                        'Processing' => 'bg-info text-dark',
                                        'Cancelled' => 'bg-danger text-white',
                                        default => 'bg-warning text-dark'
                                    };
                                @endphp
                                <span class="badge {{ $statusClass }} rounded-pill px-3 py-1">{{ $request->status }}</span>
                            </td>
                            <td>
                                <a href="{{ route('vendor.requests') }}" class="btn btn-outline-dark btn-sm rounded-pill px-3">Update Status</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="text-center py-5">
            <i class="bi bi-inbox fs-1 text-muted"></i>
            <p class="text-secondary mt-2 small">No processing requests assigned to your queue yet.</p>
        </div>
    @endif
</div>
@endsection
