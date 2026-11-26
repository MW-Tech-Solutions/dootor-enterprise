@extends('layouts.app')

@section('title', 'Staff Portal - ' . ($settings->platform_name ?? 'DOOTOR ENTERPRISES'))

@section('styles')
<style>
    .staff-portal-hero {
        background: linear-gradient(135deg, #001a0c 0%, #004225 65%, #002b17 100%);
        min-height: 100vh;
    }
    .staff-glass-card {
        background: rgba(255, 255, 255, 0.98);
        border-radius: 24px;
        box-shadow: 0 28px 70px rgba(0, 0, 0, 0.28);
        border: 1px solid rgba(212, 175, 55, 0.25);
    }
    .detail-tile {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 14px 16px;
    }
</style>
@endsection

@section('body')
<div class="staff-portal-hero d-flex flex-column justify-content-between py-4 px-3 px-md-5">
    <!-- Top Bar -->
    <div class="container d-flex justify-content-between align-items-center mb-4">
        <a href="{{ route('home') }}" class="d-inline-flex align-items-center gap-2 text-decoration-none text-white">
            @if($settings && $settings->logo_url)
                <img src="{{ asset($settings->logo_url) }}" alt="Logo" class="rounded" style="height: 40px; width: 40px; object-fit: contain;">
            @else
                <svg width="34" height="34" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M20 20C40 20 52 32 52 50C52 68 40 80 20 80C14 80 14 74 14 74V26C14 26 14 20 20 20Z" fill="url(#goldStaffLogo)" />
                    <path d="M28 32C38 32 44 40 44 50C44 60 38 68 28 68V32Z" fill="#001a0c" />
                    <path d="M50 24H80V35H64V44H76V53H64V63H80V74H50V24Z" fill="#2E7D32" />
                    <defs>
                        <linearGradient id="goldStaffLogo" x1="14" y1="20" x2="52" y2="80" gradientUnits="userSpaceOnUse">
                            <stop offset="0%" stop-color="#FFE57F" />
                            <stop offset="50%" stop-color="#D4AF37" />
                            <stop offset="100%" stop-color="#AA820A" />
                        </linearGradient>
                    </defs>
                </svg>
            @endif
            <div>
                <div class="fw-bold text-white lh-1">{{ $settings->platform_name ?? 'DOOTOR ENTERPRISES' }}</div>
                <small class="text-warning" style="font-size: 11px;">Authorized Staff &amp; Operations Portal</small>
            </div>
        </a>
        <a href="{{ route('home') }}" class="btn btn-outline-light btn-sm rounded-pill px-3">
            <i class="bi bi-arrow-left me-1"></i> Back to Homepage
        </a>
    </div>

    <!-- Main Content -->
    <div class="container my-auto py-3">
        <div class="row justify-content-center">
            <div class="col-lg-9 col-xl-8">
                <div class="staff-glass-card p-4 p-md-5">

                    @if(session('success'))
                        <div class="alert alert-success border-0 rounded-3 small mb-4">
                            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger border-0 rounded-3 small mb-4">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                        </div>
                    @endif

                    @auth
                        @php
                            $staffUser = Auth::user();
                            $isStaff = $staffUser->role === 'admin' || $staffUser->isAdmin() || $staffUser->roles()->exists();
                        @endphp

                        @if($isStaff)
                            <!-- Authenticated Staff Login & Account Details View -->
                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 border-bottom pb-4 mb-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm" style="width: 56px; height: 56px; background: linear-gradient(135deg, #004225, #D4AF37); font-size: 20px;">
                                        {{ strtoupper(substr($staffUser->first_name ?? $staffUser->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 mb-1" style="font-size: 11px;">
                                            <i class="bi bi-patch-check-fill me-1"></i> Authenticated Staff Session
                                        </span>
                                        <h1 class="h4 fw-bold text-dark mb-0">{{ $staffUser->name }}</h1>
                                        <p class="text-secondary small mb-0">{{ $staffUser->email }}</p>
                                    </div>
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('admin.dashboard') }}" class="btn btn-dark rounded-pill px-4 fw-semibold" style="background:#004225;border-color:#004225;">
                                        <i class="bi bi-speedometer2 me-1"></i> Open Dashboard
                                    </a>
                                    <form action="{{ route('logout') }}" method="POST" class="m-0">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-danger rounded-pill px-3 fw-semibold">
                                            <i class="bi bi-box-arrow-right me-1"></i> Logout
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <h2 class="h6 fw-bold text-dark text-uppercase mb-3" style="letter-spacing: 0.6px;">
                                <i class="bi bi-person-badge-fill text-success me-2"></i>Your Staff &amp; Login Details
                            </h2>

                            <div class="row g-3 mb-4">
                                <div class="col-sm-6">
                                    <div class="detail-tile">
                                        <div class="text-muted extra-small fw-semibold text-uppercase">Login Email</div>
                                        <div class="fw-bold text-dark small mt-1">{{ $staffUser->email }}</div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="detail-tile">
                                        <div class="text-muted extra-small fw-semibold text-uppercase">Staff File Number</div>
                                        <div class="fw-bold text-dark font-monospace small mt-1">{{ $staffUser->staff_file_number ?? ('STF-' . str_pad($staffUser->id, 4, '0', STR_PAD_LEFT)) }}</div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="detail-tile">
                                        <div class="text-muted extra-small fw-semibold text-uppercase">Assigned RBAC Role(s)</div>
                                        <div class="fw-bold text-success small mt-1">
                                            {{ $staffUser->roles->pluck('name')->implode(', ') ?: ucfirst($staffUser->role) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="detail-tile">
                                        <div class="text-muted extra-small fw-semibold text-uppercase">Account Status</div>
                                        <div class="fw-bold text-dark small mt-1">
                                            <span class="badge bg-success-subtle text-success rounded-pill px-2.5 py-1">{{ $staffUser->status ?? 'Approved' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Granted Permissions -->
                            <div class="detail-tile mb-4">
                                <div class="text-muted extra-small fw-semibold text-uppercase mb-2">Granted Portal Permissions</div>
                                <div class="d-flex flex-wrap gap-1.5">
                                    @php
                                        $rolePerms = $staffUser->roles->flatMap->permissions->pluck('name')->unique();
                                    @endphp
                                    @forelse($rolePerms as $permName)
                                        <span class="badge bg-white text-dark border rounded-pill px-2.5 py-1 fw-normal" style="font-size: 11px;">
                                            <i class="bi bi-check2 text-success me-1"></i>{{ $permName }}
                                        </span>
                                    @empty
                                        <span class="small text-secondary">Standard Administrator Access</span>
                                    @endforelse
                                </div>
                            </div>

                            <!-- Quick Staff Actions -->
                            <div class="d-flex flex-wrap gap-2">
                                <a href="{{ route('admin.dashboard') }}" class="btn btn-success rounded-pill px-4 fw-semibold" style="background:#004225;border-color:#004225;">
                                    <i class="bi bi-grid-fill me-1"></i> Staff Workspace
                                </a>
                                @if($staffUser->hasPermission('live_chat.manage'))
                                    <a href="{{ route('admin.live-chat') }}" class="btn btn-outline-dark rounded-pill px-4 fw-semibold">
                                        <i class="bi bi-chat-dots me-1"></i> Live Support Queue
                                    </a>
                                @endif
                                <a href="{{ route('admin.profile') }}" class="btn btn-outline-secondary rounded-pill px-4 fw-semibold">
                                    <i class="bi bi-key me-1"></i> Manage Password &amp; Profile
                                </a>
                            </div>
                        @else
                            <!-- Logged in as Client/Vendor -->
                            <div class="text-center py-4">
                                <i class="bi bi-shield-exclamation text-warning fs-1 d-block mb-3"></i>
                                <h2 class="h5 fw-bold text-dark">Client Session Active</h2>
                                <p class="text-secondary small mb-4">You are currently signed in as <strong>{{ $staffUser->email }}</strong> ({{ ucfirst($staffUser->role) }}). Log out to sign in with a Staff account.</p>
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('client.dashboard') }}" class="btn btn-outline-dark rounded-pill px-4">Go to Client Dashboard</a>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-dark rounded-pill px-4" style="background:#004225;">Switch to Staff Login</button>
                                    </form>
                                </div>
                            </div>
                        @endif
                    @else
                        <!-- Guest: Staff Portal Login Form -->
                        <div class="row align-items-center g-4">
                            <div class="col-md-5 border-end-md pe-md-4">
                                <span class="badge rounded-pill px-3 py-1.5 mb-3 fw-bold text-uppercase" style="background: rgba(0,66,37,0.1); color: #004225; font-size: 11px;">
                                    <i class="bi bi-shield-lock-fill me-1"></i> Staff &amp; Agent Access
                                </span>
                                <h1 class="h3 fw-bold text-dark mb-2">Staff Portal</h1>
                                <p class="text-secondary small mb-4" style="line-height: 1.65;">
                                    Sign in with your assigned staff credentials to view your login details, staff file number, assigned RBAC permissions, and access your operational queue.
                                </p>
                                <div class="d-flex flex-column gap-2 small text-secondary">
                                    <div><i class="bi bi-check-circle-fill text-success me-2"></i>View Staff File &amp; Role Details</div>
                                    <div><i class="bi bi-check-circle-fill text-success me-2"></i>Live Support Chat &amp; Work Queue</div>
                                    <div><i class="bi bi-check-circle-fill text-success me-2"></i>Application &amp; Document Vetting</div>
                                </div>
                            </div>

                            <div class="col-md-7 ps-md-4">
                                <h2 class="h5 fw-bold text-dark mb-1">Staff Login</h2>
                                <p class="text-muted small mb-4">Click Login to view your staff login details &amp; dashboard</p>

                                <form action="{{ route('staff.login.post') }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="staffEmail" class="form-label small fw-semibold">Staff Email Address</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light"><i class="bi bi-envelope"></i></span>
                                            <input type="email" name="email" id="staffEmail" class="form-control bg-light @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="staff@dootor-enterprises.com" required autofocus>
                                        </div>
                                        @error('email')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="staffPassword" class="form-label small fw-semibold">Password</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light"><i class="bi bi-lock"></i></span>
                                            <input type="password" name="password" id="staffPassword" class="form-control bg-light" placeholder="••••••••" required>
                                            <button type="button" class="btn btn-outline-secondary" onclick="toggleStaffPw(this)" title="Show/Hide Password">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                        <div class="form-check">
                                            <input type="checkbox" name="remember" id="staffRemember" class="form-check-input">
                                            <label for="staffRemember" class="form-check-label small text-secondary">Keep me signed in</label>
                                        </div>
                                        <a href="{{ route('password.request') }}" class="small text-decoration-none fw-semibold" style="color: #004225;">Forgot Password?</a>
                                    </div>

                                    <button type="submit" class="btn btn-dark w-100 py-2.5 rounded-pill fw-bold shadow-sm" style="background:#004225;border-color:#004225;">
                                        <i class="bi bi-box-arrow-in-right me-2"></i> Login to View Staff Details
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <div class="container text-center text-white-50 small pt-3">
        &copy; {{ date('Y') }} {{ $settings->platform_name ?? 'DOOTOR ENTERPRISES' }} · Staff &amp; Administrative Portal
    </div>
</div>
@endsection

@section('scripts')
<script>
function toggleStaffPw(btn) {
    var inp = document.getElementById('staffPassword');
    var icon = btn.querySelector('i');
    if (inp.type === 'password') {
        inp.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        inp.type = 'password';
        icon.className = 'bi bi-eye';
    }
}
</script>
@endsection
