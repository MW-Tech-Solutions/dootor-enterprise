@extends('layouts.dashboard')

@section('title', 'Client Requests - ' . ($settings->platform_name ?? 'Umar Maher'))

@section('content')
<div class="mb-4">
    <h1 class="h3 fw-bold text-dark mb-1">Client Requests</h1>
    <p class="text-secondary small">View client document files, modify status tracks, and manage processing actions</p>
</div>

<div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
    @if(count($requests) > 0)
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr class="text-secondary small">
                        <th>Request ID</th>
                        <th>Client</th>
                        <th>Service</th>
                        <th>Documents</th>
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
                            <td>
                                @if(is_array($request->documents) && count($request->documents) > 0)
                                    <div class="dropdown">
                                        <button class="btn btn-outline-secondary btn-sm dropdown-toggle rounded-pill" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            Files ({{ count($request->documents) }})
                                        </button>
                                        <ul class="dropdown-menu border-0 shadow small">
                                            @foreach($request->documents as $doc)
                                                <li><a class="dropdown-item" href="{{ $doc['url'] }}" target="_blank"><i class="bi bi-file-earmark-arrow-down me-1 text-primary"></i> {{ $doc['name'] }}</a></li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @else
                                    <span class="text-muted small">No Files</span>
                                @endif
                            </td>
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
                                <!-- Trigger Update Status Modal -->
                                <button class="btn btn-dark btn-sm rounded-pill px-3" type="button" data-bs-toggle="modal" data-bs-target="#statusModal-{{ $request->id }}">Modify</button>
                            </td>
                        </tr>

                        <!-- Status Modal -->
                        <div class="modal fade" id="statusModal-{{ $request->id }}" tabindex="-1" aria-labelledby="statusModalLabel-{{ $request->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow">
                                    <div class="modal-header border-bottom">
                                        <h5 class="modal-title fw-bold" id="statusModalLabel-{{ $request->id }}">Modify Processing Status</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="{{ route('vendor.request.status', $request->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label for="status-select-{{ $request->id }}" class="form-label small fw-medium">Processing Track</label>
                                                <select name="status" id="status-select-{{ $request->id }}" class="form-select rounded-3">
                                                    <option value="Awaiting Payment" {{ $request->status === 'Awaiting Payment' ? 'selected' : '' }}>Awaiting Payment</option>
                                                    <option value="Pending Documents" {{ $request->status === 'Pending Documents' ? 'selected' : '' }}>Pending Documents</option>
                                                    <option value="Under Review" {{ $request->status === 'Under Review' ? 'selected' : '' }}>Under Review</option>
                                                    <option value="In Progress" {{ $request->status === 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                                    <option value="Completed" {{ $request->status === 'Completed' ? 'selected' : '' }}>Completed</option>
                                                    <option value="Rejected" {{ $request->status === 'Rejected' ? 'selected' : '' }}>Rejected</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-top">
                                            <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-dark rounded-pill px-4">Update Status</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="text-center py-5">
            <i class="bi bi-clipboard-data fs-1 text-muted"></i>
            <p class="text-secondary mt-2 small">No client orders received yet.</p>
        </div>
    @endif
</div>
@endsection
