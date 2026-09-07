@extends('layouts.dashboard')

@section('title', 'Admin Profile - Dootor Enterprises')

@section('content')
<div class="container-fluid p-0">
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1">
                <i class="bi bi-person-badge text-success me-2"></i> Administrator Profile
            </h3>
            <p class="text-muted small mb-0">Update your administrator account details, profile picture, location, and security credentials.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm border-0 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm border-0 mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Profile & Account Info Form -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold text-dark mb-3">Profile Information</h5>

                    <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Avatar Upload Box -->
                        <div class="d-flex align-items-center gap-3 mb-4 p-3 bg-light rounded-4">
                            @if($user->avatar_url)
                                <img src="{{ app_file_url($user->avatar_url) }}" alt="Avatar" class="rounded-circle border border-3 border-white shadow-sm" style="width: 80px; height: 80px; object-fit: cover;">
                            @else
                                <div class="rounded-circle text-dark d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 80px; height: 80px; font-size: 28px; background-color: #d4af37;">
                                    {{ strtoupper(substr($user->first_name ?? 'A', 0, 1)) }}{{ strtoupper(substr($user->last_name ?? 'D', 0, 1)) }}
                                </div>
                            @endif

                            <div>
                                <label class="form-label fw-semibold text-dark small mb-1">Profile Avatar</label>
                                <input type="file" name="avatar" class="form-control form-control-sm" accept="image/*">
                                <span class="text-muted d-block mt-1" style="font-size: 11px;">Allowed formats: JPG, PNG, WEBP. Max size: 5MB.</span>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark small">First Name <span class="text-danger">*</span></label>
                                <input type="text" name="first_name" class="form-control py-2" value="{{ old('first_name', $user->first_name ?? explode(' ', $user->name)[0] ?? '') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark small">Last Name <span class="text-danger">*</span></label>
                                <input type="text" name="last_name" class="form-control py-2" value="{{ old('last_name', $user->last_name ?? explode(' ', $user->name)[1] ?? '') }}" required>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark small">Email Address <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control py-2" value="{{ old('email', $user->email) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark small">Phone Number</label>
                                <input type="text" name="phone" class="form-control py-2" value="{{ old('phone', $user->phone) }}" placeholder="+234...">
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark small">Country</label>
                                <input type="text" name="country" class="form-control py-2" value="{{ old('country', $user->country ?? 'Nigeria') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark small">State / Region</label>
                                <input type="text" name="state" class="form-control py-2" value="{{ old('state', $user->state) }}" placeholder="e.g. Lagos">
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn text-white fw-semibold px-4 py-2" style="background-color: #004225; border-radius: 8px;">
                                <i class="bi bi-save me-1"></i> Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Security / Password Box & Overview -->
        <div class="col-lg-4">
            <!-- Account Overview -->
            <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold text-dark mb-3">Account Overview</h5>

                    <div class="mb-3">
                        <span class="text-muted d-block small">Role & Level</span>
                        <span class="badge text-white px-3 py-2 rounded-pill mt-1" style="background-color: #004225;">
                            <i class="bi bi-shield-check me-1"></i> System Administrator
                        </span>
                    </div>

                    <div class="mb-3">
                        <span class="text-muted d-block small">Account Status</span>
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill mt-1">Active</span>
                    </div>

                    <div class="mb-3">
                        <span class="text-muted d-block small">Joined Date</span>
                        <span class="fw-semibold text-dark small">{{ $user->created_at ? $user->created_at->format('F d, Y') : 'N/A' }}</span>
                    </div>
                </div>
            </div>

            <!-- Password Change Form -->
            <div class="card border-0 shadow-sm rounded-4 bg-white">
                <div class="card-body p-4">
                    <h5 class="fw-bold text-dark mb-3">
                        <i class="bi bi-key text-warning me-1"></i> Change Password
                    </h5>

                    <form action="{{ route('admin.profile.password') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark small">Current Password <span class="text-danger">*</span></label>
                            <input type="password" name="current_password" class="form-control py-2" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark small">New Password <span class="text-danger">*</span></label>
                            <input type="password" name="new_password" class="form-control py-2" minlength="8" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark small">Confirm New Password <span class="text-danger">*</span></label>
                            <input type="password" name="new_password_confirmation" class="form-control py-2" minlength="8" required>
                        </div>

                        <button type="submit" class="btn btn-dark w-100 py-2 fw-semibold" style="border-radius: 8px;">
                            <i class="bi bi-lock me-1"></i> Update Password
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
