@extends('layouts.dashboard')

@section('title', 'My Requests - ' . ($settings->platform_name ?? 'Dooter Enterprises'))

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
                        <th>Application Ref</th>
                        <th>Service Requested</th>
                        <th>Processor</th>
                        <th>Transaction ID</th>
                        <th>Price / Paid</th>
                        <th>Payment Status</th>
                        <th>Processing Stage</th>
                        <th>Submitted</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($requests as $request)
                        <tr>
                            <td>
                                <span class="font-monospace fw-bold text-primary small">{{ $request->reference_number ?? ('DE-' . $request->id) }}</span>
                            </td>
                            <td>
                                <span class="fw-semibold text-dark">{{ $request->service_name }}</span>
                                @if($request->sub_service_name)
                                    <br><span class="text-muted small">{{ $request->sub_service_name }}</span>
                                @endif
                            </td>
                            <td><span class="small">{{ $request->vendor_name ?? 'Dooter Admin' }}</span></td>
                            <td>
                                @if($request->payment_reference)
                                    <span class="badge bg-light text-dark font-monospace border small" title="Payment Reference"><i class="bi bi-receipt me-1 text-success"></i>{{ $request->payment_reference }}</span>
                                @else
                                    <span class="text-muted small">&mdash;</span>
                                @endif
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $currencySymbol }}{{ number_format($request->price, 2) }}</div>
                                @if($request->amount_paid > 0)
                                    <div class="text-success small" style="font-size: 11px;">Paid: {{ $currencySymbol }}{{ number_format($request->amount_paid, 2) }}</div>
                                @endif
                            </td>
                            <td>
                                @if($request->payment_status === 'Paid')
                                    <span class="badge bg-success-subtle text-success rounded-pill px-2.5 py-1"><i class="bi bi-check-circle me-1"></i>Paid</span>
                                @elseif($request->payment_status === 'Partial')
                                    <span class="badge bg-info-subtle text-info rounded-pill px-2.5 py-1">Partial</span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning rounded-pill px-2.5 py-1"><i class="bi bi-clock me-1"></i>Unpaid</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $stageDisplay = $request->current_stage_name ?? $request->status;
                                    $statusBadge = match(true) {
                                        in_array($request->status, ['Completed', 'Approved', 'Verified']) => 'bg-success text-white',
                                        in_array($request->status, ['Processing', 'In Progress', 'Under Review', 'Document Verification']) => 'bg-info text-dark',
                                        in_array($request->status, ['Payment Confirmed', 'Submitted', 'Awaiting Assignment']) => 'bg-warning text-dark',
                                        in_array($request->status, ['Cancelled', 'Rejected']) => 'bg-danger text-white',
                                        default => 'bg-warning text-dark'
                                    };
                                @endphp
                                <span class="badge {{ $statusBadge }} rounded-pill px-2.5 py-1">{{ $stageDisplay }}</span>
                            </td>
                            <td class="small text-secondary">{{ $request->created_at->format('M d, Y') }}</td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('client.request.details', $request->id) }}" class="btn btn-outline-dark btn-sm rounded-pill px-3">View</a>
                                    @if($request->payment_status === 'Unpaid')
                                        <form action="{{ route('client.request.delete', $request->id) }}" method="POST" class="m-0" onsubmit="return confirm('Are you sure you want to cancel this booking?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill" title="Delete Unpaid Application"><i class="bi bi-trash"></i></button>
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
