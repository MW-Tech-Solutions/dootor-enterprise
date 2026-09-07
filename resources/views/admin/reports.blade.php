@extends('layouts.dashboard')

@section('title', 'Reports & Analytics')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="color: #0f172a;">Financial & Business Analytics</h4>
        <p class="text-muted small mb-0">Overview of service applications, revenue collected, and outstanding balances.</p>
    </div>
    <div>
        <a href="{{ route('admin.reports.export') }}" class="btn text-white fw-semibold px-4" style="background-color: #004225; border-radius: 8px;">
            <i class="bi bi-download me-2"></i> Export to CSV
        </a>
    </div>
</div>

<!-- Key Performance Metrics Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3 h-100" style="border-radius: 12px; background: linear-gradient(135deg, #004225, #006637); color: white;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="small text-white-50 fw-semibold text-uppercase">Total Revenue</span>
                    <h3 class="fw-bold my-1">${{ number_format($totalRevenue, 2) }}</h3>
                    <small class="text-white-50">Payments Received</small>
                </div>
                <div class="rounded-circle p-3" style="background: rgba(255,255,255,0.15);">
                    <i class="bi bi-currency-dollar fs-3"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3 h-100" style="border-radius: 12px; background: #ffffff;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="small text-muted fw-semibold text-uppercase">Outstanding Balance</span>
                    <h3 class="fw-bold my-1 text-danger">${{ number_format($totalOutstanding, 2) }}</h3>
                    <small class="text-muted">Pending Collections</small>
                </div>
                <div class="rounded-circle p-3 bg-danger bg-opacity-10 text-danger">
                    <i class="bi bi-clock-history fs-3"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3 h-100" style="border-radius: 12px; background: #ffffff;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="small text-muted fw-semibold text-uppercase">Total Applications</span>
                    <h3 class="fw-bold my-1 text-dark">{{ number_format($totalApplications) }}</h3>
                    <small class="text-muted">All-time bookings</small>
                </div>
                <div class="rounded-circle p-3 bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-journal-text fs-3"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3 h-100" style="border-radius: 12px; background: #ffffff;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="small text-muted fw-semibold text-uppercase">Completed Services</span>
                    <h3 class="fw-bold my-1 text-success">{{ number_format($completedApplications) }}</h3>
                    <small class="text-muted">Fully Processed</small>
                </div>
                <div class="rounded-circle p-3 bg-success bg-opacity-10 text-success">
                    <i class="bi bi-check-circle fs-3"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('admin.reports') }}" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted">Start Date</label>
                <input type="date" name="start_date" class="form-control form-control-sm" value="{{ request('start_date') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted">End Date</label>
                <input type="date" name="end_date" class="form-control form-control-sm" value="{{ request('end_date') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted">Application Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Statuses</option>
                    <option value="Awaiting Payment" {{ request('status') === 'Awaiting Payment' ? 'selected' : '' }}>Awaiting Payment</option>
                    <option value="Payment Confirmed" {{ request('status') === 'Payment Confirmed' ? 'selected' : '' }}>Payment Confirmed</option>
                    <option value="Documents Under Review" {{ request('status') === 'Documents Under Review' ? 'selected' : '' }}>Documents Under Review</option>
                    <option value="Processing" {{ request('status') === 'Processing' ? 'selected' : '' }}>Processing</option>
                    <option value="Completed" {{ request('status') === 'Completed' ? 'selected' : '' }}>Completed</option>
                    <option value="Cancelled" {{ request('status') === 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-sm text-white w-100 fw-semibold" style="background-color: #004225;">Filter</button>
                <a href="{{ route('admin.reports') }}" class="btn btn-sm btn-light border w-100">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="row g-4">
    <!-- Revenue Breakdown by Service -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
            <div class="card-header bg-white border-0 pt-3 px-3">
                <h6 class="fw-bold mb-0" style="color: #0f172a;">Revenue Breakdown by Service</h6>
            </div>
            <div class="card-body p-3">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 small">
                        <thead class="table-light">
                            <tr>
                                <th>Service Name</th>
                                <th class="text-center">Count</th>
                                <th class="text-end">Revenue ($)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($servicesBreakdown as $sb)
                            <tr>
                                <td class="fw-semibold text-dark">{{ $sb->service_name }}</td>
                                <td class="text-center"><span class="badge bg-secondary bg-opacity-10 text-secondary">{{ $sb->total }}</span></td>
                                <td class="text-end fw-bold text-success">${{ number_format($sb->revenue, 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-3">No revenue data recorded yet.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Financial Transactions -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
            <div class="card-header bg-white border-0 pt-3 px-3">
                <h6 class="fw-bold mb-0" style="color: #0f172a;">Detailed Application Ledger</h6>
            </div>
            <div class="card-body p-3">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 small">
                        <thead class="table-light">
                            <tr>
                                <th>Ref Number</th>
                                <th>Client</th>
                                <th>Service</th>
                                <th>Paid</th>
                                <th>Balance</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($requests as $req)
                            <tr>
                                <td><span class="font-monospace fw-bold text-primary">{{ $req->reference_number ?? ('DE-' . $req->id) }}</span></td>
                                <td>
                                    <div class="fw-semibold">{{ $req->client_name }}</div>
                                    <small class="text-muted">{{ $req->client_email }}</small>
                                </td>
                                <td>{{ $req->service_name }}</td>
                                <td class="fw-bold text-success">${{ number_format($req->amount_paid, 2) }}</td>
                                <td class="fw-bold {{ $req->outstanding_balance > 0 ? 'text-danger' : 'text-muted' }}">
                                    ${{ number_format($req->outstanding_balance, 2) }}
                                </td>
                                <td>
                                    <span class="badge bg-{{ $req->status === 'Completed' ? 'success' : ($req->status === 'Awaiting Payment' ? 'warning' : 'info') }} bg-opacity-10 text-{{ $req->status === 'Completed' ? 'success' : ($req->status === 'Awaiting Payment' ? 'warning' : 'info') }}">
                                        {{ $req->status }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-3">No application records found matching filter.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
