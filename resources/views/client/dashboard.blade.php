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

<!-- Active Unfinished Draft Applications Section (Requirement #10) -->
@if(isset($drafts) && $drafts->count() > 0)
    <div class="card border-0 shadow-sm p-4 rounded-4 bg-white mb-4" style="border-left: 5px solid #d4af37 !important;">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <span class="badge bg-warning text-dark px-2.5 py-1 rounded-pill small fw-bold text-uppercase mb-1">
                    <i class="bi bi-clock-history me-1"></i> Auto-Saved Unfinished Work
                </span>
                <h2 class="h5 fw-bold text-dark mb-0">Continue Your Active Applications</h2>
            </div>
            <span class="text-muted small fw-semibold">{{ $drafts->count() }} Drafts Pending</span>
        </div>

        <div class="row g-3">
            @foreach($drafts as $draft)
                <div class="col-12 col-md-6">
                    <div class="p-3 border rounded-3 bg-light h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="font-monospace fw-bold text-success small" style="color: #004225 !important;">{{ $draft->reference_number }}</span>
                                <span class="badge bg-secondary-subtle text-dark small" style="font-size: 11px;">Step {{ $draft->current_step ?? 1 }} of 4</span>
                            </div>
                            <h3 class="h6 fw-bold text-dark mb-1">{{ $draft->service_name }}</h3>
                            @if($draft->sub_service_name)
                                <p class="text-muted small mb-2">{{ $draft->sub_service_name }}</p>
                            @endif
                            <div class="progress mb-2" style="height: 6px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $draft->progress_percent ?? 25 }}%; background-color: #004225 !important;"></div>
                            </div>
                            <div class="d-flex justify-content-between text-muted" style="font-size: 11px;">
                                <span>Started: {{ $draft->created_at->format('M d, Y') }}</span>
                                <span>Last Saved: {{ $draft->updated_at->diffForHumans() }}</span>
                            </div>
                        </div>

                        <div class="pt-3 border-top mt-3 d-flex justify-content-between align-items-center">
                            <span class="text-muted" style="font-size: 11px;"><i class="bi bi-shield-check text-success"></i> Draft Secured</span>
                            <a href="{{ route('client.application.step', ['serviceRequest' => $draft->id, 'step' => max(1, $draft->current_step ?? 1)]) }}" class="btn btn-brand-primary btn-sm rounded-pill px-3">
                                <span>Continue Application</span>
                                <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif

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
                                @if($request->payment_reference)
                                    <div class="text-muted font-monospace mt-0.5" style="font-size: 10px;" title="Transaction ID / Payment Ref">
                                        <i class="bi bi-receipt me-1 text-success"></i>Tx: {{ $request->payment_reference }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                <span class="fw-bold text-dark">{{ $request->service_name }}</span><br>
                                @if($request->sub_service_name)
                                    <span class="text-secondary small d-block">{{ $request->sub_service_name }}</span>
                                @endif
                                <span class="text-muted small">Submitted {{ $request->created_at->format('M d, Y') }}</span>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $currencySymbol }}{{ number_format($request->amount_paid, 2) }}</div>
                                @if($request->price != $request->amount_paid && $request->price > 0)
                                    <div class="text-muted" style="font-size: 11px;">Total: {{ $currencySymbol }}{{ number_format($request->price, 2) }}</div>
                                @endif
                            </td>
                            <td>
                                @if($request->payment_status === 'Paid')
                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1"><i class="bi bi-check-circle me-1"></i>Paid</span>
                                @elseif($request->payment_status === 'Partial')
                                    <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-3 py-1">Partial</span>
                                @else
                                    <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3 py-1"><i class="bi bi-clock me-1"></i>Unpaid</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $stageDisplay = $request->current_stage_name ?? $request->status;
                                    $statusBadge = match(true) {
                                        in_array($request->status, ['Completed', 'Approved', 'Verified']) => 'bg-success text-white',
                                        in_array($request->status, ['Processing', 'In Progress', 'Under Review', 'Document Verification']) => 'bg-info text-dark',
                                        in_array($request->status, ['Payment Confirmed']) => 'bg-warning text-dark',
                                        in_array($request->status, ['Cancelled', 'Rejected']) => 'bg-danger text-white',
                                        default => 'bg-warning text-dark'
                                    };
                                @endphp
                                <span class="badge {{ $statusBadge }} rounded-pill px-3 py-1">{{ $stageDisplay }}</span>
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
