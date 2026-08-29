@extends('layouts.dashboard')

@section('title', 'Request Details - ' . ($settings->platform_name ?? 'Umar Maher'))

@section('content')
<div class="mb-4">
    <a href="{{ route('client.requests') }}" class="btn btn-outline-secondary btn-sm rounded-pill mb-2"><i class="bi bi-arrow-left me-1"></i> Back to Requests</a>
    <h1 class="h3 fw-bold text-dark mb-1">Request Details</h1>
    <p class="text-secondary small">Tracking ID: #{{ $request->id }}</p>
</div>

<div class="row g-4">
    <!-- Left Column: Details -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm p-4 rounded-4 bg-white mb-4">
            <h2 class="h5 fw-bold text-dark mb-3">Service Information</h2>
            <div class="row g-3 small mb-4">
                <div class="col-sm-6">
                    <span class="text-muted d-block">Service Name</span>
                    <span class="fw-semibold text-dark fs-6">{{ $request->service_name }}</span>
                </div>
                <div class="col-sm-6">
                    <span class="text-muted d-block">Amount Charged</span>
                    <span class="fw-semibold text-dark fs-6">{{ $settings->default_currency ?? 'USD' }} {{ number_format($request->price, 2) }}</span>
                </div>
                <div class="col-sm-6">
                    <span class="text-muted d-block">Vendor Agent</span>
                    <span class="fw-semibold text-dark fs-6">{{ $request->vendor_name }}</span>
                </div>
                <div class="col-sm-6">
                    <span class="text-muted d-block">Order Date</span>
                    <span class="fw-semibold text-dark fs-6">{{ $request->created_at->format('M d, Y, h:i A') }}</span>
                </div>
            </div>

            <hr class="my-4 border-light">

            <h2 class="h5 fw-bold text-dark mb-3">Submitted Files / Documents</h2>
            @if(is_array($request->documents) && count($request->documents) > 0)
                <div class="list-group list-group-flush">
                    @foreach($request->documents as $doc)
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2.5">
                            <span class="small fw-semibold text-dark"><i class="bi bi-file-earmark-text me-2 text-primary"></i> {{ $doc['name'] ?? 'Uploaded Document' }}</span>
                            <a href="{{ $doc['url'] }}" target="_blank" class="btn btn-light btn-sm rounded-pill small px-3">View File</a>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-light p-3 rounded text-center small text-secondary">
                    <i class="bi bi-info-circle me-1"></i> No files uploaded for this request.
                </div>
            @endif
        </div>
    </div>

    <!-- Right Column: Status and Mock Payment -->
    <div class="col-lg-4">
        <!-- Status Card -->
        <div class="card border-0 shadow-sm p-4 rounded-4 bg-white mb-4">
            <h3 class="h6 fw-bold text-dark mb-3">Processing Status</h3>
            @php
                $statusBadge = match($request->status) {
                    'Awaiting Payment' => 'bg-warning text-dark',
                    'Pending Documents' => 'bg-info text-white',
                    'Under Review' => 'bg-primary text-white',
                    'In Progress' => 'bg-secondary text-white',
                    'Completed' => 'bg-success text-white',
                    'Rejected' => 'bg-danger text-white',
                    default => 'bg-light text-dark'
                };
            @endphp
            <div class="d-flex align-items-center gap-2 mb-3">
                <span class="badge {{ $statusBadge }} rounded-pill px-3 py-2 fs-6 w-100 text-center">{{ $request->status }}</span>
            </div>
            
            <div class="small">
                <div class="d-flex justify-content-between py-1.5 border-bottom border-light">
                    <span class="text-muted">Payment status:</span>
                    <span class="fw-semibold text-dark">{{ $request->payment_status }}</span>
                </div>
                @if($request->payment_status === 'Paid')
                    <div class="d-flex justify-content-between py-1.5 border-bottom border-light">
                        <span class="text-muted">Gateway:</span>
                        <span class="fw-semibold text-dark text-uppercase">{{ $request->payment_gateway }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-1.5">
                        <span class="text-muted">Reference:</span>
                        <span class="fw-semibold text-dark text-truncate" style="max-width: 150px;">{{ $request->payment_reference }}</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Mock Payment Form (Only shown if unpaid) -->
        @if($request->payment_status === 'Unpaid')
            <div class="card border-0 bg-dark text-white p-4 rounded-4 shadow-sm">
                <h3 class="h6 fw-bold text-white mb-3"><i class="bi bi-wallet2 me-2"></i> Paystack / Credo Checkout</h3>
                <p class="small text-white-50 mb-4">Select a payment gateway to simulate a checkout transaction.</p>

                <form action="{{ route('client.request.payment', $request->id) }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label small text-white-50">Select Gateway</label>
                        <select name="payment_gateway" class="form-select bg-secondary text-white border-0 small" required>
                            <option value="paystack">Paystack Gateway</option>
                            <option value="credo">Credo Gateway</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small text-white-50">Mock Transaction Reference</label>
                        <input type="text" name="payment_reference" class="form-control bg-secondary text-white border-0 small" value="MOCK_REF_{{ strtoupper(Str::random(10)) }}" readonly required>
                    </div>

                    <button type="submit" class="btn btn-light w-100 rounded-pill fw-semibold py-2">Submit Mock Payment</button>
                </form>
            </div>
        @endif
    </div>
</div>
@endsection
