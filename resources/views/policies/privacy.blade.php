@extends('layouts.app')

@section('title', 'Data & Privacy Protection Statement - ' . ($settings->platform_name ?? 'DOOTOR ENTERPRISES'))

@section('body')
<div class="bg-dark text-white py-5 position-relative" style="background: linear-gradient(135deg, #001a0c 0%, #003b1c 100%) !important;">
    <div class="container py-4">
        <a href="/" class="btn btn-outline-light btn-sm rounded-pill mb-3"><i class="bi bi-arrow-left me-1"></i> Return Home</a>
        <h1 class="display-6 fw-bold mb-2">Data &amp; Privacy Protection Statement</h1>
        <p class="text-white-50 small mb-0">How your personal data, identity documents, and application records are encrypted, stored, and protected.</p>
    </div>
</div>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-sm p-4 p-md-5 rounded-4 bg-white">
                <div class="d-flex align-items-center justify-content-between pb-3 mb-4 border-bottom">
                    <span class="text-muted small"><i class="bi bi-clock me-1"></i> Last Updated: {{ $settings->updated_at ? $settings->updated_at->format('F d, Y') : date('F d, Y') }}</span>
                    <span class="badge bg-success-subtle text-success px-3 py-1.5 rounded-pill font-monospace small" style="color: #004225 !important; background-color: #e6f4ea !important;">
                        Official Privacy Statement
                    </span>
                </div>

                <div class="legal-content text-secondary" style="line-height: 1.8;">
                    @if(!empty(trim($settings->privacy_policy ?? '')))
                        {!! $settings->privacy_policy !!}
                    @else
                        <h3>1. Data Collection & Purpose</h3>
                        <p>{{ $settings->platform_name ?? 'DOOTOR ENTERPRISES' }} collects applicant personal data, contact details, identity documents, and application questionnaire inputs solely for processing and verifying requested consular and administrative document services.</p>

                        <h3>2. End-to-End Security & Access Control</h3>
                        <p>Your uploaded documents and identity records are transmitted over SSL/TLS encrypted channels and stored in secure database servers. Only assigned processing officers and platform administrators have authorization to view processing documents.</p>

                        <h3>3. Third-Party Sharing Restrictions</h3>
                        <p>We strictly do not sell, trade, or rent personal applicant information to marketing agencies or unauthorized third parties. Document details are only shared with official government processing channels or courier dispatch services as necessary to fulfill your requested service.</p>

                        <h3>4. Data Retention & Privacy Rights</h3>
                        <p>Applicant records are retained securely for verification audit trails and order history tracking. You may request data updates or profile modifications by submitting a support ticket through your client portal.</p>
                    @endif
                </div>

                <div class="mt-5 pt-4 border-top d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <a href="{{ route('register.client') }}" class="btn text-white rounded-pill px-4" style="background-color: #004225;">Continue to Registration</a>
                    <div class="d-flex gap-3 small">
                        <a href="{{ route('terms.conditions') }}" class="text-secondary text-decoration-none fw-medium">Terms &amp; Conditions</a>
                        <a href="{{ route('refund.policy') }}" class="text-secondary text-decoration-none fw-medium">Refund Policy</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
