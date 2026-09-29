@extends('layouts.app')

@section('title', 'Terms & Conditions - ' . ($settings->platform_name ?? 'DOOTOR ENTERPRISES'))

@section('body')
<div class="bg-dark text-white py-5 position-relative" style="background: linear-gradient(135deg, #001a0c 0%, #003b1c 100%) !important;">
    <div class="container py-4">
        <a href="/" class="btn btn-outline-light btn-sm rounded-pill mb-3"><i class="bi bi-arrow-left me-1"></i> Return Home</a>
        <h1 class="display-6 fw-bold mb-2">Terms &amp; Conditions</h1>
        <p class="text-white-50 small mb-0">Official platform usage guidelines, applicant responsibilities, and service policies.</p>
    </div>
</div>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-sm p-4 p-md-5 rounded-4 bg-white">
                <div class="d-flex align-items-center justify-content-between pb-3 mb-4 border-bottom">
                    <span class="text-muted small"><i class="bi bi-clock me-1"></i> Last Updated: {{ $settings->updated_at ? $settings->updated_at->format('F d, Y') : date('F d, Y') }}</span>
                    <span class="badge bg-success-subtle text-success px-3 py-1.5 rounded-pill font-monospace small" style="color: #004225 !important; background-color: #e6f4ea !important;">
                        Official Policy Document
                    </span>
                </div>

                <div class="legal-content text-secondary" style="line-height: 1.8;">
                    @if(!empty(trim($settings->terms_conditions ?? '')))
                        {!! $settings->terms_conditions !!}
                    @else
                        <h3>1. Platform Usage & Services</h3>
                        <p>By registering a client account or using the document processing portal on {{ $settings->platform_name ?? 'DOOTOR ENTERPRISES' }}, you agree to submit authentic application details and supporting documents for official vetting, consultation, and document authentication services.</p>

                        <h3>2. Client Obligations</h3>
                        <p>Applicants are responsible for ensuring all provided identity information, names, contact numbers, address details, and uploaded files are accurate, complete, and valid. Providing fraudulent or forged documentation will result in immediate termination of processing without refund.</p>

                        <h3>3. Confidentiality & Security Protocols</h3>
                        <p>{{ $settings->platform_name ?? 'DOOTOR ENTERPRISES' }} handles all personal data and document records under strict confidentiality protocols. Data is only accessible by assigned processing officers and authorized administrators.</p>

                        <h3>4. Operational Disclaimer</h3>
                        <p>{{ $settings->platform_name ?? 'DOOTOR ENTERPRISES' }} is an independent document consultancy enterprise connecting clients with verified document handling agents. We are not a government annex or official embassy branch.</p>
                    @endif
                </div>

                <div class="mt-5 pt-4 border-top d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <a href="{{ route('register.client') }}" class="btn text-white rounded-pill px-4" style="background-color: #004225;">Continue to Registration</a>
                    <div class="d-flex gap-3 small">
                        <a href="{{ route('refund.policy') }}" class="text-secondary text-decoration-none fw-medium">Refund Policy</a>
                        <a href="{{ route('privacy.policy') }}" class="text-secondary text-decoration-none fw-medium">Data &amp; Privacy Statement</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
