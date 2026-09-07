@extends('layouts.dashboard')

@section('title', 'Client Dashboard - ' . ($settings->platform_name ?? 'Dooter Enterprises'))

@section('styles')
<style>
    .stat-card-gradient {
        background: linear-gradient(135deg, #004225 0%, #006637 100%);
        color: #ffffff;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
    }
    .stat-card-gradient::after {
        content: '';
        position: absolute;
        top: -30%;
        right: -20%;
        width: 160px;
        height: 160px;
        background: rgba(255, 255, 255, 0.08);
        border-radius: 50%;
        pointer-events: none;
    }
    .stat-card-gradient:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0, 66, 37, 0.3) !important;
    }
    .stat-card-white {
        background: #ffffff;
        border: 1px solid rgba(226, 232, 240, 0.9);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .stat-card-white:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(15, 23, 42, 0.08) !important;
    }
    .icon-box-emerald {
        background-color: rgba(16, 185, 129, 0.12);
        color: #059669;
    }
    .icon-box-amber {
        background-color: rgba(245, 158, 11, 0.12);
        color: #d97706;
    }
</style>
@endsection

@section('content')
<div class="mb-4">
    <h1 class="h3 fw-bold text-dark mb-1">Welcome back, {{ Auth::user()->first_name }}!</h1>
    <p class="text-secondary small">Track your active document processing applications and explore available services</p>
</div>

<!-- High Contrast Premium Stats Row -->
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-4 rounded-4 stat-card-gradient">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="small fw-bold text-white text-uppercase tracking-wider" style="font-size: 11px; opacity: 0.95;">Total Applications</span>
                <div class="rounded-circle p-2 bg-white shadow-sm d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                    <i class="bi bi-file-earmark-text-fill fs-5" style="color: #004225;"></i>
                </div>
            </div>
            <div class="d-flex align-items-center justify-content-between">
                <span class="display-6 fw-bold text-white">{{ number_format($total_requests_count) }}</span>
                <span class="badge bg-white fw-bold rounded-pill px-3 py-1.5 shadow-sm" style="color: #004225 !important; font-size: 11px;">Active Requests</span>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-4 rounded-4 stat-card-white">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="small fw-bold text-secondary text-uppercase tracking-wider" style="font-size: 11px;">In Processing (Paid)</span>
                <div class="rounded-circle p-2 icon-box-emerald d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                    <i class="bi bi-check-circle-fill fs-5"></i>
                </div>
            </div>
            <div class="d-flex align-items-center justify-content-between">
                <span class="display-6 fw-bold text-dark">{{ number_format($paid_requests_count) }}</span>
                <span class="badge bg-success bg-opacity-10 text-success fw-bold rounded-pill px-3 py-1.5" style="font-size: 11px;"><i class="bi bi-shield-check me-1"></i> Verified</span>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-4 rounded-4 stat-card-white">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="small fw-bold text-secondary text-uppercase tracking-wider" style="font-size: 11px;">Awaiting Checkout</span>
                <div class="rounded-circle p-2 icon-box-amber d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                    <i class="bi bi-credit-card-2-front fs-5"></i>
                </div>
            </div>
            <div class="d-flex align-items-center justify-content-between">
                <span class="display-6 fw-bold text-dark">{{ number_format($unpaid_requests_count) }}</span>
                <span class="badge bg-warning bg-opacity-10 text-warning fw-bold rounded-pill px-3 py-1.5" style="font-size: 11px;"><i class="bi bi-exclamation-circle me-1"></i> Payment Due</span>
            </div>
        </div>
    </div>
</div>

<!-- Applications Table Card -->
<div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h5 fw-bold text-dark mb-0">My Applications & Processing Tracking</h2>
        <a href="{{ route('client.services') }}" class="btn text-white btn-sm rounded-pill px-4 fw-semibold" style="background-color: #004225;">+ Apply For New Service</a>
    </div>

    @if(count($requests) > 0)
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr class="text-secondary small">
                        <th>Application Ref</th>
                        <th>Service Requested</th>
                        <th>Amount Paid</th>
                        <th>Payment Status</th>
                        <th>Processing Stage</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($requests as $request)
                        <tr>
                            <td>
                                <span class="font-monospace fw-bold text-primary small">{{ $request->reference_number ?? ('DE-' . $request->id) }}</span>
                            </td>
                            <td>
                                <span class="fw-bold text-dark">{{ $request->service_name }}</span><br>
                                <span class="text-muted small">Submitted {{ $request->created_at->format('M d, Y') }}</span>
                            </td>
                            <td class="fw-semibold text-dark">${{ number_format($request->amount_paid, 2) }}</td>
                            <td>
                                @if($request->payment_status === 'Paid')
                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1">Paid</span>
                                @else
                                    <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3 py-1">Unpaid</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $statusBadge = match($request->status) {
                                        'Completed' => 'bg-success text-white',
                                        'Processing' => 'bg-info text-dark',
                                        'Cancelled' => 'bg-danger text-white',
                                        default => 'bg-warning text-dark'
                                    };
                                @endphp
                                <span class="badge {{ $statusBadge }} rounded-pill px-3 py-1">{{ $request->status }}</span>
                            </td>
                            <td>
                                <a href="{{ route('client.request.details', $request->id) }}" class="btn btn-outline-dark btn-sm rounded-pill px-3">View Details</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="text-center py-5">
            <i class="bi bi-clipboard fs-1 text-muted"></i>
            <p class="text-secondary mt-2 small mb-4">You have not created any service applications yet.</p>
            <a href="{{ route('client.services') }}" class="btn text-white rounded-pill px-4" style="background-color: #004225;">Browse Service Catalog</a>
        </div>
    @endif
</div>
@endsection
