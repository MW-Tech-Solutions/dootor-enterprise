@extends('layouts.app')

@section('title', 'Set New Password - ' . ($settings->platform_name ?? 'DOOTOR ENTERPRISES'))

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
    <!-- Left Pane: Branding -->
    <div class="col-lg-5 d-none d-lg-flex flex-column justify-content-between p-5 text-white position-relative" style="background: linear-gradient(135deg, #001a0c 0%, #003b1c 100%); min-height: 100vh;">
        <div class="position-absolute top-0 start-0 w-100 h-100" style="background: radial-gradient(circle at 10% 10%, rgba(212, 175, 55, 0.15), transparent 60%); pointer-events: none;"></div>
        
        <div>
            <a href="/" class="d-inline-flex align-items-center gap-2 text-decoration-none text-white mb-5">
                @if($settings && $settings->logo_url)
                    <img src="{{ app_file_url($settings->logo_url) }}" alt="Logo" class="rounded" style="height: 48px; width: 48px; object-fit: contain;">
                @else
                    <svg width="40" height="40" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg" class="me-1">
                        <path d="M20 20C40 20 52 32 52 50C52 68 40 80 20 80C14 80 14 74 14 74V26C14 26 14 20 20 20Z" fill="url(#goldLogoNewPass)" />
                        <path d="M28 32C38 32 44 40 44 50C44 60 38 68 28 68V32Z" fill="#FFFFFF" />
                        <path d="M50 24H80V35H64V44H76V53H64V63H80V74H50V24Z" fill="#004225" />
                        <defs>
                            <linearGradient id="goldLogoNewPass" x1="14" y1="20" x2="52" y2="80" gradientUnits="userSpaceOnUse">
                                <stop offset="0%" stop-color="#FFE57F" />
                                <stop offset="50%" stop-color="#D4AF37" />
                                <stop offset="100%" stop-color="#AA820A" />
                            </linearGradient>
                        </defs>
                    </svg>
                @endif
                <span class="fw-bold fs-4 tracking-tight text-white">{{ $settings->platform_name ?? 'DOOTOR ENTERPRISES' }}</span>
            </a>
            
            <h2 class="display-6 fw-bold text-white mb-3" style="line-height: 1.2;">Set New Password</h2>
            <p class="text-white-50 mb-5 fs-6">Please choose a strong, secure password for your account.</p>
        </div>

        <div class="border-top border-secondary border-opacity-20 pt-4 mt-5">
            <span class="d-block small text-white-50" style="font-size: 11px; line-height: 1.4;">WE ARE NOT GOVERNMENT OFFICIALS, AFFILIATES, OR AN ANNEX OF THE NIGERIAN HIGH COMMISSION.</span>
        </div>
    </div>

    <!-- Right Pane: New Password Form -->
    <div class="col-lg-7 d-flex align-items-center justify-content-center bg-light py-5 px-3 px-sm-5" style="flex-grow: 1; min-height: 100vh;">
        <div class="card border-0 shadow-sm p-4 p-sm-5 rounded-4 bg-white" style="max-width: 460px; width: 100%;">
            <div class="mb-4">
                <h1 class="h3 fw-bold text-dark mb-1">Create New Password</h1>
                <p class="text-secondary small">Resetting password for <strong>{{ $email }}</strong></p>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-3 small mb-4" role="alert">
                    <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show rounded-3 small mb-4" role="alert">
                    <i class="bi bi-exclamation-triangle me-1"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form action="{{ route('password.update-web') }}" method="POST">
                @csrf

                <!-- New Password -->
                <div class="mb-3">
                    <label for="new_password" class="form-label small fw-medium text-dark">New Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-secondary"><i class="bi bi-lock"></i></span>
                        <input type="password" name="new_password" id="new_password" class="form-control bg-light border-start-0 py-2.5 rounded-end-3 @error('new_password') is-invalid @enderror" placeholder="Minimum 8 characters" required autofocus>
                    </div>
                    @error('new_password')
                        <div class="text-danger small mt-1" style="font-size: 11.5px;">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Confirm New Password -->
                <div class="mb-4">
                    <label for="new_password_confirmation" class="form-label small fw-medium text-dark">Confirm New Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-secondary"><i class="bi bi-shield-lock"></i></span>
                        <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="form-control bg-light border-start-0 py-2.5 rounded-end-3" placeholder="Re-enter new password" required>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-brand-primary w-100 py-2.5 rounded-3 mb-3 d-flex align-items-center justify-content-center gap-2">
                    <span>Update Password</span>
                    <i class="bi bi-check2-circle"></i>
                </button>
            </form>

            <div class="text-center text-secondary small mt-3 pt-3 border-top border-light">
                <a href="{{ route('login') }}" class="text-decoration-none text-brand-success fw-semibold">Back to Login</a>
            </div>
        </div>
    </div>
</div>
@endsection
