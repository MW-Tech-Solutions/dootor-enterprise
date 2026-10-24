@extends('layouts.dashboard')

@section('title', 'Client Dashboard - ' . ($settings->platform_name ?? 'Dooter Enterprises'))

@section('styles')
@parent
<style>
    .stat-card-gradient {
        background: linear-gradient(135deg, #004d25 0%, #006637 55%, #004825 100%);
        color: #ffffff;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        border-radius: 20px !important;
        border: 1px solid rgba(255, 255, 255, 0.12) !important;
    }
    .stat-card-gradient::after {
        content: '';
        position: absolute;
        top: -25%;
        right: -15%;
        width: 150px;
        height: 150px;
        background: rgba(255, 255, 255, 0.08);
        border-radius: 50%;
        pointer-events: none;
    }
    .stat-card-gradient:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 24px rgba(0, 66, 37, 0.25) !important;
    }
    .stat-card-white {
        background: #ffffff;
        border: 1px solid #f1f5f9 !important;
        border-radius: 20px !important;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.03) !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .stat-card-white:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(15, 23, 42, 0.06) !important;
    }
    .hover-green:hover {
        color: #004225 !important;
    }
</style>
@endsection

@section('content')
<div class="mb-4">
    <h1 class="h3 fw-bold text-dark mb-1" style="font-weight: 800 !important; font-size: 1.55rem; letter-spacing: -0.02em;">Welcome back, {{ Auth::user()->first_name }}!</h1>
    <p class="text-secondary small mb-0" style="color: #64748b !important; font-size: 13.5px; line-height: 1.45;">Track your active document processing applications and explore available services</p>
</div>

<!-- High Contrast Premium Stats Row (Matches Exact Mockup: Card 1 full-width, Card 2 & 3 side-by-side on mobile) -->
<div class="row g-3 g-md-4 mb-4">
    <!-- Total Applications -->
    <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm p-3.5 p-md-4 rounded-4 stat-card-gradient h-100" style="padding: 22px 20px !important;">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="small fw-bold text-white text-uppercase tracking-wider" style="font-size: 11px; opacity: 0.95; letter-spacing: 0.04em;">TOTAL APPLICATIONS</span>
                <div class="rounded-circle p-2 bg-white shadow-sm d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; flex-shrink: 0;">
                    <i class="bi bi-file-earmark-text-fill fs-5" style="color: #004d25;"></i>
                </div>
            </div>
            <div class="d-flex align-items-end justify-content-between mt-auto">
                <span class="display-5 fw-bold text-white" style="font-weight: 800; font-size: 2.75rem; line-height: 1;">{{ number_format($total_requests_count) }}</span>
                <span class="badge bg-white fw-bold rounded-pill px-3 py-1.5 shadow-sm" style="color: #004d25 !important; font-size: 11px;">Active Requests</span>
            </div>
        </div>
    </div>

    <!-- In Processing (Paid) -->
    <div class="col-6 col-md-4">
        <div class="card border-0 shadow-sm p-3.5 p-md-4 rounded-4 stat-card-white h-100 d-flex flex-column justify-content-between" style="padding: 20px 16px !important;">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="fw-bold text-secondary text-uppercase tracking-wider" style="font-size: 10px; letter-spacing: 0.02em; color: #64748b !important;">IN PROCESSING (PAID)</span>
                <i class="bi bi-check-circle-fill text-success fs-5"></i>
            </div>
            <div class="my-2">
                <span class="display-6 fw-bold text-dark" style="font-weight: 800; font-size: 2.3rem; line-height: 1;">{{ number_format($paid_requests_count) }}</span>
            </div>
            <div class="d-flex justify-content-end">
                <span class="badge fw-bold rounded-pill px-2.5 py-1" style="background-color: #ecfdf5; color: #059669; font-size: 10.5px;">
                    <i class="bi bi-shield-check me-1"></i> Verified
                </span>
            </div>
        </div>
    </div>

    <!-- Awaiting Checkout -->
    <div class="col-6 col-md-4">
        <div class="card border-0 shadow-sm p-3.5 p-md-4 rounded-4 stat-card-white h-100 d-flex flex-column justify-content-between" style="padding: 20px 16px !important;">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="fw-bold text-secondary text-uppercase tracking-wider" style="font-size: 10px; letter-spacing: 0.02em; color: #64748b !important;">AWAITING CHECKOUT</span>
                <div class="rounded-2 d-flex align-items-center justify-content-center" style="background-color: #fffbeb; color: #f59e0b; width: 28px; height: 28px; border: 1px solid #fef3c7;">
                    <i class="bi bi-credit-card-2-front" style="font-size: 14px;"></i>
                </div>
            </div>
            <div class="my-2">
                <span class="display-6 fw-bold text-dark" style="font-weight: 800; font-size: 2.3rem; line-height: 1;">{{ number_format($unpaid_requests_count) }}</span>
            </div>
            <div class="d-flex justify-content-end">
                <span class="badge fw-bold rounded-pill px-2.5 py-1" style="background-color: #fffbeb; color: #d97706; font-size: 10.5px;">
                    <i class="bi bi-exclamation-circle me-1"></i> Payment Due
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Active Unfinished Draft Applications Section (Requirement #10) -->
@if(isset($drafts) && $drafts->count() > 0)
    <div class="card border-0 shadow-sm p-3.5 p-md-4 rounded-4 bg-white mb-4" style="border-left: 5px solid #d4af37 !important; border-radius: 20px !important; padding: 22px 20px !important;">
        <div class="mb-3">
            <span class="badge text-dark px-2.5 py-1.5 rounded-pill small fw-bold text-uppercase mb-2 d-inline-flex align-items-center" style="background-color: #f59e0b; font-size: 10px; letter-spacing: 0.04em;">
                <i class="bi bi-clock-history me-1.5 fw-bold"></i> AUTO-SAVED UNFINISHED WORK
            </span>
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="h5 fw-bold text-dark mb-0" style="font-weight: 800; font-size: 1.15rem; letter-spacing: -0.01em;">Continue Your Active Applications</h2>
                <span class="text-muted small fw-semibold" style="font-size: 11.5px;">{{ $drafts->count() }} Drafts Pending</span>
            </div>
        </div>

        <div class="row g-3">
            @foreach($drafts as $draft)
                <div class="col-12 col-md-6">
                    <div class="p-3.5 border rounded-4 bg-light h-100 d-flex flex-column justify-content-between" style="border-color: #e2e8f0 !important; border-radius: 16px !important; background-color: #f8fafc !important; padding: 18px !important;">
                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="font-monospace fw-bold small" style="color: #004225 !important; font-size: 12px; letter-spacing: 0.02em;">{{ $draft->reference_number }}</span>
                                <span class="badge bg-secondary-subtle text-secondary small fw-semibold" style="font-size: 10.5px; border-radius: 6px; padding: 4px 8px;">Step {{ $draft->current_step ?? 1 }} of 4</span>
                            </div>
                            <h3 class="h6 fw-bold text-dark mb-0.5" style="font-size: 14.5px; font-weight: 700;">{{ $draft->service_name }}</h3>
                            @if($draft->sub_service_name)
                                <p class="text-muted small mb-2" style="font-size: 12px;">{{ $draft->sub_service_name }}</p>
                            @else
                                <div class="mb-2"></div>
                            @endif
                            <div class="progress mb-2" style="height: 5px; background-color: #e2e8f0; border-radius: 999px;">
                                <div class="progress-bar" role="progressbar" style="width: {{ $draft->progress_percent ?? 25 }}%; background-color: #004225 !important; border-radius: 999px;"></div>
                            </div>
                            <div class="d-flex justify-content-between text-muted" style="font-size: 11px;">
                                <span>Started: {{ $draft->created_at->format('M d, Y') }}</span>
                                <span>Last Saved: {{ $draft->updated_at->diffForHumans() }}</span>
                            </div>
                        </div>

                        <div class="pt-3 border-top mt-3 d-flex justify-content-between align-items-center" style="border-color: #e2e8f0 !important;">
                            <span class="text-secondary small d-flex align-items-center gap-1.5" style="font-size: 11.5px;"><i class="bi bi-shield-check text-success"></i> Draft Secured</span>
                            <a href="{{ route('client.application.step', ['serviceRequest' => $draft->id, 'step' => max(1, $draft->current_step ?? 1)]) }}" class="text-decoration-none fw-bold text-dark d-flex align-items-center gap-1 hover-green" style="font-size: 12.5px;">
                                <span>Continue Application</span>
                                <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif

<!-- Applications Table Card -->
<div class="card border-0 shadow-sm p-3.5 p-md-4 rounded-4 bg-white mb-4" style="border: 1px solid #eef2f6 !important; border-radius: 20px !important; padding: 22px 20px !important;">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <h2 class="h5 fw-bold text-dark mb-0">My Applications & Processing Tracking</h2>
        <a href="{{ route('client.services') }}" class="btn text-white btn-sm rounded-pill px-3 px-md-4 fw-semibold" style="background-color: #004225;">+ Apply For New Service</a>
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
