@extends('layouts.app')

@section('title', 'Forgot Password - ' . ($settings->platform_name ?? 'DOOTOR ENTERPRISES'))

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
                    <img src="{{ $settings->logo_url }}" alt="Logo" class="rounded" style="height: 48px; width: 48px; object-fit: contain;">
                @else
                    <svg width="40" height="40" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg" class="me-1">
                        <!-- Stylized D (Gold) -->
                        <path d="M20 20C40 20 52 32 52 50C52 68 40 80 20 80C14 80 14 74 14 74V26C14 26 14 20 20 20Z" fill="url(#goldLogoForgot)" />
                        <path d="M28 32C38 32 44 40 44 50C44 60 38 68 28 68V32Z" fill="#FFFFFF" />
                        <!-- Stylized E (Dark Green) -->
                        <path d="M50 24H80V35H64V44H76V53H64V63H80V74H50V24Z" fill="#004225" />
                        <defs>
                            <linearGradient id="goldLogoForgot" x1="14" y1="20" x2="52" y2="80" gradientUnits="userSpaceOnUse">
                                <stop offset="0%" stop-color="#FFE57F" />
                                <stop offset="50%" stop-color="#D4AF37" />
                                <stop offset="100%" stop-color="#AA820A" />
                            </linearGradient>
                        </defs>
                    </svg>
                @endif
                <span class="fw-bold fs-4 tracking-tight text-white">{{ $settings->platform_name ?? 'DOOTOR ENTERPRISES' }}</span>
            </a>
            
            <h2 class="display-6 fw-bold text-white mb-3" style="line-height: 1.2;">Security First Credential Recovery</h2>
            <p class="text-white-50 mb-5 fs-6">Recover your account passwords securely through verification links dispatched straight to your registered e-mail address.</p>
        </div>

        <div class="border-top border-secondary border-opacity-20 pt-4 mt-5">
            <span class="d-block small text-white-50" style="font-size: 11px; line-height: 1.4;">WE ARE NOT GOVERNMENT OFFICIALS, AFFILIATES, OR AN ANNEX OF THE NIGERIAN HIGH COMMISSION.</span>
        </div>
    </div>

    <!-- Right Pane: Forgot Password Form Container -->
    <div class="col-lg-7 d-flex align-items-center justify-content-center bg-light py-5 px-3 px-sm-5" style="flex-grow: 1; min-height: 100vh;">
        <div class="card border-0 shadow-sm p-4 p-sm-5 rounded-4 bg-white" style="max-width: 460px; width: 100%;">
            <!-- Mobile Brand Logo header (hidden on desktop) -->
            <div class="text-center mb-4 d-block d-lg-none">
                <a href="/" class="d-inline-flex align-items-center gap-2 text-decoration-none text-dark mb-2">
                    @if($settings && $settings->logo_url)
                        <img src="{{ $settings->logo_url }}" alt="Logo" class="rounded" style="height: 38px; width: 38px; object-fit: contain;">
                    @else
                        <svg width="28" height="28" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg" class="me-1">
                            <path d="M20 20C40 20 52 32 52 50C52 68 40 80 20 80C14 80 14 74 14 74V26C14 26 14 20 20 20Z" fill="url(#goldLogoForgotMobile)" />
                            <path d="M28 32C38 32 44 40 44 50C44 60 38 68 28 68V32Z" fill="#FFFFFF" />
                            <path d="M50 24H80V35H64V44H76V53H64V63H80V74H50V24Z" fill="#004225" />
                            <defs>
                                <linearGradient id="goldLogoForgotMobile" x1="14" y1="20" x2="52" y2="80" gradientUnits="userSpaceOnUse">
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
                <h1 class="h3 fw-bold text-dark mb-1">Reset Password</h1>
                <p class="text-secondary small">Enter your email to receive a password reset link</p>
            </div>

            <form action="{{ route('password.email') }}" method="POST">
                @csrf

                <!-- Email -->
                <div class="mb-4">
                    <label for="email" class="form-label small fw-medium">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted" style="border-color: #dee2e6;"><i class="bi bi-envelope"></i></span>
                        <input type="email" name="email" id="email" class="form-control bg-light border-start-0 rounded-end-3 @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="name@example.com" style="border-color: #dee2e6;" required autofocus>
                    </div>
                    @error('email')
                        <div class="text-danger small mt-1" style="font-size: 11.5px;">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Submit -->
                <button type="submit" class="btn btn-brand-primary w-100 py-2.5 rounded-3 mb-3 d-flex align-items-center justify-content-center gap-2">
                    <span>Send Password Reset Link</span>
                    <i class="bi bi-arrow-right"></i>
                </button>
            </form>

            <div class="text-center text-secondary small mt-3 pt-3 border-top border-light">
                Remember your password? <a href="{{ route('login') }}" class="text-decoration-none text-brand-success fw-semibold">Sign In</a>
            </div>
        </div>
    </div>
</div>
@endsection
