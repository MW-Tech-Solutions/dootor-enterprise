@extends('layouts.dashboard')

@section('title', 'Client Subscriptions - ' . ($settings->platform_name ?? 'DOOTOR ENTERPRISES'))

@section('content')
<div class="mb-4">
    <h1 class="h3 fw-bold text-dark mb-1">Client Subscriptions</h1>
    <p class="text-secondary small">Search clients and manage processing statuses for their ordered services</p>
</div>

<!-- Search Panel -->
<div class="card border-0 shadow-sm p-4 rounded-4 bg-white mb-4">
    <form action="{{ route('admin.subscriptions') }}" method="GET" class="row g-3">
        <div class="col-md-9">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 border-light"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="search" class="form-control border-start-0 border-light ps-0" placeholder="Search by name, email, request ID, or payment reference..." value="{{ $search }}">
            </div>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-dark rounded-pill px-4 flex-grow-1">Search</button>
            @if($search)
                <a href="{{ route('admin.subscriptions') }}" class="btn btn-outline-secondary rounded-pill px-3"><i class="bi bi-x-lg"></i></a>
            @endif
        </div>
    </form>
</div>

<!-- Users and Subscriptions Listing -->
@if($users->count() > 0)
    <div class="d-flex flex-column gap-4">
        @foreach($users as $user)
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                <div class="card-header bg-light border-0 py-3 px-4 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-dark text-white d-flex align-items-center justify-content-center fw-bold" style="width: 40px; height: 40px; font-size: 14px;">
                            {{ strtoupper(substr($user->first_name, 0, 1)) }}
                        </div>
                        <div>
                            <h3 class="h6 fw-bold text-dark mb-0">{{ $user->first_name }} {{ $user->last_name }}</h3>
                            <span class="text-secondary small">{{ $user->email }} | {{ $user->phone ?? 'No Phone' }}</span>
                        </div>
                    </div>
                    <div>
                        <span class="badge bg-dark-subtle text-dark rounded-pill px-3 py-1 text-uppercase small" style="font-size: 11px;">
                            Total Orders: {{ $user->clientRequests->count() }}
                        </span>
                    </div>
                </div>
                
                <div class="card-body p-4">
                    @if($user->clientRequests->count() > 0)
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                    <tr class="text-secondary small border-bottom">
                                        <th class="ps-0">Request ID</th>
                                        <th>Service Name</th>
                                        <th>Price</th>
                                        <th>Payment Reference</th>
                                        <th>Payment Status</th>
                                        <th>Processing Status</th>
                                        <th class="text-end pe-0">Update Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($user->clientRequests as $request)
                                        <tr>
                                            <td class="ps-0 fw-bold text-dark">#{{ sprintf('%04d', $request->id) }}</td>
                                            <td>
                                                <div class="fw-semibold text-dark">{{ $request->service_name }}</div>
                                                <span class="text-muted small">Ordered {{ $request->created_at->format('M d, Y') }}</span>
                                            </td>
                                            <td class="fw-bold">{{ $settings->default_currency ?? 'USD' }} {{ number_format($request->price, 2) }}</td>
                                            <td>
                                                <code class="small">{{ $request->payment_reference ?? 'N/A' }}</code>
                                            </td>
                                            <td>
                                                <span class="badge rounded-pill px-2.5 py-1 {{ $request->payment_status === 'Paid' ? 'bg-success text-white' : 'bg-warning text-dark' }}">
                                                    {{ $request->payment_status }}
                                                </span>
                                            </td>
                                            <td>
                                                @php
                                                    $statusBadge = match($request->status) {
                                                        'Completed' => 'bg-success text-white',
                                                        'Processing' => 'bg-info text-dark',
                                                        'Cancelled' => 'bg-danger text-white',
                                                        default => 'bg-warning text-dark'
                                                    };
                                                @endphp
                                                <span class="badge {{ $statusBadge }} rounded-pill px-2.5 py-1">
                                                    {{ $request->status }}
                                                </span>
                                            </td>
                                            <td class="text-end pe-0">
                                                <form action="{{ route('admin.subscription.update', $request->id) }}" method="POST" class="d-inline-flex gap-2 align-items-center">
                                                    @csrf
                                                    @method('PATCH')
                                                    <select name="status" class="form-select form-select-sm rounded-3 w-auto" style="min-width: 140px;">
                                                        <option value="Awaiting Payment" {{ $request->status === 'Awaiting Payment' ? 'selected' : '' }}>Awaiting Payment</option>
                                                        <option value="Processing" {{ $request->status === 'Processing' ? 'selected' : '' }}>Processing</option>
                                                        <option value="Completed" {{ $request->status === 'Completed' ? 'selected' : '' }}>Completed</option>
                                                        <option value="Cancelled" {{ $request->status === 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                                                    </select>
                                                    <button type="submit" class="btn btn-dark btn-sm rounded-3 px-3">Save</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4 bg-light rounded-3">
                            <i class="bi bi-journal-x fs-4 text-muted"></i>
                            <p class="text-secondary small mb-0 mt-1">This user hasn't subscribed to any services yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
    
    <div class="d-flex justify-content-center mt-4">
        {{ $users->links() }}
    </div>
@else
    <div class="card border-0 shadow-sm p-5 rounded-4 text-center bg-white">
        <i class="bi bi-people display-4 text-muted mb-3"></i>
        <h3 class="h5 fw-bold text-dark mb-1">No Clients Found</h3>
        <p class="text-secondary small mb-0">No users match your query or have active service subscriptions.</p>
    </div>
@endif
@endsection
