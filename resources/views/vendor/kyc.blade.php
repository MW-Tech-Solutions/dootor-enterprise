@extends('layouts.dashboard')

@section('title', 'Complete KYC - ' . ($settings->platform_name ?? 'Umar Maher'))

@section('content')
<div class="mb-4">
    <h1 class="h3 fw-bold text-dark mb-1">KYC Profile Submission</h1>
    <p class="text-secondary small">Submit your official identity papers and banking info to get approved by system administrators</p>
</div>

<div class="row g-4">
    <!-- KYC Form -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
            @if($kyc->status === 'Approved')
                <div class="alert alert-success rounded-3 p-4 mb-0 text-center" role="alert">
                    <i class="bi bi-patch-check-fill display-5 mb-2 d-block"></i>
                    <h4 class="fw-bold mb-1">Verification Approved!</h4>
                    <p class="small text-secondary mb-0">Your profile is fully vetted. You can now enable services and customize your public storefront.</p>
                </div>
            @elseif($kyc->status === 'Submitted')
                <div class="alert alert-info rounded-3 p-4 mb-0 text-center" role="alert">
                    <i class="bi bi-clock-history display-5 mb-2 d-block"></i>
                    <h4 class="fw-bold mb-1">Application Submitted</h4>
                    <p class="small text-secondary mb-0">We have received your KYC application. The verification team is reviewing your documents. You will be notified once approved.</p>
                </div>
            @else
                @if($kyc->status === 'Rejected')
                    <div class="alert alert-danger rounded-3 mb-4" role="alert">
                        <h4 class="h6 fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-2"></i> Verification Rejected</h4>
                        <p class="small mb-0"><strong>Reason:</strong> {{ $kyc->rejection_reason ?? 'The uploaded files were not readable. Please upload a clear photo.' }}</p>
                    </div>
                @endif

                <form action="{{ route('vendor.kyc.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- Identity info -->
                    <h3 class="h6 fw-bold text-dark mb-3 border-bottom pb-2">1. Identity Details</h3>
                    
                    <div class="mb-3">
                        <label for="nin" class="form-label small fw-medium">National Identification Number (NIN)</label>
                        <input type="text" name="nin" id="nin" class="form-control rounded-3 @error('nin') is-invalid @enderror" value="{{ old('nin', $kyc->nin) }}" placeholder="11 digits number" required>
                        @error('nin')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label for="id_card" class="form-label small fw-medium">National ID / Passport Photo</label>
                            @if($kyc->id_card_url)
                                <a href="{{ $kyc->id_card_url }}" target="_blank" class="d-block small mb-2 text-decoration-none"><i class="bi bi-file-earmark-image"></i> View Current ID Card</a>
                            @endif
                            <input type="file" name="id_card" id="id_card" class="form-control rounded-3 form-control-sm" {{ !$kyc->id_card_url ? 'required' : '' }}>
                        </div>
                        <div class="col-md-4">
                            <label for="proof_of_address" class="form-label small fw-medium">Proof of Address (Utility Bill)</label>
                            @if($kyc->proof_of_address_url)
                                <a href="{{ $kyc->proof_of_address_url }}" target="_blank" class="d-block small mb-2 text-decoration-none"><i class="bi bi-file-earmark-image"></i> View Current Proof</a>
                            @endif
                            <input type="file" name="proof_of_address" id="proof_of_address" class="form-control rounded-3 form-control-sm" {{ !$kyc->proof_of_address_url ? 'required' : '' }}>
                        </div>
                        <div class="col-md-4">
                            <label for="personal_image" class="form-label small fw-medium">Personal Passport Image</label>
                            @if($kyc->personal_image_url)
                                <a href="{{ $kyc->personal_image_url }}" target="_blank" class="d-block small mb-2 text-decoration-none"><i class="bi bi-file-earmark-image"></i> View Current Photo</a>
                            @endif
                            <input type="file" name="personal_image" id="personal_image" class="form-control rounded-3 form-control-sm" {{ !$kyc->personal_image_url ? 'required' : '' }}>
                        </div>
                    </div>

                    <!-- Payment settings -->
                    <h3 class="h6 fw-bold text-dark mb-3 border-bottom pb-2">2. Payout Account Details</h3>
                    <div class="mb-3">
                        <label for="bank_name" class="form-label small fw-medium">Bank Name</label>
                        <input type="text" name="bank_name" id="bank_name" class="form-control rounded-3 @error('bank_name') is-invalid @enderror" value="{{ old('bank_name', $kyc->bank_name) }}" placeholder="e.g. GTBank, Access Bank" required>
                        @error('bank_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-sm-6">
                            <label for="account_name" class="form-label small fw-medium">Account Holder Name</label>
                            <input type="text" name="account_name" id="account_name" class="form-control rounded-3 @error('account_name') is-invalid @enderror" value="{{ old('account_name', $kyc->account_name) }}" placeholder="e.g. John Doe Hub" required>
                            @error('account_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-sm-6">
                            <label for="account_number" class="form-label small fw-medium">Account Number</label>
                            <input type="text" name="account_number" id="account_number" class="form-control rounded-3 @error('account_number') is-invalid @enderror" value="{{ old('account_number', $kyc->account_number) }}" placeholder="10 digits number" required>
                            @error('account_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Terms & Agreements -->
                    <h3 class="h6 fw-bold text-dark mb-3 border-bottom pb-2">3. Legal Declarations</h3>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="terms_agreed" id="terms_agreed" required>
                        <label class="form-check-label small text-secondary" for="terms_agreed">
                            I agree to the Platform Terms of Service and Privacy Policy.
                        </label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="privacy_agreed" id="privacy_agreed" required>
                        <label class="form-check-label small text-secondary" for="privacy_agreed">
                            I consent to the collection and vetting checks of my identity documents.
                        </label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="data_consent_agreed" id="data_consent_agreed" required>
                        <label class="form-check-label small text-secondary" for="data_consent_agreed">
                            I verify that all information provided is accurate and holds official validity.
                        </label>
                    </div>

                    <div class="mb-4">
                        <label for="digital_signature" class="form-label small fw-medium">Digital Signature (Type your Full Name)</label>
                        <input type="text" name="digital_signature" id="digital_signature" class="form-control rounded-3" value="{{ old('digital_signature', $kyc->digital_signature) }}" placeholder="e.g. Johnathan Doe" required>
                    </div>

                    <button type="submit" class="btn btn-dark rounded-pill px-4 py-2 small">Submit Verification Profile</button>
                </form>
            @endif
        </div>
    </div>

    <!-- Info Box -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm p-4 rounded-4 bg-white mb-4">
            <h3 class="h6 fw-bold text-dark mb-3">Verification Info</h3>
            
            <div class="d-flex justify-content-between py-1.5 border-bottom border-light small">
                <span class="text-muted">Verification Status:</span>
                @php
                    $kycBadge = match($kyc->status) {
                        'NotStarted' => 'bg-secondary text-white',
                        'Submitted' => 'bg-info text-white',
                        'Approved' => 'bg-success text-white',
                        'Rejected' => 'bg-danger text-white',
                        default => 'bg-light text-dark'
                    };
                @endphp
                <span class="badge {{ $kycBadge }} rounded-pill px-2.5 py-1">{{ $kyc->status }}</span>
            </div>
            
            <div class="mt-4 p-3 bg-light rounded-3 small">
                <span class="fw-semibold text-dark d-block mb-1"><i class="bi bi-shield-exclamation text-primary"></i> Vetting Timeline</span>
                <span class="text-secondary small">Vetting verification files usually takes between 1 to 2 business days. We will review your ID, utilities bill, and bank records before approval.</span>
            </div>
        </div>
    </div>
</div>
@endsection
