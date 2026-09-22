@extends('layouts.dashboard')

@section('title', 'Application Details - ' . ($request->reference_number ?? ('DE-' . $request->id)))

@section('content')
<div class="mb-4">
    <a href="{{ route('client.requests') }}" class="btn btn-outline-secondary btn-sm rounded-pill mb-2"><i class="bi bi-arrow-left me-1"></i> Back to My Applications</a>
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <h1 class="h3 fw-bold text-dark mb-0">{{ $request->service_name }}</h1>
                <span class="badge {{ $request->application_method === 'manual' ? 'bg-warning text-dark' : 'bg-info text-dark' }} font-monospace">
                    <i class="bi {{ $request->application_method === 'manual' ? 'bi-file-earmark-arrow-down' : 'bi-laptop' }} me-1"></i>
                    {{ ucfirst($request->application_method ?? 'online') }} Application
                </span>
            </div>
            <span class="font-monospace fw-bold text-success fs-6">Tracking Reference: {{ $request->reference_number }}</span>
        </div>
        <div>
            <span class="badge bg-success fs-6 px-3 py-2" style="background-color: #004225 !important;">
                Status: {{ $request->status }}
            </span>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Requirement 8: Interactive Application Progress Stepper / Timeline -->
<div class="card border-0 shadow-sm p-4 rounded-4 bg-white mb-4">
    <h5 class="fw-bold text-dark mb-3"><i class="bi bi-diagram-3 me-2 text-success"></i> Application Lifecycle Progress</h5>
    
    @php
        $allStages = $request->service && $request->service->workflowStages 
            ? $request->service->workflowStages->where('is_user_visible', true)->sortBy('sort_order')->values() 
            : collect();

        $histories = $request->stageHistories ? $request->stageHistories->where('is_user_visible', true) : collect();
        $completedStageIds = $histories->pluck('stage_id')->filter()->toArray();
        $completedStageNames = $histories->pluck('stage_name')->map(fn($n) => strtolower(trim($n)))->filter()->toArray();

        // Find matching current stage object based on current_stage_id, current_stage_name, or status
        $currentStageObj = $allStages->first(function($stg) use ($request) {
            return ($request->current_stage_id && $stg->id == $request->current_stage_id)
                || (strcasecmp($stg->stage_name, $request->current_stage_name ?? '') === 0)
                || (strcasecmp($stg->stage_name, $request->status ?? '') === 0)
                || (strcasecmp($stg->status_key, $request->status ?? '') === 0);
        });

        $currentSortOrder = $currentStageObj ? $currentStageObj->sort_order : 1;

        // If status is 'Payment Confirmed' or payment_status is 'Paid', at least Stage 2 (sort_order 2) must be active/completed
        if (($request->status === 'Payment Confirmed' || $request->payment_status === 'Paid') && $currentSortOrder < 2) {
            $currentSortOrder = 2;
        }
    @endphp

    @if($allStages->count() > 0)
        <div class="d-flex flex-column flex-md-row justify-content-between position-relative my-3 text-center">
            @foreach($allStages as $idx => $stg)
                @php
                    $isStageInHistory = in_array($stg->id, $completedStageIds) || in_array(strtolower(trim($stg->stage_name)), $completedStageNames);

                    $isCurrent = ($currentStageObj && $stg->id == $currentStageObj->id)
                        || ($stg->sort_order == $currentSortOrder)
                        || (strcasecmp($stg->stage_name, $request->current_stage_name ?? '') === 0)
                        || (strcasecmp($stg->stage_name, $request->status ?? '') === 0);

                    $isCompleted = ($stg->sort_order < $currentSortOrder) 
                        || ($isStageInHistory && $stg->sort_order < $currentSortOrder);

                    // If a stage comes BEFORE currentSortOrder, it's completed and not current
                    if ($stg->sort_order < $currentSortOrder) {
                        $isCompleted = true;
                        $isCurrent = false;
                    }
                @endphp
                <div class="flex-grow-1 p-2 mb-3 mb-md-0 position-relative">
                    <div class="mx-auto rounded-circle d-flex align-items-center justify-content-center fw-bold fs-6 mb-2 shadow-sm"
                        style="width: 44px; height: 44px; {{ $isCompleted ? 'background-color: #004225; color: #fff;' : ($isCurrent ? 'background-color: #d4af37; color: #000;' : 'background-color: #e2e8f0; color: #64748b;') }}">
                        @if($isCompleted)
                            <i class="bi bi-check-lg"></i>
                        @else
                            {{ $idx + 1 }}
                        @endif
                    </div>
                    <span class="d-block fw-semibold small {{ $isCurrent || $isCompleted ? 'text-dark fw-bold' : 'text-secondary' }}">{{ $stg->stage_name }}</span>
                    @if($stg->estimated_days)
                        <span class="d-block text-muted" style="font-size: 10px;">~{{ $stg->estimated_days }} days</span>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <!-- Generic Default Stepper if no workflow stages configured -->
        <div class="d-flex flex-column flex-md-row justify-content-between position-relative my-3 text-center">
            <div class="flex-grow-1 p-2 mb-2">
                <div class="mx-auto rounded-circle d-flex align-items-center justify-content-center fw-bold text-white mb-2" style="width: 40px; height: 40px; background-color: #004225;">
                    <i class="bi bi-check-lg"></i>
                </div>
                <span class="d-block fw-semibold small text-dark">Submitted</span>
            </div>
            <div class="flex-grow-1 p-2 mb-2">
                <div class="mx-auto rounded-circle d-flex align-items-center justify-content-center fw-bold text-dark mb-2" style="width: 40px; height: 40px; background-color: #d4af37;">
                    2
                </div>
                <span class="d-block fw-semibold small text-dark">Processing</span>
            </div>
            <div class="flex-grow-1 p-2 mb-2">
                <div class="mx-auto rounded-circle d-flex align-items-center justify-content-center fw-bold text-secondary mb-2" style="width: 40px; height: 40px; background-color: #e2e8f0;">
                    3
                </div>
                <span class="d-block fw-semibold small text-secondary">Completed</span>
            </div>
        </div>
    @endif
</div>

<div class="row g-4">
    <!-- Left Column: Details & Documents -->
    <div class="col-lg-8">
        <!-- Applicant & Service Summary -->
        <div class="card border-0 shadow-sm p-4 rounded-4 bg-white mb-4">
            <h5 class="fw-bold text-dark mb-3">Service & Financial Summary</h5>
            <div class="row g-3 small">
                <div class="col-sm-6">
                    <span class="text-muted d-block">Service Name</span>
                    <span class="fw-semibold text-dark fs-6">{{ $request->service_name }}</span>
                </div>
                <div class="col-sm-6">
                    <span class="text-muted d-block">Total Application Fee</span>
                    <span class="fw-bold text-dark fs-6">${{ number_format($request->price, 2) }}</span>
                </div>
                <div class="col-sm-6">
                    <span class="text-muted d-block">Amount Paid</span>
                    <span class="fw-bold text-success fs-6">${{ number_format($request->amount_paid, 2) }}</span>
                </div>
                <div class="col-sm-6">
                    <span class="text-muted d-block">Outstanding Balance</span>
                    <span class="fw-bold {{ $request->outstanding_balance > 0 ? 'text-danger' : 'text-muted' }} fs-6">
                        ${{ number_format($request->outstanding_balance, 2) }}
                    </span>
                </div>
                <div class="col-sm-6">
                    <span class="text-muted d-block">Assigned Officer</span>
                    <span class="fw-semibold text-dark fs-6">{{ $request->assignedStaff->name ?? ($request->assignedRole->name ?? 'Dooter Support Desk') }}</span>
                </div>
                <div class="col-sm-6">
                    <span class="text-muted d-block">Submission Date</span>
                    <span class="fw-semibold text-dark fs-6">{{ $request->created_at->format('M d, Y, h:i A') }}</span>
                </div>
            </div>

            @if($request->manual_form_path || $request->passport_photo_path)
                <div class="mt-4 pt-3 border-top d-flex flex-wrap gap-4 align-items-center">
                    @if($request->passport_photo_path)
                        <div>
                            <span class="text-muted d-block small mb-2">Uploaded Passport Photograph</span>
                            <a href="{{ asset($request->passport_photo_path) }}" target="_blank">
                                <img src="{{ asset($request->passport_photo_path) }}" alt="Passport Photo" class="rounded border p-1" style="width: 90px; height: 110px; object-fit: cover;">
                            </a>
                        </div>
                    @endif
                    @if($request->manual_form_path)
                        <div>
                            <span class="text-muted d-block small mb-2">Uploaded Scanned Application Form</span>
                            <a href="{{ asset($request->manual_form_path) }}" target="_blank" class="btn btn-outline-success btn-sm font-semibold rounded-pill px-3 py-2" style="border-color: #004225; color: #004225;">
                                <i class="bi bi-file-earmark-pdf me-1"></i> View Scanned Form
                            </a>
                        </div>
                    @endif
                </div>
            @endif

            @if(is_array($request->form_data) && count($request->form_data) > 0)
                <hr class="my-4 border-light">
                <h5 class="fw-bold text-dark mb-3">Submitted Questionnaire Details</h5>
                <div class="row g-3 small bg-light p-3 rounded-3">
                    @foreach($request->form_data as $key => $val)
                        <div class="col-sm-6">
                            <span class="text-muted d-block">{{ ucwords(str_replace('_', ' ', $key)) }}</span>
                            <span class="fw-semibold text-dark">{{ is_array($val) ? implode(', ', $val) : $val }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Documents Verification Status -->
        <div class="card border-0 shadow-sm p-4 rounded-4 bg-white mb-4">
            <h5 class="fw-bold text-dark mb-3">Submitted Documents & Verification</h5>
            @if($request->requestDocuments && $request->requestDocuments->count() > 0)
                <div class="list-group list-group-flush">
                    @foreach($request->requestDocuments as $doc)
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-3 border-bottom">
                            <div>
                                <span class="fw-semibold text-dark d-block">
                                    <i class="bi bi-file-earmark-text me-2 text-success"></i> {{ $doc->document_name }}
                                </span>
                                @if($doc->admin_notes)
                                    <small class="text-danger d-block mt-1"><i class="bi bi-exclamation-triangle me-1"></i> Officer Note: {{ $doc->admin_notes }}</small>
                                @endif
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-{{ $doc->status === 'Accepted' ? 'success' : ($doc->status === 'Requires Correction' || $doc->status === 'Rejected' ? 'danger' : 'warning') }} bg-opacity-10 text-{{ $doc->status === 'Accepted' ? 'success' : ($doc->status === 'Requires Correction' || $doc->status === 'Rejected' ? 'danger' : 'warning') }} px-3 py-1">
                                    {{ $doc->status }}
                                </span>
                                <a href="{{ asset($doc->file_path) }}" target="_blank" class="btn btn-light btn-sm rounded-pill px-3">View File</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-light p-3 rounded text-center small text-secondary">
                    <i class="bi bi-info-circle me-1"></i> No documents attached to this application.
                </div>
            @endif
        </div>

        <!-- Public Stage Notes & Communications -->
        @if($request->applicationNotes && $request->applicationNotes->where('is_internal', false)->count() > 0)
            <div class="card border-0 shadow-sm p-4 rounded-4 bg-white mb-4">
                <h5 class="fw-bold text-dark mb-3"><i class="bi bi-chat-left-text me-2 text-success"></i> Officer Updates & Notes</h5>
                <div class="list-group list-group-flush">
                    @foreach($request->applicationNotes->where('is_internal', false) as $note)
                        <div class="list-group-item px-0 py-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <strong class="small text-dark">{{ $note->user ? $note->user->name : 'Processing Officer' }}</strong>
                                <span class="text-muted" style="font-size: 11px;">{{ $note->created_at->format('M d, Y H:i') }}</span>
                            </div>
                            <p class="small text-secondary mb-0">{{ $note->note }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <!-- Right Column: Status & Credo Payment -->
    <div class="col-lg-4">
        <!-- Status Card -->
        <div class="card border-0 shadow-sm p-4 rounded-4 bg-white mb-4">
            <h5 class="fw-bold text-dark mb-3">Current Application Status</h5>
            
            <div class="p-3 bg-light rounded-3 text-center mb-3">
                <span class="text-muted small d-block">Current Stage</span>
                <h6 class="fw-bold text-dark my-1">{{ $request->current_stage_name ?? $request->status }}</h6>
            </div>
            
            <div class="small">
                <div class="d-flex justify-content-between py-2 border-bottom border-light">
                    <span class="text-muted">Payment status:</span>
                    <span class="badge bg-{{ $request->payment_status === 'Paid' ? 'success' : 'danger' }} bg-opacity-10 text-{{ $request->payment_status === 'Paid' ? 'success' : 'danger' }}">
                        {{ $request->payment_status }}
                    </span>
                </div>
                @if($request->payment_gateway)
                    <div class="d-flex justify-content-between py-2 border-bottom border-light">
                        <span class="text-muted">Payment Gateway:</span>
                        <span class="fw-semibold text-dark text-uppercase">{{ $request->payment_gateway }}</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Payment Checkout Card (Shown if unpaid or has balance) -->
        @if($request->payment_status !== 'Paid' || $request->outstanding_balance > 0)
            @php
                $activeGw = strtolower($settings->payment_gateway ?? config('services.payment.gateway') ?: env('PAYMENT_GATEWAY', 'paystack'));
                $gwLabel = $activeGw === 'credo' ? 'Credo' : 'Paystack';
                $currencySymbol = ($settings->default_currency ?? config('services.payment.currency', 'NGN')) === 'NGN' ? '₦' : '$';
                $currencyCode = $settings->default_currency ?? config('services.payment.currency', 'NGN');
            @endphp
            <div class="card border-0 shadow-sm p-4 rounded-4 text-white mb-4" style="background: linear-gradient(135deg, #004225, #006637);">
                <h5 class="fw-bold text-white mb-2"><i class="bi bi-credit-card me-2"></i> {{ $gwLabel }} Checkout</h5>
                <p class="small text-white-50 mb-3">Pay securely online using credit/debit card or bank transfer via {{ $gwLabel }} Gateway.</p>

                <div class="d-flex justify-content-between align-items-center bg-white bg-opacity-10 p-3 rounded-3 mb-4">
                    <span class="small text-white-50">Amount Due:</span>
                    <span class="fw-bold fs-4 text-white">{{ $currencyCode }} {{ number_format($request->outstanding_balance > 0 ? $request->outstanding_balance : $request->price, 2) }}</span>
                </div>

                <form action="{{ route('payment.checkout', $request->id) }}" method="POST" id="payForm" onsubmit="return handlePaySubmit(event, this)">
                    @csrf
                    <input type="hidden" name="amount" value="{{ $request->outstanding_balance > 0 ? $request->outstanding_balance : $request->price }}">
                    <input type="hidden" name="gateway" value="{{ $activeGw }}">
                    <button type="submit" id="payBtn" onclick="triggerPaySpinner(this)" class="btn btn-light w-100 py-3 rounded-3 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2" style="color: #004225; transition: all 0.25s ease;">
                        <i class="bi bi-lock-fill me-1"></i>
                        <span>Pay Now with {{ $gwLabel }}</span>
                    </button>
                </form>
            </div>

            <script>
                function resetPayButton() {
                    const btn = document.getElementById('payBtn');
                    const form = document.getElementById('payForm');
                    if (form) {
                        form.dataset.submitting = 'false';
                    }
                    if (btn) {
                        btn.dataset.spinning = 'false';
                        btn.style.pointerEvents = 'auto';
                        btn.style.opacity = '1';
                        btn.classList.remove('shadow-lg');
                        btn.innerHTML = `
                            <i class="bi bi-lock-fill me-1"></i>
                            <span>Pay Now with {{ $gwLabel }}</span>
                        `;
                    }
                }

                function triggerPaySpinner(btn) {
                    if (!btn || btn.dataset.spinning === 'true') return;
                    btn.dataset.spinning = 'true';
                    btn.style.pointerEvents = 'none';
                    btn.style.opacity = '0.9';
                    btn.classList.add('shadow-lg');
                    btn.innerHTML = `
                        <div class="d-flex align-items-center justify-content-center gap-2 py-1">
                            <span class="spinner-border spinner-border-sm me-1" role="status" style="width: 1.25rem; height: 1.25rem; border-width: 2.5px; color: #004225 !important;"></span>
                            <span class="spinner-grow spinner-grow-sm me-1" role="status" style="width: 0.75rem; height: 0.75rem; animation-delay: 0.15s; color: #004225 !important;"></span>
                            <span class="spinner-grow spinner-grow-sm me-1" role="status" style="width: 0.5rem; height: 0.5rem; animation-delay: 0.3s; color: #004225 !important;"></span>
                            <span class="fw-bold fs-6 ms-1" style="color: #004225;">Connecting to {{ $gwLabel }}...</span>
                        </div>
                    `;
                }

                function handlePaySubmit(event, form) {
                    if (form.dataset.submitting === 'true') {
                        event.preventDefault();
                        return false;
                    }

                    event.preventDefault();
                    form.dataset.submitting = 'true';

                    const btn = document.getElementById('payBtn');
                    triggerPaySpinner(btn);

                    setTimeout(function() {
                        form.submit();
                    }, 1200);
                }

                window.addEventListener('pageshow', function() {
                    resetPayButton();
                });
                document.addEventListener('DOMContentLoaded', function() {
                    resetPayButton();
                });
            </script>
        @endif
    </div>
</div>
@endsection
