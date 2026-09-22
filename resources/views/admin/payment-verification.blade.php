@extends('layouts.dashboard')

@section('title', 'Credo Direct Payment Verification - Dootor Enterprises')

@section('content')
<div class="container-fluid p-0">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
        <div>
            <h3 class="fw-bold text-dark mb-1">
                <i class="bi bi-credit-card-2-front text-success me-2"></i> Credo Payment Verification & Re-query
            </h3>
            <p class="text-muted small mb-0">Query Credo Central API directly to verify client payments and automatically update database records for unconfirmed or delayed transactions.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm border-0 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm border-0 mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show rounded-3 shadow-sm border-0 mb-4" role="alert">
            <i class="bi bi-info-circle-fill me-2"></i> {{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Direct Credo API Query Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
        <div class="card-body p-4">
            <h5 class="fw-bold text-dark mb-2">
                <i class="bi bi-cpu text-primary me-2"></i> Direct Credo API Re-query Search
            </h5>
            <p class="text-muted small mb-3">Enter an Application Reference (e.g. <code>DOOTOR-BVRE-2026-113544</code>) or Payment Reference to check payment servers directly.</p>
            
            <form action="{{ route('admin.payment-verification.query') }}" method="POST" class="row g-3 align-items-center">
                @csrf
                <div class="col-md-9">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" name="reference" class="form-control bg-light border-start-0 py-2" placeholder="Paste Reference Number or Credo Payment Reference..." required>
                    </div>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn text-white fw-semibold w-100 py-2" style="background-color: #004225; border-radius: 8px;">
                        <i class="bi bi-arrow-repeat me-1"></i> Query Credo API Now
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Filter & Table Card -->
    <div class="card border-0 shadow-sm rounded-4 bg-white">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
                <h5 class="fw-bold text-dark mb-0">Service Applications Payment Log</h5>

                <form action="{{ route('admin.payment-verification') }}" method="GET" class="d-flex gap-2">
                    <select name="status_filter" class="form-select form-select-sm rounded-pill" onchange="this.form.submit()" style="width: 160px;">
                        <option value="">All Payments</option>
                        <option value="unpaid" {{ $statusFilter === 'unpaid' ? 'selected' : '' }}>Unpaid / Pending</option>
                        <option value="paid" {{ $statusFilter === 'paid' ? 'selected' : '' }}>Paid Only</option>
                    </select>

                    <div class="input-group input-group-sm" style="width: 240px;">
                        <input type="text" name="search" class="form-control rounded-start-pill" placeholder="Search reference..." value="{{ $search }}">
                        <button class="btn btn-outline-secondary rounded-end-pill" type="submit"><i class="bi bi-search"></i></button>
                    </div>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr class="text-uppercase text-secondary small fw-bold">
                            <th>Tracking Ref</th>
                            <th>Applicant</th>
                            <th>Service</th>
                            <th>Amount</th>
                            <th>Gateway / Ref</th>
                            <th>Payment Status</th>
                            <th>Application Status</th>
                            <th class="text-end">Credo Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $req)
                            <tr>
                                <td>
                                    <span class="fw-bold text-dark font-monospace" style="font-size: 13px;">{{ $req->reference_number }}</span>
                                    <span class="d-block text-muted" style="font-size: 10.5px;">{{ $req->created_at->format('M d, Y h:i A') }}</span>
                                </td>
                                <td>
                                    <span class="fw-semibold text-dark d-block">{{ $req->client_name }}</span>
                                    <span class="text-secondary small">{{ $req->client_email }}</span>
                                </td>
                                <td>
                                    <span class="fw-semibold text-dark">{{ $req->service_name }}</span>
                                </td>
                                <td>
                                    <span class="fw-bold text-dark">${{ number_format($req->price, 2) }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border me-1">{{ $req->payment_gateway ?? 'Credo' }}</span>
                                    <span class="d-block text-muted font-monospace" style="font-size: 10.5px;">{{ $req->payment_reference ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    @if($req->payment_status === 'Paid')
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill">
                                            <i class="bi bi-check-circle me-1"></i> Paid
                                        </span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-1 rounded-pill">
                                            <i class="bi bi-hourglass-split me-1"></i> {{ $req->payment_status ?? 'Unpaid' }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-dark text-white px-2 py-1 rounded-pill small">{{ $req->status }}</span>
                                </td>
                                <td class="text-end">
                                    <form action="{{ route('admin.payment-verification.query') }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="reference" value="{{ $req->reference_number }}">
                                        <button type="submit" class="btn btn-outline-dark btn-sm rounded-pill px-3" title="Check Credo API directly for this payment">
                                            <i class="bi bi-arrow-repeat me-1"></i> Re-query Credo
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary"></i>
                                    No service applications found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $requests->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
