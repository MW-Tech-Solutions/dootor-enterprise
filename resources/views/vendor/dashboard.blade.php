@extends('layouts.dashboard')

@section('title', 'Vendor Dashboard - ' . ($settings->platform_name ?? 'Umar Maher'))

@section('content')
<div class="mb-4">
    <h1 class="h3 fw-bold text-dark mb-1">Agent Panel</h1>
    <p class="text-secondary small">Manage your storefront services, client request operations, and profile custom settings</p>
</div>

<!-- Stats row -->
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
            <span class="text-muted d-block small mb-1">Active Custom Services</span>
            <span class="fw-bold display-6 text-success">{{ $active_services_count }}</span>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
            <span class="text-muted d-block small mb-1">Total Enabled Services</span>
            <span class="fw-bold display-6">{{ $total_services_count }}</span>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
            <span class="text-muted d-block small mb-1">Total Client Requests</span>
            <span class="fw-bold display-6 text-primary">{{ $total_requests_count }}</span>
        </div>
    </div>
</div>

<!-- Recent client requests -->
<div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h5 fw-bold text-dark mb-0">Recent Client Requests</h2>
        <a href="{{ route('vendor.requests') }}" class="btn btn-dark btn-sm rounded-pill px-3">View All Requests</a>
    </div>

    @if(count($requests) > 0)
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr class="text-secondary small">
                        <th>Request ID</th>
                        <th>Client</th>
                        <th>Service Requested</th>
                        <th>Price</th>
                        <th>Payment Status</th>
                        <th>Processing Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($requests as $request)
                        <tr>
                            <td><span class="fw-semibold">#{{ $request->id }}</span></td>
                            <td>
                                <span class="fw-semibold text-dark">{{ $request->client_name }}</span> <br>
                                <span class="text-muted small">{{ $request->client_email }}</span>
                            </td>
                            <td><span class="fw-semibold text-dark">{{ $request->service_name }}</span></td>
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
                                <a href="{{ route('vendor.requests') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">Manage</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="text-center py-5">
            <i class="bi bi-inbox fs-1 text-muted"></i>
            <p class="text-secondary mt-2 small">No service requests received from clients yet.</p>
        </div>
    @endif
</div>
@endsection
