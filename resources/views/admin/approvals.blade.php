@extends('layouts.dashboard')

@section('title', 'Vendor Approvals - ' . ($settings->platform_name ?? 'Umar Maher'))

@section('content')
<div class="mb-4">
    <h1 class="h3 fw-bold text-dark mb-1">Vendor Vetting Queue</h1>
    <p class="text-secondary small">Review uploaded national ID cards, utility bills, and digital agreements before approval</p>
</div>

<div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
    @if(count($pending_vendors) > 0)
        <div class="accordion accordion-flush" id="approvalsAccordion">
            @foreach($pending_vendors as $index => $vendor)
                <div class="accordion-item border-bottom py-3">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed d-flex justify-content-between align-items-center" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $vendor->id }}">
                            <div class="d-flex align-items-center gap-3">
                                @if($vendor->avatar_url)
                                    <img src="{{ $vendor->avatar_url }}" alt="avatar" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                                @else
                                    <div class="rounded-circle bg-dark text-white d-flex align-items-center justify-content-center fw-bold" style="width: 40px; height: 40px; font-size: 14px;">
                                        {{ strtoupper(substr($vendor->first_name, 0, 1)) }}
                                    </div>
                                @endif
                                <div>
                                    <span class="fw-bold text-dark d-block">{{ $vendor->first_name }} {{ $vendor->last_name }}</span>
                                    <span class="text-muted small">Submitted: {{ $vendor->kycProfile->updated_at->format('M d, Y, h:i A') }}</span>
                                </div>
                            </div>
                        </button>
                    </h2>
                    <div id="collapse-{{ $vendor->id }}" class="accordion-collapse collapse" data-bs-parent="#approvalsAccordion">
                        <div class="accordion-body px-0 pt-4">
                            <div class="row g-4">
                                <!-- Docs info -->
                                <div class="col-md-7">
                                    <h3 class="h6 fw-bold text-dark mb-3 border-bottom pb-2">Documents Uploaded</h3>
                                    
                                    <div class="row g-3 mb-4">
                                        <div class="col-sm-4 text-center">
                                            <span class="text-muted d-block small mb-1">ID Card / Passport</span>
                                            @if($vendor->kycProfile->id_card_url)
                                                <a href="{{ $vendor->kycProfile->id_card_url }}" target="_blank" class="d-block border rounded p-1 shadow-sm bg-light">
                                                    <img src="{{ $vendor->kycProfile->id_card_url }}" alt="ID Card" class="img-fluid rounded" style="max-height: 120px; object-fit: cover;">
                                                </a>
                                            @else
                                                <span class="text-danger small">Missing</span>
                                            @endif
                                        </div>
                                        <div class="col-sm-4 text-center">
                                            <span class="text-muted d-block small mb-1">Proof of Address</span>
                                            @if($vendor->kycProfile->proof_of_address_url)
                                                <a href="{{ $vendor->kycProfile->proof_of_address_url }}" target="_blank" class="d-block border rounded p-1 shadow-sm bg-light">
                                                    <img src="{{ $vendor->kycProfile->proof_of_address_url }}" alt="Address Proof" class="img-fluid rounded" style="max-height: 120px; object-fit: cover;">
                                                </a>
                                            @else
                                                <span class="text-danger small">Missing</span>
                                            @endif
                                        </div>
                                        <div class="col-sm-4 text-center">
                                            <span class="text-muted d-block small mb-1">Personal Photo</span>
                                            @if($vendor->kycProfile->personal_image_url)
                                                <a href="{{ $vendor->kycProfile->personal_image_url }}" target="_blank" class="d-block border rounded p-1 shadow-sm bg-light">
                                                    <img src="{{ $vendor->kycProfile->personal_image_url }}" alt="Personal Photo" class="img-fluid rounded" style="max-height: 120px; object-fit: cover;">
                                                </a>
                                            @else
                                                <span class="text-danger small">Missing</span>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div class="bg-light p-3 rounded-3 small">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-muted">NIN Number:</span>
                                            <span class="fw-bold text-dark">{{ $vendor->kycProfile->nin }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-muted">Digital Signature:</span>
                                            <span class="fw-bold text-dark">{{ $vendor->kycProfile->digital_signature }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span class="text-muted">Consent Agreements:</span>
                                            <span class="text-success"><i class="bi bi-check-circle-fill"></i> Accepted</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Bank settings & Action buttons -->
                                <div class="col-md-5">
                                    <h3 class="h6 fw-bold text-dark mb-3 border-bottom pb-2">Payout Destination</h3>
                                    
                                    <div class="table-responsive small mb-4">
                                        <table class="table table-sm table-borderless m-0">
                                            <tr>
                                                <td class="text-muted py-1" style="width: 140px;">Bank Name:</td>
                                                <td class="fw-bold text-dark py-1">{{ $vendor->kycProfile->bank_name }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted py-1">Account Holder:</td>
                                                <td class="fw-bold text-dark py-1">{{ $vendor->kycProfile->account_name }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted py-1">Account Number:</td>
                                                <td class="fw-bold text-dark py-1">{{ $vendor->kycProfile->account_number }}</td>
                                            </tr>
                                        </table>
                                    </div>

                                    <div class="d-flex gap-2">
                                        <!-- Approve form -->
                                        <form action="{{ route('admin.approvals.approve', $vendor->id) }}" method="POST" class="flex-grow-1 m-0">
                                            @csrf
                                            <button type="submit" class="btn btn-success w-100 rounded-pill py-2 small fw-semibold">Approve Profile</button>
                                        </form>
                                        
                                        <!-- Reject Trigger button -->
                                        <button class="btn btn-outline-danger w-100 rounded-pill py-2 small fw-semibold" type="button" data-bs-toggle="modal" data-bs-target="#rejectModal-{{ $vendor->id }}">Reject</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Rejection Modal -->
                <div class="modal fade" id="rejectModal-{{ $vendor->id }}" tabindex="-1" aria-labelledby="rejectModalLabel-{{ $vendor->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0 shadow">
                            <div class="modal-header border-bottom">
                                <h5 class="modal-title fw-bold" id="rejectModalLabel-{{ $vendor->id }}">Specify Rejection Reason</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form action="{{ route('admin.approvals.reject', $vendor->id) }}" method="POST">
                                @csrf
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="reason-{{ $vendor->id }}" class="form-label small fw-medium">Rejection Feedback (sent to vendor)</label>
                                        <textarea name="rejection_reason" id="reason-{{ $vendor->id }}" class="form-control rounded-3" rows="4" placeholder="Specify why the files were rejected..." required></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer border-top">
                                    <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-danger rounded-pill px-4">Submit Rejection</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-5">
            <i class="bi bi-inbox fs-1 text-muted"></i>
            <p class="text-secondary mt-2 small">No vendor applications waiting for review.</p>
        </div>
    @endif
</div>
@endsection
