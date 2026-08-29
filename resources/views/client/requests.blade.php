@extends('layouts.dashboard')

@section('title', 'My Requests - ' . ($settings->platform_name ?? 'Umar Maher'))

@section('content')
<div class="mb-4">
    <h1 class="h3 fw-bold text-dark mb-1">My Requests</h1>
    <p class="text-secondary small">View and manage your service orders and tracking</p>
</div>

<div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
    @if(count($requests) > 0)
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr class="text-secondary small">
                        <th>Request ID</th>
                        <th>Service</th>
                        <th>Vendor</th>
                        <th>Price</th>
                        <th>Payment Status</th>
                        <th>Processing Status</th>
                        <th>Created</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($requests as $request)
                        <tr>
                            <td><span class="fw-semibold">#{{ $request->id }}</span></td>
                            <td><span class="fw-semibold text-dark">{{ $request->service_name }}</span></td>
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
                            <td class="small text-secondary">{{ $request->created_at->format('M d, Y') }}</td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('client.request.details', $request->id) }}" class="btn btn-outline-dark btn-sm rounded-pill px-3">View</a>
                                    @if($request->payment_status === 'Unpaid')
                                        <form action="{{ route('client.request.delete', $request->id) }}" method="POST" class="m-0" onsubmit="return confirm('Are you sure you want to cancel this booking?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill"><i class="bi bi-trash"></i></button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="text-center py-5">
            <i class="bi bi-clipboard fs-1 text-muted"></i>
            <p class="text-secondary mt-2 small">You haven't ordered any services yet.</p>
            <a href="{{ route('client.services') }}" class="btn btn-dark rounded-pill px-4 mt-3">Browse Services</a>
        </div>
    @endif
</div>
@endsection
