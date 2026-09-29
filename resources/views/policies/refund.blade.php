@extends('layouts.app')

@section('title', 'Refund & Cancellation Policy - ' . ($settings->platform_name ?? 'DOOTOR ENTERPRISES'))

@section('body')
<div class="bg-dark text-white py-5 position-relative" style="background: linear-gradient(135deg, #001a0c 0%, #003b1c 100%) !important;">
    <div class="container py-4">
        <a href="/" class="btn btn-outline-light btn-sm rounded-pill mb-3"><i class="bi bi-arrow-left me-1"></i> Return Home</a>
        <h1 class="display-6 fw-bold mb-2">Refund &amp; Cancellation Policy</h1>
        <p class="text-white-50 small mb-0">Official terms governing cancellations, processing windows, and refund eligibility.</p>
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
                    @if(!empty(trim($settings->refund_policy ?? '')))
                        {!! $settings->refund_policy !!}
                    @else
                        <h3>1. Processing & Cancellation Window</h3>
                        <p>Clients may request a full refund prior to service assignment or initial application review by our processing team. Once officer assignment or document verification has commenced, partial administrative processing fees apply.</p>

                        <h3>2. Refund Eligibility Criteria</h3>
                        <p>Full refunds are granted if {{ $settings->platform_name ?? 'DOOTOR ENTERPRISES' }} is unable to initiate processing for your request within the designated service timeline due to internal operational issues.</p>

                        <h3>3. Non-Refundable Items</h3>
                        <p>Government statutory filing fees, official agency verification fees, or third-party courier dispatch costs already disbursed on behalf of the applicant are non-refundable under any circumstances.</p>

                        <h3>4. How to Submit a Refund Request</h3>
                        <p>To request a refund or raise a billing query, please open a support ticket from your client portal support desk or email <strong>support@dootor-enterprises.com</strong> with your application reference number.</p>
                    @endif
                </div>

                <div class="mt-5 pt-4 border-top d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <a href="{{ route('register.client') }}" class="btn text-white rounded-pill px-4" style="background-color: #004225;">Continue to Registration</a>
                    <div class="d-flex gap-3 small">
                        <a href="{{ route('terms.conditions') }}" class="text-secondary text-decoration-none fw-medium">Terms &amp; Conditions</a>
                        <a href="{{ route('privacy.policy') }}" class="text-secondary text-decoration-none fw-medium">Data &amp; Privacy Statement</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
