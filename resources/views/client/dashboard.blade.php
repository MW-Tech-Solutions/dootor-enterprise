@extends('layouts.dashboard')

@section('title', 'Client Dashboard - ' . ($settings->platform_name ?? 'Umar Maher'))

@section('content')
<div class="mb-4">
    <h1 class="h3 fw-bold text-dark mb-1">Hello, {{ Auth::user()->first_name }}!</h1>
    <p class="text-secondary small">Track your document requests and explore verified providers</p>
</div>

<!-- Stats row -->
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
            <span class="text-muted d-block small mb-1">Total Requests</span>
            <span class="fw-bold display-6">{{ $total_requests_count }}</span>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
            <span class="text-muted d-block small mb-1">Processing (Paid)</span>
            <span class="fw-bold display-6 text-success">{{ $paid_requests_count }}</span>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
            <span class="text-muted d-block small mb-1">Awaiting Payment</span>
            <span class="fw-bold display-6 text-warning">{{ $unpaid_requests_count }}</span>
        </div>
    </div>
</div>

<!-- Requests Table Card -->
<div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h5 fw-bold text-dark mb-0">Recent Requests</h2>
        <a href="{{ route('client.services') }}" class="btn btn-dark btn-sm rounded-pill px-3">New Booking</a>
    </div>

    @if(count($requests) > 0)
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr class="text-secondary small">
                        <th>Service</th>
                        <th>Vendor</th>
                        <th>Price</th>
                        <th>Payment</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($requests as $request)
                        <tr>
                            <td>
                                <span class="fw-semibold text-dark">{{ $request->service_name }}</span> <br>
                                <span class="text-muted small">ID: #{{ $request->id }}</span>
                            </td>
                            <td>{{ $request->vendor_name }}</td>
                            <td>{{ $settings->default_currency ?? 'USD' }} {{ number_format($request->price, 2) }}</td>
                            <td>
                                @if($request->payment_status === 'Paid')
                                    <span class="badge bg-success-subtle text-success rounded-pill px-2.5 py-1">Paid</span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning rounded-pill px-2.5 py-1">Unpaid</span>
                                @endif
                            </td>
                            <td>
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
                                <span class="badge {{ $statusBadge }} rounded-pill px-2.5 py-1">{{ $request->status }}</span>
                            </td>
                            <td>
                                <a href="{{ route('client.request.details', $request->id) }}" class="btn btn-outline-secondary btn-sm rounded-pill">Details</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="text-center py-5">
            <i class="bi bi-clipboard fs-1 text-muted"></i>
            <p class="text-secondary mt-2 small mb-4">You have not created any requests yet.</p>
            <a href="{{ route('client.services') }}" class="btn btn-dark rounded-pill px-4">Browse Services</a>
        </div>
    @endif
</div>
@endsection
