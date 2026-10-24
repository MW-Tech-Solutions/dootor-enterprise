@extends('layouts.dashboard')

@section('title', 'My Requests - ' . ($settings->platform_name ?? 'Dooter Enterprises'))

@section('content')
<div class="mb-4">
    <h1 class="h3 fw-bold text-dark mb-1">My Requests</h1>
    <p class="text-secondary small">View and manage your service orders and tracking</p>
</div>

@if(count($requests) > 0)
    <!-- Mobile Card View (Matches Screenshot Exact 1:1) -->
    <div class="d-md-none">
        @foreach($requests as $request)
            <div class="card border-0 shadow-sm p-3.5 rounded-4 bg-white mb-3" style="border: 1px solid #f1f5f9 !important; border-radius: 20px !important;">
                <!-- Application Ref Header -->
                <div class="d-flex align-items-center gap-2.5 mb-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background-color: #ecfdf5; color: #059669; border: 1px solid #d1fae5; flex-shrink: 0; border-radius: 10px !important;">
                        <i class="bi bi-file-earmark-text" style="font-size: 18px;"></i>
                    </div>
                    <div>
                        <span class="text-secondary text-uppercase d-block" style="font-size: 10px; font-weight: 600; letter-spacing: 0.02em;">Application Ref</span>
                        <span class="font-monospace fw-bold" style="color: #004225; font-size: 12.5px;">{{ $request->reference_number ?? ('DE-' . $request->id) }}</span>
                    </div>
                </div>

                <!-- 2-Column Info Section -->
                <div class="row g-2 mb-3">
                    <div class="col-7 pe-2">
                        <span class="text-secondary d-block" style="font-size: 10.5px;">Service</span>
                        <span class="fw-bold text-dark d-block text-truncate" style="font-size: 13.5px;">{{ $request->service_name }}</span>
                        
                        <span class="text-secondary d-block mt-2" style="font-size: 10.5px;">Subservice</span>
                        <span class="text-secondary d-block text-truncate" style="font-size: 12px; color: #64748b !important;">{{ $request->sub_service_name ?? '—' }}</span>
                    </div>
                    <div class="col-5 ps-2">
                        <span class="text-secondary d-block" style="font-size: 10.5px;">Processor</span>
                        <span class="text-dark fw-semibold d-block text-truncate" style="font-size: 12.5px;">{{ $request->vendor_name ?? 'Dootor Enterprises' }}</span>
                        
                        <span class="text-secondary d-block mt-2" style="font-size: 10.5px;">Transaction ID</span>
                        <span class="text-secondary font-monospace d-block text-truncate" style="font-size: 11px; color: #64748b !important;">{{ $request->payment_reference ?? '—' }}</span>
                    </div>
                </div>

                <!-- Price and Badges -->
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 pt-2.5 border-top mb-3" style="border-color: #f1f5f9 !important;">
                    <div>
                        <span class="text-secondary d-block" style="font-size: 10.5px;">Price / Paid</span>
                        <span class="fw-bold text-dark" style="font-size: 1.25rem; font-weight: 800;">{{ $currencySymbol ?? '$' }}{{ number_format($request->amount_paid > 0 ? $request->amount_paid : $request->price, 2) }}</span>
                    </div>
                    <div class="d-flex align-items-center gap-1.5 flex-wrap">
                        @if($request->payment_status === 'Paid')
                            <span class="badge fw-bold rounded-pill px-2.5 py-1 d-inline-flex align-items-center gap-1" style="background-color: #ecfdf5; color: #059669; font-size: 10.5px;">
                                <i class="bi bi-shield-check"></i> Paid
                            </span>
                        @elseif($request->payment_status === 'Partial')
                            <span class="badge fw-bold rounded-pill px-2.5 py-1 d-inline-flex align-items-center gap-1" style="background-color: #e0f2fe; color: #0284c7; font-size: 10.5px;">
                                Partial
                            </span>
                        @else
                            <span class="badge fw-bold rounded-pill px-2.5 py-1 d-inline-flex align-items-center gap-1" style="background-color: #fffbeb; color: #d97706; font-size: 10.5px;">
                                <i class="bi bi-exclamation-circle"></i> Unpaid
                            </span>
                        @endif

                        @php
                            $stageName = $request->current_stage_name ?? $request->status;
                            $stageBadgeStyle = match(true) {
                                in_array($request->status, ['Completed', 'Approved', 'Verified']) => 'background-color: #ecfdf5; color: #059669;',
                                in_array($request->status, ['In Progress', 'Processing', 'Document Verification']) => 'background-color: #e0f2fe; color: #0284c7;',
                                in_array($request->status, ['Application Submitted', 'Submitted', 'Under Review']) => 'background-color: #fef3c7; color: #b45309;',
                                in_array($request->status, ['Awaiting Assignment', 'Pending']) => 'background-color: #fef3c7; color: #b45309;',
                                in_array($request->status, ['Cancelled', 'Rejected']) => 'background-color: #fee2e2; color: #dc2626;',
                                default => 'background-color: #e0f2fe; color: #0284c7;'
                            };
                        @endphp
                        <span class="badge fw-bold rounded-pill px-2.5 py-1 d-inline-flex align-items-center gap-1" style="{{ $stageBadgeStyle }}; font-size: 10.5px;">
                            <i class="bi bi-clock"></i> {{ $stageName }}
                        </span>
                    </div>
                </div>

                <!-- Footer: Submitted date and Action Buttons -->
                <div class="d-flex align-items-center justify-content-between pt-2.5 border-top" style="border-color: #f1f5f9 !important;">
                    <div class="d-flex align-items-center gap-1.5 text-secondary" style="font-size: 11.5px;">
                        <i class="bi bi-calendar3"></i>
                        <span>Submitted {{ $request->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('client.request.details', $request->id) }}" class="btn btn-sm rounded-pill px-3.5 fw-semibold" style="font-size: 12px; border: 1.5px solid #004225; color: #004225; background: #ffffff;">
                            View
                        </a>
                        @if($request->payment_status === 'Unpaid')
                            <form action="{{ route('client.request.delete', $request->id) }}" method="POST" class="m-0" onsubmit="return confirm('Are you sure you want to cancel this booking?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm rounded-circle d-flex align-items-center justify-content-center p-0" style="width: 32px; height: 32px; border: 1.5px solid #fca5a5; color: #ef4444; background: #ffffff;" title="Delete Unpaid Application">
                                    <i class="bi bi-trash" style="font-size: 13px;"></i>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Desktop Table View -->
    <div class="card border-0 shadow-sm p-4 rounded-4 bg-white d-none d-md-block">
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
                            <td><span class="small">{{ $request->vendor_name ?? 'Dootor Admin' }}</span></td>
                            <td>
                                @if($request->payment_reference)
                                    <span class="badge bg-light text-dark font-monospace border small" title="Payment Reference"><i class="bi bi-receipt me-1 text-success"></i>{{ $request->payment_reference }}</span>
                                @else
                                    <span class="text-muted small">&mdash;</span>
                                @endif
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $currencySymbol ?? '$' }}{{ number_format($request->price, 2) }}</div>
                                @if($request->amount_paid > 0)
                                    <div class="text-success small" style="font-size: 11px;">Paid: {{ $currencySymbol ?? '$' }}{{ number_format($request->amount_paid, 2) }}</div>
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
    </div>
@else
    <div class="card border-0 shadow-sm p-4 rounded-4 bg-white text-center py-5">
        <i class="bi bi-clipboard fs-1 text-muted"></i>
        <p class="text-secondary mt-2 small">You haven't ordered any services yet.</p>
        <div>
            <a href="{{ route('client.services') }}" class="btn btn-dark rounded-pill px-4 mt-2">Browse Services</a>
        </div>
    </div>
@endif
@endsection
