@extends('layouts.dashboard')

@section('title', 'Application Expirations & Lifecycle - ' . ($settings['company_name'] ?? 'DOOTOR ENTERPRISES'))

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <div class="rounded-3 p-2 d-flex align-items-center justify-content-center shadow-sm" style="width: 38px; height: 38px; background: #004225; color: #d4af37;">
                    <i class="bi bi-hourglass-split fs-5"></i>
                </div>
                <h1 class="h3 fw-bold text-dark mb-0">Application Expirations & Lifecycle</h1>
                <span class="badge bg-dark rounded-pill px-3 py-1 small">{{ $stats['total_expired'] }} Ready to Purge</span>
            </div>
            <p class="text-secondary small mb-0">
                Monitor and enforce automated expiration for halfway unfinished applications (<strong>{{ $settings['draft_expiry_value'] }} {{ $settings['draft_expiry_unit'] }}</strong>) and paid incomplete applications (<strong>{{ $settings['paid_incomplete_expiry_value'] }} {{ $settings['paid_incomplete_expiry_unit'] }}</strong>).
            </p>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap flex-sm-nowrap flex-shrink-0">
            <button class="btn btn-white border shadow-sm rounded-pill px-3.5 py-2 d-inline-flex align-items-center gap-2 text-dark fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#expirationPoliciesCard" style="font-size: 13px; white-space: nowrap;">
                <i class="bi bi-sliders text-success"></i>
                <span>Configure Policies</span>
            </button>
            <button class="btn btn-danger rounded-pill px-3.5 py-2 d-inline-flex align-items-center gap-2 shadow-sm fw-semibold" type="button" data-bs-toggle="modal" data-bs-target="#batchPurgeModal" {{ $stats['total_expired'] === 0 ? 'disabled' : '' }} style="font-size: 13px; white-space: nowrap;">
                <i class="bi bi-trash3-fill"></i>
                <span>Purge Expired Now ({{ $stats['total_expired'] }})</span>
            </button>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm d-flex align-items-center gap-2 mb-4" role="alert">
            <i class="bi bi-check-circle-fill fs-5"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm d-flex align-items-center gap-2 mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill fs-5"></i>
            <div>{{ session('error') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Quick Stats Row -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm p-3 rounded-4 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase d-block mb-1" style="font-size: 11px;">Unfinished Drafts</span>
                        <h3 class="fw-bold text-dark mb-0">{{ $stats['total_drafts'] }}</h3>
                        <span class="text-muted small" style="font-size: 11.5px;">{{ $stats['expired_drafts'] }} reached {{ $settings['draft_expiry_value'] }} {{ $settings['draft_expiry_unit'] }}</span>
                    </div>
                    <div class="rounded-3 p-2 bg-secondary-subtle text-secondary d-flex align-items-center justify-content-center" style="width: 46px; height: 46px; font-size: 22px;">
                        <i class="bi bi-file-earmark-text"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm p-3 rounded-4 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase d-block mb-1" style="font-size: 11px;">Paid Incomplete</span>
                        <h3 class="fw-bold text-primary mb-0">{{ $stats['total_paid_incomplete'] }}</h3>
                        <span class="text-muted small" style="font-size: 11.5px;">{{ $stats['expired_paid_incomplete'] }} reached {{ $settings['paid_incomplete_expiry_value'] }} {{ $settings['paid_incomplete_expiry_unit'] }}</span>
                    </div>
                    <div class="rounded-3 p-2 bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width: 46px; height: 46px; font-size: 22px;">
                        <i class="bi bi-credit-card-2-back-fill"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm p-3 rounded-4 bg-white h-100 border-start border-warning border-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-warning small fw-semibold text-uppercase d-block mb-1" style="font-size: 11px;">Expiring Soon (&le; 5-7d)</span>
                        <h3 class="fw-bold text-warning mb-0">{{ $stats['expiring_soon_drafts'] + $stats['expiring_soon_paid'] }}</h3>
                        <span class="text-muted small" style="font-size: 11.5px;">At risk of system expiration</span>
                    </div>
                    <div class="rounded-3 p-2 bg-warning-subtle text-warning d-flex align-items-center justify-content-center" style="width: 46px; height: 46px; font-size: 22px;">
                        <i class="bi bi-clock-history"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm p-3 rounded-4 bg-white h-100 border-start border-danger border-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-danger small fw-semibold text-uppercase d-block mb-1" style="font-size: 11px;">Total Expired Now</span>
                        <h3 class="fw-bold text-danger mb-0">{{ $stats['total_expired'] }}</h3>
                        <span class="text-muted small" style="font-size: 11.5px;">Eligible for automated deletion</span>
                    </div>
                    <div class="rounded-3 p-2 bg-danger-subtle text-danger d-flex align-items-center justify-content-center" style="width: 46px; height: 46px; font-size: 22px;">
                        <i class="bi bi-exclamation-octagon-fill"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Dynamic Expiration Policies Configuration Card -->
    <div class="collapse mb-4 {{ request()->has('configure') ? 'show' : '' }}" id="expirationPoliciesCard">
        <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
            <div class="card-header bg-dark text-white p-3 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-sliders text-warning"></i>
                    <h5 class="fw-bold mb-0" style="font-size: 15px;">Dynamic Application Expiration Policy Settings</h5>
                </div>
                <span class="badge bg-warning text-dark font-monospace px-2.5 py-1">System Lifecycle</span>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('admin.expirations.settings') }}" method="POST">
                    @csrf
                    <div class="row g-4">
                        <!-- Rule 1: Drafts & Halfway Incomplete -->
                        <div class="col-md-6">
                            <div class="p-4 rounded-4 bg-white border shadow-sm h-100 position-relative overflow-hidden" style="border: 1px solid #e2e8f0 !important;">
                                <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2.5 py-1 fw-semibold" style="font-size: 11px;">
                                            <i class="bi bi-file-earmark-text me-1"></i> Drafts (Unpaid)
                                        </span>
                                        <h6 class="fw-bold text-dark mb-0" style="font-size: 14.5px;">Unfinished Halfway Applications</h6>
                                    </div>
                                </div>
                                <p class="text-secondary small mb-3" style="line-height: 1.6; font-size: 12.5px;">
                                    If a client started an application but didn't finish halfway without payment, the application will automatically expire and be deleted from the system after this timeframe.
                                </p>
                                <div class="row g-2">
                                    <div class="col-7">
                                        <label class="form-label text-secondary fw-semibold extra-small text-uppercase mb-1" style="font-size: 11px;">Inactivity Duration</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-clock-history"></i></span>
                                            <input type="number" name="draft_expiry_value" class="form-control rounded-end-3 border-start-0 fw-semibold" min="1" max="365" value="{{ old('draft_expiry_value', $settings['draft_expiry_value']) }}" required>
                                        </div>
                                    </div>
                                    <div class="col-5">
                                        <label class="form-label text-secondary fw-semibold extra-small text-uppercase mb-1" style="font-size: 11px;">Time Unit</label>
                                        <select name="draft_expiry_unit" class="form-select rounded-3 fw-semibold" required>
                                            <option value="days" {{ old('draft_expiry_unit', $settings['draft_expiry_unit']) === 'days' ? 'selected' : '' }}>Days</option>
                                            <option value="months" {{ old('draft_expiry_unit', $settings['draft_expiry_unit']) === 'months' ? 'selected' : '' }}>Months</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-1.5 text-muted small mt-2.5" style="font-size: 11.5px;">
                                    <i class="bi bi-info-circle text-primary"></i>
                                    <span>System default policy: <strong>30 days</strong>.</span>
                                </div>
                            </div>
                        </div>

                        <!-- Rule 2: Paid Incomplete -->
                        <div class="col-md-6">
                            <div class="p-4 rounded-4 bg-white border shadow-sm h-100 position-relative overflow-hidden" style="border: 1px solid #e2e8f0 !important;">
                                <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1 fw-semibold" style="font-size: 11px;">
                                            <i class="bi bi-credit-card me-1"></i> Paid Applications
                                        </span>
                                        <h6 class="fw-bold text-dark mb-0" style="font-size: 14.5px;">Incomplete After Payment</h6>
                                    </div>
                                </div>
                                <p class="text-secondary small mb-3" style="line-height: 1.6; font-size: 12.5px;">
                                    If a client made payment but did not finish/finalize the application steps, the application will automatically expire and be cleared after this duration.
                                </p>
                                <div class="row g-2">
                                    <div class="col-7">
                                        <label class="form-label text-secondary fw-semibold extra-small text-uppercase mb-1" style="font-size: 11px;">Inactivity Duration</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-calendar-event"></i></span>
                                            <input type="number" name="paid_incomplete_expiry_value" class="form-control rounded-end-3 border-start-0 fw-semibold" min="1" max="365" value="{{ old('paid_incomplete_expiry_value', $settings['paid_incomplete_expiry_value']) }}" required>
                                        </div>
                                    </div>
                                    <div class="col-5">
                                        <label class="form-label text-secondary fw-semibold extra-small text-uppercase mb-1" style="font-size: 11px;">Time Unit</label>
                                        <select name="paid_incomplete_expiry_unit" class="form-select rounded-3 fw-semibold" required>
                                            <option value="months" {{ old('paid_incomplete_expiry_unit', $settings['paid_incomplete_expiry_unit']) === 'months' ? 'selected' : '' }}>Months</option>
                                            <option value="days" {{ old('paid_incomplete_expiry_unit', $settings['paid_incomplete_expiry_unit']) === 'days' ? 'selected' : '' }}>Days</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-1.5 text-muted small mt-2.5" style="font-size: 11.5px;">
                                    <i class="bi bi-info-circle text-primary"></i>
                                    <span>System default policy: <strong>3 months</strong>.</span>
                                </div>
                            </div>
                        </div>

                        <!-- Auto Purge Action & Scheduler Toggle -->
                        <div class="col-12">
                            <div class="p-3.5 rounded-4 bg-light border d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                                <div class="flex-grow-1" style="max-width: 500px;">
                                    <label class="form-label small fw-bold text-dark mb-1">
                                        <i class="bi bi-trash3 text-danger me-1"></i> System Expiration Purge Action
                                    </label>
                                    <select name="purge_action" class="form-select form-select-sm rounded-3">
                                        <option value="delete" {{ old('purge_action', $settings['purge_action']) === 'delete' ? 'selected' : '' }}>
                                            Permanently Delete Record &amp; Associated Files (Zero Orphan Bloat)
                                        </option>
                                        <option value="mark_expired" {{ old('purge_action', $settings['purge_action']) === 'mark_expired' ? 'selected' : '' }}>
                                            Retain in Database &amp; Change Status to 'Expired'
                                        </option>
                                    </select>
                                </div>
                                <div class="form-check form-switch m-0 pt-2 pt-md-0">
                                    <input type="hidden" name="auto_purge_enabled" value="0">
                                    <input class="form-check-input fs-5" type="checkbox" name="auto_purge_enabled" value="1" id="autoPurgeSwitch" {{ old('auto_purge_enabled', $settings['auto_purge_enabled']) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold text-dark ms-2 small d-block" for="autoPurgeSwitch">
                                        Enable Daily Automated Purge Schedule
                                        <span class="text-muted fw-normal d-block" style="font-size: 11px;">Executes daily cron cleanup for all expired records</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                        <button type="button" class="btn btn-outline-secondary btn-sm px-3 rounded-pill" data-bs-toggle="collapse" data-bs-target="#expirationPoliciesCard">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-dark btn-sm px-4 rounded-pill d-flex align-items-center gap-1.5 shadow-sm">
                            <i class="bi bi-check2-circle"></i>
                            <span>Save Expiration Policies</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Main Content Area: Tabs & Filterable Table -->
    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
        <!-- Tab Navigation Bar -->
        <div class="card-header bg-white border-bottom p-0">
            <ul class="nav nav-tabs border-0 px-3 pt-2 gap-2" role="tablist">
                <li class="nav-item">
                    <a class="nav-link border-0 rounded-top-3 px-3 py-2.5 fw-semibold d-flex align-items-center gap-2 {{ $activeTab === 'drafts' ? 'active bg-light text-dark border-bottom border-3 border-dark' : 'text-secondary' }}" 
                       href="{{ route('admin.expirations', ['tab' => 'drafts', 'status_filter' => $statusFilter, 'search' => $search]) }}">
                        <i class="bi bi-file-earmark-text"></i>
                        <span>Unfinished Halfway Drafts</span>
                        <span class="badge {{ $stats['expired_drafts'] > 0 ? 'bg-danger' : 'bg-secondary' }} rounded-pill font-monospace" style="font-size: 11px;">
                            {{ $stats['total_drafts'] }}
                        </span>
                        @if($stats['expired_drafts'] > 0)
                            <span class="badge bg-danger-subtle text-danger border border-danger rounded-pill" style="font-size: 10px;">
                                {{ $stats['expired_drafts'] }} Expired
                            </span>
                        @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link border-0 rounded-top-3 px-3 py-2.5 fw-semibold d-flex align-items-center gap-2 {{ $activeTab === 'paid' ? 'active bg-light text-dark border-bottom border-3 border-dark' : 'text-secondary' }}" 
                       href="{{ route('admin.expirations', ['tab' => 'paid', 'status_filter' => $statusFilter, 'search' => $search]) }}">
                        <i class="bi bi-credit-card-2-back-fill text-primary"></i>
                        <span>Paid Incomplete Applications</span>
                        <span class="badge {{ $stats['expired_paid_incomplete'] > 0 ? 'bg-danger' : 'bg-primary' }} rounded-pill font-monospace" style="font-size: 11px;">
                            {{ $stats['total_paid_incomplete'] }}
                        </span>
                        @if($stats['expired_paid_incomplete'] > 0)
                            <span class="badge bg-danger-subtle text-danger border border-danger rounded-pill" style="font-size: 10px;">
                                {{ $stats['expired_paid_incomplete'] }} Expired
                            </span>
                        @endif
                    </a>
                </li>
            </ul>
        </div>

        <!-- Filter & Search Toolbar -->
        <div class="p-3 bg-light border-bottom">
            <form method="GET" action="{{ route('admin.expirations') }}" class="row g-2 align-items-center">
                <input type="hidden" name="tab" value="{{ $activeTab }}">
                
                <div class="col-md-5">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control form-control-sm border-start-0" placeholder="Search reference #, client name, email, or service..." value="{{ $search }}">
                        @if(!empty($search))
                            <a href="{{ route('admin.expirations', ['tab' => $activeTab, 'status_filter' => $statusFilter]) }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-x"></i></a>
                        @endif
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white text-muted">Status Filter</span>
                        <select name="status_filter" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="all" {{ $statusFilter === 'all' ? 'selected' : '' }}>All Statuses</option>
                            <option value="expired" {{ $statusFilter === 'expired' ? 'selected' : '' }}>Expired (Overdue for deletion)</option>
                            <option value="expiring_soon" {{ $statusFilter === 'expiring_soon' ? 'selected' : '' }}>Expiring Soon (&le; 5-7 days)</option>
                            <option value="active" {{ $statusFilter === 'active' ? 'selected' : '' }}>Active (Within valid period)</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-3 d-flex gap-2 justify-content-md-end">
                    <button type="submit" class="btn btn-dark btn-sm px-3 rounded-pill">
                        Filter
                    </button>
                    @if(!empty($search) || $statusFilter !== 'all')
                        <a href="{{ route('admin.expirations', ['tab' => $activeTab]) }}" class="btn btn-outline-secondary btn-sm px-2.5 rounded-pill" title="Reset Filters">
                            <i class="bi bi-arrow-counterclockwise"></i> Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Applications Table -->
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 13px;">
                <thead class="table-light text-uppercase text-secondary" style="font-size: 11px;">
                    <tr>
                        <th class="ps-4">Reference & Service</th>
                        <th>Client Details</th>
                        <th>Application Step</th>
                        <th>Last Saved / Active</th>
                        <th>Expiration Deadline</th>
                        <th>Lifecycle Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($applications as $app)
                        @php
                            $badge = $app->computed_badge ?? 'active';
                            $daysInactive = $app->computed_days_inactive ?? 0;
                            $expiresAt = $app->computed_expires_at ?? now();
                            $daysRemaining = $app->computed_days_remaining ?? 0;
                            $isExpired = $app->computed_is_expired ?? false;
                        @endphp
                        <tr class="{{ $isExpired ? 'table-danger-subtle' : '' }}">
                            <!-- Reference & Service -->
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-3 p-1.5 {{ $isExpired ? 'bg-danger text-white' : 'bg-light text-dark' }} text-center" style="width: 32px; height: 32px;">
                                        <i class="bi {{ $activeTab === 'paid' ? 'bi-credit-card-2-back' : 'bi-file-earmark' }} fs-6"></i>
                                    </div>
                                    <div>
                                        <span class="fw-bold text-dark font-monospace d-block" style="font-size: 12.5px;">{{ $app->reference_number }}</span>
                                        <span class="text-secondary small d-block">{{ $app->service_name ?? 'Service #' . $app->service_id }}</span>
                                        @if($app->sub_service_name)
                                            <span class="badge bg-secondary-subtle text-secondary border rounded-pill" style="font-size: 10px;">
                                                Sub: {{ $app->sub_service_name }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Client Details -->
                            <td>
                                <div>
                                    <span class="fw-semibold text-dark d-block">
                                        {{ $app->client_name ?: ($app->client ? $app->client->name : 'Anonymous Client') }}
                                    </span>
                                    <span class="text-muted small d-block" style="font-size: 11.5px;">
                                        {{ $app->client_email ?: ($app->client ? $app->client->email : 'No email') }}
                                    </span>
                                    @if($app->client && $app->client->phone)
                                        <span class="text-muted small d-block" style="font-size: 11px;">
                                            <i class="bi bi-telephone me-1"></i>{{ $app->client->phone }}
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <!-- Application Step & Progress -->
                            <td style="min-width: 140px;">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <span class="fw-semibold text-secondary small" style="font-size: 11.5px;">
                                        Step {{ $app->current_step ?? 1 }} of 3
                                    </span>
                                    <span class="fw-bold font-monospace small" style="font-size: 11px;">
                                        {{ $app->progress_percent ?? 0 }}%
                                    </span>
                                </div>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar {{ $isExpired ? 'bg-danger' : ($app->progress_percent > 66 ? 'bg-success' : 'bg-warning') }}" 
                                         role="progressbar" 
                                         style="width: {{ max(10, $app->progress_percent ?? 10) }}%;" 
                                         aria-valuenow="{{ $app->progress_percent ?? 0 }}" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                    </div>
                                </div>
                                @if($activeTab === 'paid')
                                    <span class="badge bg-success-subtle text-success border border-success rounded-pill mt-1" style="font-size: 10px;">
                                        <i class="bi bi-check2-circle me-0.5"></i> Paid: {{ $currencySymbol ?? '$' }}{{ number_format($app->amount_paid ?? 0, 2) }}
                                    </span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning rounded-pill mt-1" style="font-size: 10px;">
                                        <i class="bi bi-clock me-0.5"></i> Payment Unpaid
                                    </span>
                                @endif
                            </td>

                            <!-- Last Activity -->
                            <td>
                                <div>
                                    <span class="fw-semibold text-dark d-block">
                                        {{ $daysInactive }} days ago
                                    </span>
                                    <span class="text-muted small" style="font-size: 11px;">
                                        {{ $app->computed_last_activity ? $app->computed_last_activity->format('M d, Y H:i') : 'N/A' }}
                                    </span>
                                </div>
                            </td>

                            <!-- Expiration Deadline -->
                            <td>
                                <div>
                                    <span class="fw-semibold text-dark d-block">
                                        {{ $expiresAt ? $expiresAt->format('M d, Y') : 'N/A' }}
                                    </span>
                                    <span class="text-muted small" style="font-size: 11px;">
                                        Limit: {{ $activeTab === 'paid' ? ($settings['paid_incomplete_expiry_value'] . ' ' . $settings['paid_incomplete_expiry_unit']) : ($settings['draft_expiry_value'] . ' ' . $settings['draft_expiry_unit']) }}
                                    </span>
                                </div>
                            </td>

                            <!-- Lifecycle Status Badge -->
                            <td>
                                @if($isExpired)
                                    <span class="badge bg-danger text-white px-2.5 py-1.5 rounded-pill d-inline-flex align-items-center gap-1 shadow-sm">
                                        <i class="bi bi-exclamation-octagon-fill"></i>
                                        <span>Expired ({{ abs($daysRemaining) }}d overdue)</span>
                                    </span>
                                @elseif($badge === 'expiring_soon')
                                    <span class="badge bg-warning text-dark px-2.5 py-1.5 rounded-pill d-inline-flex align-items-center gap-1 shadow-sm">
                                        <i class="bi bi-hourglass-split"></i>
                                        <span>Expires in {{ $daysRemaining }} day{{ $daysRemaining == 1 ? '' : 's' }}</span>
                                    </span>
                                @else
                                    <span class="badge bg-success-subtle text-success border border-success px-2.5 py-1.5 rounded-pill d-inline-flex align-items-center gap-1">
                                        <i class="bi bi-check-circle"></i>
                                        <span>Active ({{ $daysRemaining }}d remaining)</span>
                                    </span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="text-end pe-4">
                                <div class="d-inline-flex align-items-center gap-1">
                                    <a href="{{ route('admin.work-queue.process', $app->id) }}" class="btn btn-outline-secondary btn-sm rounded-circle p-1.5" title="Inspect Application Details" style="width: 32px; height: 32px;">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-danger btn-sm rounded-circle p-1.5" title="Delete Now" style="width: 32px; height: 32px;" data-bs-toggle="modal" data-bs-target="#deleteAppModal{{ $app->id }}">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </div>

                                <!-- Single Delete Modal -->
                                <div class="modal fade text-start" id="deleteAppModal{{ $app->id }}" tabindex="-1" aria-labelledby="deleteAppModalLabel{{ $app->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow rounded-4 overflow-hidden">
                                            <div class="modal-header bg-danger text-white">
                                                <h5 class="modal-title fs-6 fw-bold" id="deleteAppModalLabel{{ $app->id }}">
                                                    <i class="bi bi-trash3-fill me-1"></i> Delete Application {{ $app->reference_number }}
                                                </h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body p-4">
                                                <p class="text-dark mb-2">
                                                    Are you sure you want to permanently delete this application?
                                                </p>
                                                <div class="p-3 bg-light rounded-3 mb-3 small">
                                                    <div><strong>Reference:</strong> {{ $app->reference_number }}</div>
                                                    <div><strong>Client:</strong> {{ $app->client_name ?: ($app->client ? $app->client->name : 'N/A') }}</div>
                                                    <div><strong>Service:</strong> {{ $app->service_name }}</div>
                                                    <div><strong>Days Inactive:</strong> {{ $daysInactive }} days</div>
                                                    <div><strong>Status:</strong> {{ $isExpired ? 'Expired' : 'Active' }}</div>
                                                </div>
                                                <div class="alert alert-warning small mb-0 d-flex align-items-center gap-2">
                                                    <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                                                    <div>
                                                        This action will permanently delete all associated uploaded files (documents, passport photos, forms) and database records. This cannot be undone.
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer bg-light border-0">
                                                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                                                <form action="{{ route('admin.expirations.delete', $app->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm rounded-pill px-3">
                                                        <i class="bi bi-trash3-fill me-1"></i> Confirm Permanent Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="py-4">
                                    <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center mb-3 text-secondary" style="width: 64px; height: 64px; font-size: 28px;">
                                        <i class="bi bi-check2-all text-success"></i>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1">No Applications Match Filter</h6>
                                    <p class="text-secondary small mb-3">
                                        @if($statusFilter === 'expired')
                                            There are no expired applications currently pending purge in this category.
                                        @else
                                            No incomplete applications found matching your criteria.
                                        @endif
                                    </p>
                                    @if(!empty($search) || $statusFilter !== 'all')
                                        <a href="{{ route('admin.expirations', ['tab' => $activeTab]) }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                                            Clear Filters
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($applications->hasPages())
            <div class="p-3 border-top bg-light d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="text-muted small">
                    Showing {{ $applications->firstItem() }} to {{ $applications->lastItem() }} of {{ $applications->total() }} applications
                </div>
                <div>
                    {{ $applications->links() }}
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Batch Purge Modal -->
<div class="modal fade" id="batchPurgeModal" tabindex="-1" aria-labelledby="batchPurgeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4 overflow-hidden">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fs-6 fw-bold" id="batchPurgeModalLabel">
                    <i class="bi bi-shield-exclamation me-1"></i> Confirm System Expiration Purge
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p class="text-dark mb-3">
                    You are about to execute a batch cleanup of all expired applications across the system.
                </p>
                <div class="p-3 bg-light rounded-3 mb-3 small">
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span>Expired Unfinished Drafts (&ge; {{ $settings['draft_expiry_value'] }} {{ $settings['draft_expiry_unit'] }}):</span>
                        <strong class="text-danger font-monospace">{{ $stats['expired_drafts'] }}</strong>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span>Expired Paid Incomplete (&ge; {{ $settings['paid_incomplete_expiry_value'] }} {{ $settings['paid_incomplete_expiry_unit'] }}):</span>
                        <strong class="text-danger font-monospace">{{ $stats['expired_paid_incomplete'] }}</strong>
                    </div>
                    <div class="d-flex justify-content-between py-1 pt-2">
                        <span>Total Records to Purge:</span>
                        <strong class="text-danger font-monospace fs-6">{{ $stats['total_expired'] }}</strong>
                    </div>
                </div>
                <div class="alert alert-danger small mb-0 d-flex align-items-center gap-2">
                    <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                    <div>
                        <strong>Notice:</strong> This action will remove the eligible application records and physically erase all related stored uploads from the server to free up space.
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light border-0">
                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('admin.expirations.purge') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-sm rounded-pill px-4 shadow-sm">
                        <i class="bi bi-trash3-fill me-1"></i> Proceed & Purge All Expired ({{ $stats['total_expired'] }})
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
