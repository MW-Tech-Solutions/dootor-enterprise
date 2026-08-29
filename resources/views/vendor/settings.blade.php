@extends('layouts.dashboard')

@section('title', 'Storefront Settings - ' . ($settings->platform_name ?? 'Umar Maher'))

@section('content')
<div class="mb-4">
    <h1 class="h3 fw-bold text-dark mb-1">Storefront &amp; Profile Settings</h1>
    <p class="text-secondary small">Customize your public storefront layout, brand theme, and manager profile details</p>
</div>

<div class="row g-4">
    <!-- Storefront Customizer -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
            <h2 class="h5 fw-bold text-dark mb-4"><i class="bi bi-shop me-2"></i> Customize Public Storefront</h2>
            
            <form action="{{ route('vendor.settings.storefront') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="mb-3">
                    <label for="storefront_name" class="form-label small fw-medium">Storefront Hub Name</label>
                    <input type="text" name="storefront_name" id="storefront_name" class="form-control rounded-3" value="{{ old('storefront_name', $storefront->storefront_name) }}" placeholder="e.g. Johnathan Doc Express" required>
                </div>

                <div class="mb-3">
                    <label for="storefront_about" class="form-label small fw-medium">Storefront About Bio</label>
                    <textarea name="storefront_about" id="storefront_about" class="form-control rounded-3" rows="3" placeholder="Tell clients about your services, processing timelines, expertise...">{{ old('storefront_about', $storefront->storefront_about) }}</textarea>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-sm-6">
                        <label for="theme" class="form-label small fw-medium">Storefront Base Theme</label>
                        <select name="theme" id="theme" class="form-select rounded-3">
                            <option value="light" {{ $storefront->theme === 'light' ? 'selected' : '' }}>Light Theme</option>
                            <option value="dark" {{ $storefront->theme === 'dark' ? 'selected' : '' }}>Dark Theme</option>
                        </select>
                    </div>
                    <div class="col-sm-6">
                        <label for="color_palette" class="form-label small fw-medium">Brand Accent Palette</label>
                        <select name="color_palette" id="color_palette" class="form-select rounded-3">
                            <option value="default" {{ $storefront->color_palette === 'default' ? 'selected' : '' }}>Default Blue-Light</option>
                            <option value="blue" {{ $storefront->color_palette === 'blue' ? 'selected' : '' }}>Vibrant Blue</option>
                            <option value="green" {{ $storefront->color_palette === 'green' ? 'selected' : '' }}>Forest Green</option>
                            <option value="purple" {{ $storefront->color_palette === 'purple' ? 'selected' : '' }}>Royal Purple</option>
                            <option value="red" {{ $storefront->color_palette === 'red' ? 'selected' : '' }}>Coral Red</option>
                            <option value="orange" {{ $storefront->color_palette === 'orange' ? 'selected' : '' }}>Sunset Orange</option>
                            <option value="yellow" {{ $storefront->color_palette === 'yellow' ? 'selected' : '' }}>Sunny Yellow</option>
                            <option value="teal" {{ $storefront->color_palette === 'teal' ? 'selected' : '' }}>Deep Teal</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="gradient" class="form-label small fw-medium">Banner CSS Gradient (Optional - overrides theme default)</label>
                    <input type="text" name="gradient" id="gradient" class="form-control rounded-3" value="{{ old('gradient', $storefront->gradient) }}" placeholder="e.g. linear-gradient(135deg, #11998e, #38ef7d)">
                </div>

                <div class="mb-4">
                    <label for="background_image" class="form-label small fw-medium">Storefront Hero Banner Image</label>
                    @if($storefront->background_image_url)
                        <div class="mb-2">
                            <img src="{{ $storefront->background_image_url }}" alt="banner" class="img-fluid rounded" style="max-height: 100px; object-fit: cover;">
                        </div>
                    @endif
                    <input type="file" name="background_image" id="background_image" class="form-control form-control-sm border-0 bg-light rounded-3">
                </div>

                <button type="submit" class="btn btn-dark rounded-pill px-4">Save Storefront Settings</button>
            </form>
        </div>
    </div>

    <!-- Manager profile settings -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm p-4 rounded-4 bg-white mb-4">
            <h2 class="h5 fw-bold text-dark mb-4"><i class="bi bi-person me-2"></i> Update Manager Profile</h2>
            
            <form action="{{ route('vendor.settings.profile') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="mb-4 d-flex align-items-center gap-3">
                    @if($user->avatar_url)
                        <img src="{{ $user->avatar_url }}" alt="avatar" class="rounded-circle border border-3 border-light shadow-sm" style="width: 70px; height: 70px; object-fit: cover;">
                    @else
                        <div class="rounded-circle bg-dark text-white d-flex align-items-center justify-content-center fw-bold border border-3 border-light shadow-sm" style="width: 70px; height: 70px; font-size: 24px;">
                            {{ strtoupper(substr($user->first_name ?? 'V', 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        <label for="avatar" class="form-label small fw-medium mb-1">Avatar Image</label>
                        <input type="file" name="avatar" id="avatar" class="form-control form-control-sm border-0 bg-light rounded-3">
                    </div>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-sm-6">
                        <label for="first_name" class="form-label small fw-medium">First Name</label>
                        <input type="text" name="first_name" id="first_name" class="form-control rounded-3" value="{{ old('first_name', $user->first_name) }}" required>
                    </div>
                    <div class="col-sm-6">
                        <label for="last_name" class="form-label small fw-medium">Last Name</label>
                        <input type="text" name="last_name" id="last_name" class="form-control rounded-3" value="{{ old('last_name', $user->last_name) }}" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="phone" class="form-label small fw-medium">Phone Number</label>
                    <input type="text" name="phone" id="phone" class="form-control rounded-3" value="{{ old('phone', $user->phone) }}">
                </div>

                <div class="row g-2 mb-4">
                    <div class="col-sm-6">
                        <label for="country" class="form-label small fw-medium">Country</label>
                        <input type="text" name="country" id="country" class="form-control rounded-3" value="{{ old('country', $user->country) }}">
                    </div>
                    <div class="col-sm-6">
                        <label for="state" class="form-label small fw-medium">State</label>
                        <input type="text" name="state" id="state" class="form-control rounded-3" value="{{ old('state', $user->state) }}">
                    </div>
                </div>

                <button type="submit" class="btn btn-dark rounded-pill px-4">Update Profile</button>
            </form>
        </div>
        
        @if($user->status === 'Approved')
            <div class="card border-0 bg-light p-4 rounded-4 shadow-sm text-center">
                <span class="small fw-semibold text-secondary d-block mb-2">View Your Public Web Hub</span>
                <a href="{{ route('vendor.storefront', $user->id) }}" target="_blank" class="btn btn-outline-dark rounded-pill px-4 btn-sm">Visit Storefront <i class="bi bi-box-arrow-up-right ms-1"></i></a>
            </div>
        @endif
    </div>
</div>
@endsection
