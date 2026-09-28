@extends('layouts.dashboard')

@section('title', 'Reports & System Records - ' . ($settings->platform_name ?? 'DOOTOR ENTERPRISES'))

@section('content')
<!-- Page Header -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h1 class="h3 fw-bold text-dark mb-1"><i class="bi bi-file-earmark-bar-graph me-2 text-success"></i>Reports & System Records</h1>
        <p class="text-secondary small mb-0">Generate, filter, analyze, and export complete administrative, client, service, and financial records.</p>
    </div>
</div>

<!-- Main Filter & Report Type Selector Form -->
<div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
    <div class="card-body p-4">
        <form method="GET" action="{{ route('admin.reports') }}" id="reportFilterForm">
            <div class="row g-3 align-items-end">
                <!-- 1. Report Type Selector -->
                <div class="col-md-4">
                    <label for="report_type" class="form-label small fw-bold text-dark mb-1"><i class="bi bi-journal-album me-1 text-primary"></i> 1. Record / Report Type</label>
                    <select name="report_type" id="report_type" class="form-select rounded-3 border-dark-subtle fw-semibold" onchange="this.form.submit()">
                        @php
                            $groupedTypes = [];
                            foreach ($availableReportTypes as $code => $info) {
                                $cat = $info['category'] ?? 'General';
                                $groupedTypes[$cat][$code] = $info;
                            }
                        @endphp
                        @foreach($groupedTypes as $category => $items)
                            <optgroup label="{{ $category }}">
                                @foreach($items as $code => $info)
                                    <option value="{{ $code }}" {{ $reportType === $code ? 'selected' : '' }}>
                                        {{ $info['label'] }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>

                <!-- 2. Date Range Preset -->
                <div class="col-md-3">
                    <label for="date_preset" class="form-label small fw-bold text-dark mb-1"><i class="bi bi-calendar3 me-1 text-primary"></i> 2. Date Range</label>
                    <select name="date_preset" id="date_preset" class="form-select rounded-3" onchange="toggleCustomDateFields()">
                        <option value="all_time" {{ request('date_preset') === 'all_time' ? 'selected' : '' }}>All Time</option>
                        <option value="today" {{ request('date_preset') === 'today' ? 'selected' : '' }}>Today</option>
                        <option value="yesterday" {{ request('date_preset') === 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                        <option value="last_7_days" {{ request('date_preset') === 'last_7_days' ? 'selected' : '' }}>Last 7 Days</option>
                        <option value="last_30_days" {{ request('date_preset') === 'last_30_days' ? 'selected' : '' }}>Last 30 Days</option>
                        <option value="this_month" {{ request('date_preset') === 'this_month' ? 'selected' : '' }}>This Month</option>
                        <option value="last_month" {{ request('date_preset') === 'last_month' ? 'selected' : '' }}>Last Month</option>
                        <option value="this_year" {{ request('date_preset') === 'this_year' ? 'selected' : '' }}>This Year</option>
                        <option value="custom" {{ request('date_preset') === 'custom' || request('date_from') || request('date_to') ? 'selected' : '' }}>Custom Date Range...</option>
                    </select>
                </div>

                <!-- 3. Custom Date From / Date To -->
                <div class="col-md-2 custom-date-wrapper" style="{{ request('date_preset') === 'custom' || request('date_from') || request('date_to') ? 'display: block;' : 'display: none;' }}">
                    <label for="date_from" class="form-label small fw-bold text-dark mb-1">From Date</label>
                    <input type="date" name="date_from" id="date_from" class="form-control rounded-3" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2 custom-date-wrapper" style="{{ request('date_preset') === 'custom' || request('date_from') || request('date_to') ? 'display: block;' : 'display: none;' }}">
                    <label for="date_to" class="form-label small fw-bold text-dark mb-1">To Date</label>
                    <input type="date" name="date_to" id="date_to" class="form-control rounded-3" value="{{ request('date_to') }}">
                </div>

                <!-- 4. Status Filter -->
                <div class="col-md-3">
                    <label for="status" class="form-label small fw-bold text-dark mb-1"><i class="bi bi-funnel me-1 text-primary"></i> Status</label>
                    <select name="status" id="status" class="form-select rounded-3">
                        <option value="">All Statuses</option>
                        @if(in_array($reportType, ['client_records', 'staff_records', 'service_records']))
                            <option value="Active" {{ request('status') === 'Active' ? 'selected' : '' }}>Active</option>
                            <option value="Inactive" {{ request('status') === 'Inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="Suspended" {{ request('status') === 'Suspended' ? 'selected' : '' }}>Suspended</option>
                        @else
                            <option value="Draft" {{ request('status') === 'Draft' ? 'selected' : '' }}>Draft</option>
                            <option value="Submitted" {{ request('status') === 'Submitted' ? 'selected' : '' }}>Submitted</option>
                            <option value="Awaiting Payment" {{ request('status') === 'Awaiting Payment' ? 'selected' : '' }}>Awaiting Payment</option>
                            <option value="Payment Confirmed" {{ request('status') === 'Payment Confirmed' ? 'selected' : '' }}>Payment Confirmed</option>
                            <option value="Documents Under Review" {{ request('status') === 'Documents Under Review' ? 'selected' : '' }}>Documents Under Review</option>
                            <option value="Processing" {{ request('status') === 'Processing' ? 'selected' : '' }}>Processing</option>
                            <option value="Completed" {{ request('status') === 'Completed' ? 'selected' : '' }}>Completed</option>
                            <option value="Rejected" {{ request('status') === 'Rejected' ? 'selected' : '' }}>Rejected</option>
                            <option value="Cancelled" {{ request('status') === 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                        @endif
                    </select>
                </div>

                <!-- 5. Service Filter (Conditional) -->
                @if(in_array($reportType, ['all', 'applications', 'submitted_applications', 'completed_applications', 'payment_records', 'document_records']))
                <div class="col-md-3">
                    <label for="service_id" class="form-label small fw-bold text-dark mb-1"><i class="bi bi-briefcase me-1 text-primary"></i> Service</label>
                    <select name="service_id" id="service_id" class="form-select rounded-3">
                        <option value="">All Services</option>
                        @foreach($services as $s)
                            <option value="{{ $s->id }}" {{ (string)request('service_id') === (string)$s->id ? 'selected' : '' }}>
                                {{ $s->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif

                <!-- 6. Staff Filter (Conditional) -->
                @if(in_array($reportType, ['all', 'applications', 'assigned_applications', 'staff_activity_records', 'assignment_records']))
                <div class="col-md-3">
                    <label for="staff_id" class="form-label small fw-bold text-dark mb-1"><i class="bi bi-person-gear me-1 text-primary"></i> Staff / Officer</label>
                    <select name="staff_id" id="staff_id" class="form-select rounded-3">
                        <option value="">All Staff Members</option>
                        @foreach($staffMembers as $st)
                            <option value="{{ $st->id }}" {{ (string)request('staff_id') === (string)$st->id ? 'selected' : '' }}>
                                {{ $st->first_name }} {{ $st->last_name }} ({{ $st->staff_file_number ?? 'STF-' . $st->id }})
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif

                <!-- 7. Search Input -->
                <div class="col-md-4">
                    <label for="search" class="form-label small fw-bold text-dark mb-1"><i class="bi bi-search me-1 text-primary"></i> Search Records</label>
                    <input type="text" name="search" id="search" class="form-control rounded-3" value="{{ request('search') }}" placeholder="Search ref, name, email, phone...">
                </div>

                <!-- 8. Per Page Pagination -->
                <div class="col-md-2">
                    <label for="per_page" class="form-label small fw-bold text-dark mb-1">Per Page</label>
                    <select name="per_page" id="per_page" class="form-select rounded-3">
                        <option value="25" {{ (int)request('per_page', 25) === 25 ? 'selected' : '' }}>25 Records</option>
                        <option value="50" {{ (int)request('per_page') === 50 ? 'selected' : '' }}>50 Records</option>
                        <option value="100" {{ (int)request('per_page') === 100 ? 'selected' : '' }}>100 Records</option>
                        <option value="250" {{ (int)request('per_page') === 250 ? 'selected' : '' }}>250 Records</option>
                    </select>
                </div>

                <!-- Action Buttons -->
                <div class="col-md-3 d-flex gap-2 align-self-end">
                    <button type="submit" class="btn btn-dark rounded-pill px-4 py-2 fw-semibold w-100 shadow-sm" style="background-color: #004225; border-color: #004225;">
                        <i class="bi bi-filter me-1"></i> Apply Filters
                    </button>
                    <a href="{{ route('admin.reports') }}" class="btn btn-light border rounded-pill px-3 py-2 fw-semibold text-secondary">
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Active Filter Display Chips -->
@php
    $activeChips = [];
    if ($reportType && $reportType !== 'all') $activeChips[] = ['label' => 'Report: ' . ($activeReportInfo['label'] ?? $reportType), 'key' => 'report_type'];
    if (request('date_preset') && request('date_preset') !== 'all_time') $activeChips[] = ['label' => 'Period: ' . ucfirst(str_replace('_', ' ', request('date_preset'))), 'key' => 'date_preset'];
    if (request('status')) $activeChips[] = ['label' => 'Status: ' . request('status'), 'key' => 'status'];
    if (request('service_id')) {
        $foundService = $services->firstWhere('id', request('service_id'));
        if ($foundService) $activeChips[] = ['label' => 'Service: ' . $foundService->name, 'key' => 'service_id'];
    }
    if (request('search')) $activeChips[] = ['label' => 'Search: "' . request('search') . '"', 'key' => 'search'];
@endphp

@if(count($activeChips) > 0)
    <div class="mb-4 d-flex align-items-center gap-2 flex-wrap">
        <span class="small fw-bold text-muted me-1"><i class="bi bi-funnel-fill text-success"></i> Active Filters:</span>
        @foreach($activeChips as $chip)
            <span class="badge bg-white text-dark border shadow-sm px-3 py-2 rounded-pill font-monospace small fw-medium">
                {{ $chip['label'] }}
            </span>
        @endforeach
        <a href="{{ route('admin.reports') }}" class="small text-danger ms-2 text-decoration-none fw-semibold"><i class="bi bi-x-circle"></i> Clear All</a>
    </div>
@endif

<!-- Summary Cards Bar -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm p-3 h-100 rounded-4 bg-white border-start border-4 border-success">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="small text-muted fw-bold text-uppercase">{{ $metrics['label1'] ?? 'Matching Records' }}</span>
                    <h3 class="fw-bold my-1 text-dark">{{ number_format($metrics['total'] ?? 0) }}</h3>
                    <small class="text-secondary">Filtered Results</small>
                </div>
                <div class="rounded-circle p-3 bg-success bg-opacity-10 text-success">
                    <i class="bi bi-list-check fs-3"></i>
                </div>
            </div>
        </div>
    </div>

    @if(isset($metrics['revenue']))
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm p-3 h-100 rounded-4 text-white" style="background: linear-gradient(135deg, #004225 0%, #002e1a 100%);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="small text-white-50 fw-bold text-uppercase">{{ $metrics['label2'] ?? 'Revenue Collected' }}</span>
                    <h3 class="fw-bold my-1">${{ number_format($metrics['revenue'], 2) }}</h3>
                    <small class="text-white-50">Total Paid Amount</small>
                </div>
                <div class="rounded-circle p-3" style="background: rgba(255,255,255,0.15);">
                    <i class="bi bi-currency-dollar fs-3"></i>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if(isset($metrics['outstanding']))
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm p-3 h-100 rounded-4 bg-white border-start border-4 border-danger">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="small text-muted fw-bold text-uppercase">{{ $metrics['label3'] ?? 'Outstanding' }}</span>
                    <h3 class="fw-bold my-1 text-danger">${{ number_format($metrics['outstanding'], 2) }}</h3>
                    <small class="text-secondary">Pending Collection</small>
                </div>
                <div class="rounded-circle p-3 bg-danger bg-opacity-10 text-danger">
                    <i class="bi bi-clock-history fs-3"></i>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if(isset($metrics['completed']))
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm p-3 h-100 rounded-4 bg-white border-start border-4 border-info">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="small text-muted fw-bold text-uppercase">{{ $metrics['label4'] ?? 'Completed' }}</span>
                    <h3 class="fw-bold my-1 text-info">{{ number_format($metrics['completed']) }}</h3>
                    <small class="text-secondary">Fully Processed</small>
                </div>
                <div class="rounded-circle p-3 bg-info bg-opacity-10 text-info">
                    <i class="bi bi-check2-circle fs-3"></i>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Records Table & Export Bar Header -->
<div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
    <div class="card-header bg-white border-0 pt-4 px-4 pb-2 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <div>
            <h2 class="h5 fw-bold text-dark mb-0">{{ $activeReportInfo['label'] }} Ledger</h2>
            <p class="text-muted small mb-0">Total Matching Records: <strong class="text-success">{{ number_format($records->total()) }}</strong></p>
        </div>

        <!-- Export Toolbar Component -->
        <div class="d-flex align-items-center gap-2">
            <form method="GET" action="{{ route('admin.reports.export') }}" target="_blank" class="d-flex align-items-center gap-2" id="exportForm">
                <!-- Forward all active query filters to export request -->
                <input type="hidden" name="report_type" value="{{ $reportType }}">
                <input type="hidden" name="date_preset" value="{{ request('date_preset') }}">
                <input type="hidden" name="date_from" value="{{ request('date_from') }}">
                <input type="hidden" name="date_to" value="{{ request('date_to') }}">
                <input type="hidden" name="status" value="{{ request('status') }}">
                <input type="hidden" name="service_id" value="{{ request('service_id') }}">
                <input type="hidden" name="staff_id" value="{{ request('staff_id') }}">
                <input type="hidden" name="payment_status" value="{{ request('payment_status') }}">
                <input type="hidden" name="country" value="{{ request('country') }}">
                <input type="hidden" name="search" value="{{ request('search') }}">

                <label for="export_format" class="small fw-bold text-dark text-nowrap mb-0"><i class="bi bi-download me-1 text-success"></i> Export Format:</label>
                <select id="export_format" class="form-select form-select-sm rounded-3 fw-semibold" style="width: 100px;">
                    <option value="csv">CSV</option>
                    <option value="pdf">PDF</option>
                </select>

                <button type="button" class="btn btn-sm btn-dark rounded-pill px-4 py-2 fw-semibold shadow-sm text-nowrap" style="background-color: #004225; border-color: #004225;" onclick="triggerReportExport()" {{ $records->total() === 0 ? 'disabled' : '' }}>
                    <i class="bi bi-file-earmark-arrow-down me-1"></i> Export Report
                </button>
            </form>
        </div>
    </div>

    <!-- Table Container -->
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 small">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">#</th>
                        @php
                            $headers = \App\Services\ReportExportService::getColumnHeaders($reportType);
                        @endphp
                        @foreach($headers as $h)
                            <th>{{ $h }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $index => $rec)
                        @php
                            $row = \App\Services\ReportExportService::formatRecordRow($reportType, $rec);
                        @endphp
                        <tr>
                            <td class="ps-4 font-monospace text-muted">{{ $records->firstItem() + $index }}</td>
                            @foreach($row as $cIdx => $cell)
                                <td>
                                    @if(str_contains($cell, 'DE-') || str_contains($cell, 'CL-') || str_contains($cell, 'STF-') || str_contains($cell, 'AUD-') || str_contains($cell, 'ASG-') || str_contains($cell, 'HST-') || str_contains($cell, 'LOG-'))
                                        <span class="font-monospace fw-bold text-primary">{{ $cell }}</span>
                                    @elseif(in_array($cell, ['Active', 'Completed', 'Sent', 'Paid', 'Payment Confirmed']))
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1 fw-bold">{{ $cell }}</span>
                                    @elseif(in_array($cell, ['Draft', 'Awaiting Payment', 'Uploaded', 'Pending', 'In Review']))
                                        <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3 py-1 fw-bold">{{ $cell }}</span>
                                    @elseif(in_array($cell, ['Rejected', 'Cancelled', 'Failed', 'Suspended', 'Unpaid']))
                                        <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-1 fw-bold">{{ $cell }}</span>
                                    @else
                                        {{ $cell }}
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($headers) + 1 }}" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block text-secondary mb-2"></i>
                                <strong class="d-block text-dark">No records found</strong>
                                <span class="small">No system records match your selected report filters. Try expanding your date range or clearing search criteria.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination Footer -->
    <div class="card-footer bg-white border-0 py-3 px-4 d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
        <div class="small text-muted">
            Showing <strong class="text-dark">{{ $records->firstItem() ?? 0 }}</strong> to <strong class="text-dark">{{ $records->lastItem() ?? 0 }}</strong> of <strong class="text-dark">{{ number_format($records->total()) }}</strong> matching records
        </div>
        <div>
            {{ $records->links() }}
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function toggleCustomDateFields() {
        var preset = document.getElementById('date_preset').value;
        var wrappers = document.querySelectorAll('.custom-date-wrapper');
        wrappers.forEach(function(el) {
            el.style.display = (preset === 'custom') ? 'block' : 'none';
        });
    }

    function triggerReportExport() {
        var format = document.getElementById('export_format').value;
        var form = document.getElementById('exportForm');
        
        if (format === 'pdf') {
            form.action = "{{ route('admin.reports.export-pdf') }}";
        } else {
            form.action = "{{ route('admin.reports.export') }}";
        }

        form.submit();
    }
</script>
@endsection
