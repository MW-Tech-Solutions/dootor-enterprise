@extends('layouts.dashboard')

@section('title', 'Profile Settings - ' . ($settings->platform_name ?? 'Umar Maher'))

@section('content')
<div class="mb-4">
    <h1 class="h3 fw-bold text-dark mb-1">Profile Settings</h1>
    <p class="text-secondary small">Manage your personal info and user profile settings</p>
</div>

<div class="card border-0 shadow-sm p-4 rounded-4 bg-white" style="max-width: 600px;">
    <form action="{{ route('client.settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- Profile photo -->
        <div class="mb-4 d-flex align-items-center gap-3">
            @if($user->avatar_url)
                <img src="{{ $user->avatar_url }}" alt="avatar" class="rounded-circle border border-3 border-light shadow-sm" style="width: 80px; height: 80px; object-fit: cover;">
            @else
                <div class="rounded-circle bg-dark text-white d-flex align-items-center justify-content-center fw-bold border border-3 border-light shadow-sm" style="width: 80px; height: 80px; font-size: 28px;">
                    {{ strtoupper(substr($user->first_name ?? 'U', 0, 1)) }}
                </div>
            @endif
            <div>
                <label for="avatar" class="form-label small fw-medium mb-1">Profile Picture</label>
                <input type="file" name="avatar" id="avatar" class="form-control form-control-sm border-0 bg-light rounded-3">
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-sm-6">
                <label for="first_name" class="form-label small fw-medium">First Name</label>
                <input type="text" name="first_name" id="first_name" class="form-control rounded-3 @error('first_name') is-invalid @enderror" value="{{ old('first_name', $user->first_name) }}" required>
                @error('first_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-sm-6">
                <label for="last_name" class="form-label small fw-medium">Last Name</label>
                <input type="text" name="last_name" id="last_name" class="form-control rounded-3 @error('last_name') is-invalid @enderror" value="{{ old('last_name', $user->last_name) }}" required>
                @error('last_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label small fw-medium">Email Address (Read Only)</label>
            <input type="email" class="form-control rounded-3 bg-light text-muted" value="{{ $user->email }}" readonly>
        </div>

        <div class="mb-3">
            <label for="phone" class="form-label small fw-medium">Phone Number</label>
            <input type="text" name="phone" id="phone" class="form-control rounded-3 @error('phone') is-invalid @enderror" value="{{ old('phone', $user->phone) }}">
            @error('phone')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="row g-3 mb-4">
            <div class="col-sm-6">
                <label for="country" class="form-label small fw-medium">Country</label>
                <input type="text" name="country" id="country" class="form-control rounded-3" value="{{ old('country', $user->country) }}" placeholder="Nigeria">
            </div>
            <div class="col-sm-6">
                <label for="state" class="form-label small fw-medium">State / Region</label>
                <input type="text" name="state" id="state" class="form-control rounded-3" value="{{ old('state', $user->state) }}" placeholder="Lagos">
            </div>
        </div>

        <button type="submit" class="btn btn-dark rounded-pill px-4 py-2 small">Save Profile Changes</button>
    </form>
</div>
@endsection
