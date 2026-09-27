@extends('layouts.app')

@section('title', 'Register Client - ' . ($settings->platform_name ?? 'DOOTOR ENTERPRISES'))

@section('styles')
<style>
    .btn-brand-primary {
        background-color: #004225 !important;
        border: none !important;
        color: #ffffff !important;
        font-weight: 600;
        transition: all 0.25s ease;
    }
    .btn-brand-primary:hover {
        background-color: #002411 !important;
        box-shadow: 0 4px 12px rgba(0, 66, 37, 0.2);
        transform: translateY(-1px);
    }
    .text-brand-success {
        color: #004225 !important;
        font-weight: 600;
    }
    .text-brand-success:hover {
        color: #d4af37 !important;
        text-decoration: underline !important;
    }
    .form-control:focus {
        border-color: #004225 !important;
        box-shadow: 0 0 0 0.25rem rgba(0, 66, 37, 0.15) !important;
    }
</style>
@endsection

@section('body')
<div class="container-fluid p-0 min-vh-100 d-flex flex-column flex-lg-row">
    <!-- Left Pane: Branding & Service Features (hidden on mobile, shown on lg) -->
    <div class="col-lg-5 d-none d-lg-flex flex-column justify-content-between p-5 text-white position-relative" style="background: linear-gradient(135deg, #001a0c 0%, #003b1c 100%); min-height: 100vh;">
        <!-- Glowing background decoration -->
        <div class="position-absolute top-0 start-0 w-100 h-100" style="background: radial-gradient(circle at 10% 10%, rgba(212, 175, 55, 0.15), transparent 60%); pointer-events: none;"></div>
        
        <div>
            <a href="/" class="d-inline-flex align-items-center gap-2 text-decoration-none text-white mb-5">
                @if($settings && $settings->logo_url)
                    <img src="{{ asset($settings->logo_url) }}" alt="Logo" class="rounded" style="height: 48px; width: 48px; object-fit: contain;">
                @else
                    <svg width="40" height="40" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg" class="me-1">
                        <!-- Stylized D (Gold) -->
                        <path d="M20 20C40 20 52 32 52 50C52 68 40 80 20 80C14 80 14 74 14 74V26C14 26 14 20 20 20Z" fill="url(#goldLogoRegClient)" />
                        <path d="M28 32C38 32 44 40 44 50C44 60 38 68 28 68V32Z" fill="#FFFFFF" />
                        <!-- Stylized E (Dark Green) -->
                        <path d="M50 24H80V35H64V44H76V53H64V63H80V74H50V24Z" fill="#004225" />
                        <defs>
                            <linearGradient id="goldLogoRegClient" x1="14" y1="20" x2="52" y2="80" gradientUnits="userSpaceOnUse">
                                <stop offset="0%" stop-color="#FFE57F" />
                                <stop offset="50%" stop-color="#D4AF37" />
                                <stop offset="100%" stop-color="#AA820A" />
                            </linearGradient>
                        </defs>
                    </svg>
                @endif
                <span class="fw-bold fs-4 tracking-tight text-white">{{ $settings->platform_name ?? 'DOOTOR ENTERPRISES' }}</span>
            </a>
            
            <h2 class="display-6 fw-bold text-white mb-3" style="line-height: 1.2;">Access Vetted Document Consultancy</h2>
            <p class="text-white-50 mb-5 fs-6">Register a secure client profile to order and track official documents vetting, verification, and authentication supports.</p>
            
            <div class="d-flex flex-column gap-3">
                <div class="d-flex align-items-start gap-3">
                    <div class="bg-warning text-dark rounded-circle p-1 d-flex align-items-center justify-content-center" style="width: 24px; height: 24px; background-color: #d4af37 !important; flex-shrink: 0;">
                        <i class="bi bi-shield-check" style="font-size: 14px;"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 text-white fw-semibold small">Secure Profile Registration</h5>
                        <p class="text-white-50 small mb-0">Your database entries are protected. Only verified agents assigned to your request will access file processing details.</p>
                    </div>
                </div>
                
                <div class="d-flex align-items-start gap-3">
                    <div class="bg-warning text-dark rounded-circle p-1 d-flex align-items-center justify-content-center" style="width: 24px; height: 24px; background-color: #d4af37 !important; flex-shrink: 0;">
                        <i class="bi bi-clock-history" style="font-size: 14px;"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 text-white fw-semibold small">Expedited Tracking Timeline</h5>
                        <p class="text-white-50 small mb-0">Track files through order placements, vetted validations, processing, and courier deliveries.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="border-top border-secondary border-opacity-20 pt-4 mt-5">
            <span class="d-block small text-white-50" style="font-size: 11px; line-height: 1.4;">WE ARE NOT GOVERNMENT OFFICIALS, AFFILIATES, OR AN ANNEX OF THE NIGERIAN HIGH COMMISSION.</span>
        </div>
    </div>

    <!-- Right Pane: Client Registration Form Container -->
    <div class="col-lg-7 d-flex align-items-center justify-content-center bg-light py-5 px-3 px-sm-5" style="flex-grow: 1; min-height: 100vh;">
        <div class="card border-0 shadow-sm p-4 p-sm-5 rounded-4 bg-white" style="max-width: 520px; width: 100%;">
            <!-- Mobile Brand Logo header (hidden on desktop) -->
            <div class="text-center mb-4 d-block d-lg-none">
                <a href="/" class="d-inline-flex align-items-center gap-2 text-decoration-none text-dark mb-2">
                    @if($settings && $settings->logo_url)
                        <img src="{{ asset($settings->logo_url) }}" alt="Logo" class="rounded" style="height: 38px; width: 38px; object-fit: contain;">
                    @else
                        <svg width="28" height="28" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg" class="me-1">
                            <path d="M20 20C40 20 52 32 52 50C52 68 40 80 20 80C14 80 14 74 14 74V26C14 26 14 20 20 20Z" fill="url(#goldLogoRegClientMobile)" />
                            <path d="M28 32C38 32 44 40 44 50C44 60 38 68 28 68V32Z" fill="#FFFFFF" />
                            <path d="M50 24H80V35H64V44H76V53H64V63H80V74H50V24Z" fill="#004225" />
                            <defs>
                                <linearGradient id="goldLogoRegClientMobile" x1="14" y1="20" x2="52" y2="80" gradientUnits="userSpaceOnUse">
                                    <stop offset="0%" stop-color="#FFE57F" />
                                    <stop offset="50%" stop-color="#D4AF37" />
                                    <stop offset="100%" stop-color="#AA820A" />
                                </linearGradient>
                            </defs>
                        </svg>
                    @endif
                    <span class="fw-bold text-dark fs-5">{{ $settings->platform_name ?? 'DOOTOR ENTERPRISES' }}</span>
                </a>
            </div>

            <div class="mb-4">
                <h1 class="h3 fw-bold text-dark mb-1">Create Client Account</h1>
                <p class="text-secondary small">Register to book verified document services</p>
            </div>

            <form action="{{ route('register.client') }}" method="POST">
                @csrf

                <!-- Name Fields -->
                <div class="row g-2 mb-3 align-items-end">
                    <div class="col-sm-4">
                        <label for="first_name" class="form-label small fw-medium text-nowrap">First Name</label>
                        <input type="text" name="first_name" id="first_name" class="form-control bg-light rounded-3 @error('first_name') is-invalid @enderror" value="{{ old('first_name') }}" placeholder="Abiodun" style="border-color: #dee2e6;" required>
                        @error('first_name')
                            <div class="text-danger small mt-1" style="font-size: 11px;">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-sm-4">
                        <label for="middle_name" class="form-label small fw-medium text-nowrap">Middle Name <span class="text-muted fw-normal" style="font-size: 11px;">(Optional)</span></label>
                        <input type="text" name="middle_name" id="middle_name" class="form-control bg-light rounded-3 @error('middle_name') is-invalid @enderror" value="{{ old('middle_name') }}" placeholder="Kolawole" style="border-color: #dee2e6;">
                        @error('middle_name')
                            <div class="text-danger small mt-1" style="font-size: 11px;">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-sm-4">
                        <label for="last_name" class="form-label small fw-medium text-nowrap">Last Name</label>
                        <input type="text" name="last_name" id="last_name" class="form-control bg-light rounded-3 @error('last_name') is-invalid @enderror" value="{{ old('last_name') }}" placeholder="Okonkwo" style="border-color: #dee2e6;" required>
                        @error('last_name')
                            <div class="text-danger small mt-1" style="font-size: 11px;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Contact Fields -->
                <div class="row g-2 mb-3">
                    <div class="col-sm-7">
                        <label for="email" class="form-label small fw-medium">Email Address</label>
                        <input type="email" name="email" id="email" class="form-control bg-light rounded-3 @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="abiodun@example.com" style="border-color: #dee2e6;" required>
                        @error('email')
                            <div class="text-danger small mt-1" style="font-size: 11px;">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-sm-5">
                        <label for="phone" class="form-label small fw-medium">Phone Number</label>
                        <input type="text" name="phone" id="phone" class="form-control bg-light rounded-3 @error('phone') is-invalid @enderror" value="{{ old('phone') }}" placeholder="+1 (416) 555-0199" style="border-color: #dee2e6;">
                        @error('phone')
                            <div class="text-danger small mt-1" style="font-size: 11px;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Location Distinction: Country Applying From vs Country for Requested Service -->
                @php
                    $allCountries = \App\Services\LocationService::allCountries();
                    $oldApplyingFrom = old('country_applying_from', old('country', 'Canada'));
                    $oldRequestedCountry = old('country_service_requested', 'Nigeria');
                @endphp

                <div class="row g-2 mb-3">
                    <div class="col-sm-6">
                        <label for="country_applying_from" class="form-label small fw-medium">Country Applying From</label>
                        <select name="country_applying_from" id="country_applying_from" class="form-select african-country-select bg-light rounded-3 @error('country_applying_from') is-invalid @enderror" data-selected="{{ $oldApplyingFrom }}" data-division-target="state" data-label-target="state_label" style="border-color: #dee2e6;" required>
                            @foreach($allCountries as $c)
                                <option value="{{ $c['name'] }}" {{ $oldApplyingFrom == $c['name'] ? 'selected' : '' }}>{{ $c['name'] }}</option>
                            @endforeach
                        </select>
                        @error('country_applying_from')
                            <div class="text-danger small mt-1" style="font-size: 11px;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-sm-6">
                        <label for="country_service_requested" class="form-label small fw-medium">Country for Requested Service</label>
                        <select name="country_service_requested" id="country_service_requested" class="form-select bg-light rounded-3 @error('country_service_requested') is-invalid @enderror" style="border-color: #dee2e6;" required>
                            @foreach($allCountries as $c)
                                <option value="{{ $c['name'] }}" {{ $oldRequestedCountry == $c['name'] ? 'selected' : '' }}>{{ $c['name'] }}</option>
                            @endforeach
                        </select>
                        @error('country_service_requested')
                            <div class="text-danger small mt-1" style="font-size: 11px;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Dynamic Administrative Division & City -->
                <div class="row g-2 mb-3">
                    <div class="col-sm-6">
                        <label for="state" id="state_label" class="form-label small fw-medium african-division-label">State / Province / Region</label>
                        <select name="state" id="state" class="form-select african-division-select bg-light rounded-3 @error('state') is-invalid @enderror" data-selected="{{ old('state') }}" style="border-color: #dee2e6;">
                            <option value="">Loading Divisions...</option>
                        </select>
                        @error('state')
                            <div class="text-danger small mt-1" style="font-size: 11px;">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-sm-6">
                        <label for="city" class="form-label small fw-medium">City / Town</label>
                        <input type="text" name="city" id="city" class="form-control bg-light rounded-3 @error('city') is-invalid @enderror" value="{{ old('city') }}" placeholder="e.g. Toronto, London, Lagos" style="border-color: #dee2e6;">
                        @error('city')
                            <div class="text-danger small mt-1" style="font-size: 11px;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Password Fields -->
                <div class="row g-2 mb-4">
                    <div class="col-sm-6">
                        <label for="password" class="form-label small fw-medium">Password</label>
                        <input type="password" name="password" id="password" class="form-control bg-light rounded-3 @error('password') is-invalid @enderror" placeholder="••••••••" style="border-color: #dee2e6;" required>
                        @error('password')
                            <div class="text-danger small mt-1" style="font-size: 11px;">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-sm-6">
                        <label for="password_confirmation" class="form-label small fw-medium">Confirm Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control bg-light rounded-3" placeholder="••••••••" style="border-color: #dee2e6;" required>
                    </div>
                </div>

                <!-- Terms & Conditions and Refund Policy Agreement Checkboxes -->
                <div class="p-3 bg-light rounded-3 border mb-4">
                    <!-- Checkbox 1: Terms & Conditions -->
                    <div class="form-check mb-2.5">
                        <input class="form-check-input @error('terms_check') is-invalid @enderror" type="checkbox" name="terms_check" id="termsCheck" value="1" required {{ old('terms_check') ? 'checked' : '' }}>
                        <label class="form-check-label small text-dark fw-medium" for="termsCheck">
                            I have read and agree to the <a href="javascript:void(0)" class="text-brand-success fw-bold text-decoration-underline" data-bs-toggle="modal" data-bs-target="#termsModal">Terms &amp; Conditions</a>
                        </label>
                        <small class="d-block text-muted" style="font-size: 11px;">You must review and accept the official platform terms before creating an account.</small>
                        @error('terms_check')
                            <div class="text-danger small mt-1" style="font-size: 11px;">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr class="my-2 border-secondary border-opacity-10">

                    <!-- Checkbox 2: Refund Policy -->
                    <div class="form-check">
                        <input class="form-check-input @error('refund_check') is-invalid @enderror" type="checkbox" name="refund_check" id="refundCheck" value="1" required {{ old('refund_check') ? 'checked' : '' }}>
                        <label class="form-check-label small text-dark fw-medium" for="refundCheck">
                            I have read and agree to the <a href="javascript:void(0)" class="text-brand-success fw-bold text-decoration-underline" data-bs-toggle="modal" data-bs-target="#refundModal">Refund Policy</a>
                        </label>
                        <small class="d-block text-muted" style="font-size: 11px;">Review our policy regarding service cancellations, processing windows, and refund eligibility.</small>
                        @error('refund_check')
                            <div class="text-danger small mt-1" style="font-size: 11px;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Submit -->
                <button type="submit" class="btn btn-brand-primary w-100 py-2.5 rounded-3 mb-3 d-flex align-items-center justify-content-center gap-2">
                    <span>Register as Client</span>
                    <i class="bi bi-arrow-right"></i>
                </button>
            </form>

            <div class="text-center text-secondary small mt-3 pt-3 border-top border-light">
                Already have an account? <a href="{{ route('login') }}" class="text-decoration-none text-brand-success fw-semibold">Sign In</a>
                {{-- Vendor registration disabled for now: <a href="{{ route('register') }}" class="text-decoration-none text-brand-success fw-semibold">Register as Vendor</a> --}}
            </div>
        </div>
    </div>
</div>

<!-- Terms & Conditions Modal -->
<div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-light border-bottom py-3">
                <h5 class="modal-title fw-bold text-dark mb-0 fs-6" id="termsModalLabel">
                    <i class="bi bi-shield-check me-2 text-success"></i> Platform Terms &amp; Conditions
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-secondary small" style="line-height: 1.6;">
                @php
                    $sysSettings = \App\Models\SystemSetting::first();
                    $termsContent = $sysSettings->terms_conditions ?? '';
                @endphp
                @if(!empty(trim($termsContent)))
                    {!! $termsContent !!}
                @else
                    <h5>1. Platform Usage & Services</h5>
                    <p>By creating a client account on DOOTOR ENTERPRISES, you agree to submit authentic application details and supporting documents for official vetting, consultation, and document authentication services.</p>
                    <h5>2. Client Obligations</h5>
                    <p>Applicants are responsible for ensuring all provided identity information, names, contact numbers, and uploaded files are accurate, complete, and valid.</p>
                    <h5>3. Confidentiality & Data Protection</h5>
                    <p>DOOTOR ENTERPRISES handles all personal data and document records under strict confidentiality protocols. Data is only accessible by assigned processing officers and authorized administrators.</p>
                    <h5>4. Disclaimer</h5>
                    <p>DOOTOR ENTERPRISES is an independent document consultancy enterprise and is not a government annex or official embassy branch.</p>
                @endif
            </div>
            <div class="modal-footer bg-light border-top py-2.5">
                <button type="button" class="btn btn-brand-primary btn-sm rounded-pill px-4" data-bs-dismiss="modal" onclick="document.getElementById('termsCheck').checked = true;">
                    <i class="bi bi-check-circle me-1"></i> I Accept Terms &amp; Conditions
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Refund Policy Modal -->
<div class="modal fade" id="refundModal" tabindex="-1" aria-labelledby="refundModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-light border-bottom py-3">
                <h5 class="modal-title fw-bold text-dark mb-0 fs-6" id="refundModalLabel">
                    <i class="bi bi-cash-stack me-2 text-success"></i> Official Refund &amp; Cancellation Policy
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-secondary small" style="line-height: 1.6;">
                @php
                    $refundContent = $sysSettings->refund_policy ?? '';
                @endphp
                @if(!empty(trim($refundContent)))
                    {!! $refundContent !!}
                @else
                    <h5>1. Processing & Cancellation Window</h5>
                    <p>Clients may request a full refund prior to service assignment or initial application review. Once document processing or officer assignment has commenced, partial administrative processing fees apply.</p>
                    <h5>2. Refund Eligibility</h5>
                    <p>Full refunds are granted if DOOTOR ENTERPRISES is unable to initiate processing for your request within the designated service timeline due to internal operational issues.</p>
                    <h5>3. Non-Refundable Items</h5>
                    <p>Government statutory filing fees or official third-party courier dispatch costs already disbursed on behalf of the applicant are non-refundable.</p>
                    <h5>4. Requesting a Refund</h5>
                    <p>To request a refund, please contact customer support through your client dashboard support portal with your application reference number.</p>
                @endif
            </div>
            <div class="modal-footer bg-light border-top py-2.5">
                <button type="button" class="btn btn-brand-primary btn-sm rounded-pill px-4" data-bs-dismiss="modal" onclick="document.getElementById('refundCheck').checked = true;">
                    <i class="bi bi-check-circle me-1"></i> I Accept Refund Policy
                </button>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('js/location-loader.js') }}"></script>
@endsection
