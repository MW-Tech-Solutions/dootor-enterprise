@extends('layouts.dashboard')

@section('title', 'System Settings - ' . ($settings->platform_name ?? 'Dooter Enterprises'))

@section('content')
<div class="mb-4">
    <h1 class="h3 fw-bold text-dark mb-1">System Settings</h1>
    <p class="text-secondary small">Configure white-label branding layout configurations, currency, billing key gates, and email configurations</p>
</div>

<div class="row g-4">
    <!-- White-Label Branding Settings Form -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm p-4 rounded-4 bg-white mb-4">
            <h2 class="h5 fw-bold text-dark mb-4"><i class="bi bi-palette me-2"></i> Branding &amp; Visual Design</h2>
            
            <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="row g-3 mb-3">
                    <div class="col-sm-6">
                        <label for="platform_name" class="form-label small fw-medium">Platform Display Name</label>
                        <input type="text" name="platform_name" id="platform_name" class="form-control rounded-3" value="{{ old('platform_name', $settings->platform_name) }}" required>
                    </div>
                    <div class="col-sm-6">
                        <label for="company_name" class="form-label small fw-medium">Official Company Name</label>
                        <input type="text" name="company_name" id="company_name" class="form-control rounded-3" value="{{ old('company_name', $settings->company_name) }}" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="logo_file" class="form-label small fw-medium">Upload Platform Logo</label>
                    @if($settings->logo_url)
                    <div class="mb-2 d-flex align-items-center gap-3 p-2 border rounded-3 bg-light">
                        <img src="{{ asset($settings->logo_url) }}" alt="Current Logo" class="rounded" style="max-height: 40px; max-width: 120px; object-fit: contain;">
                        <span class="small text-muted">Current Logo Active</span>
                    </div>
                    @endif
                    <input type="file" name="logo_file" id="logo_file" class="form-control rounded-3" accept="image/*">
                    <small class="text-muted d-block mt-1">Recommended format: PNG, SVG, WEBP, or JPG (Max 5MB)</small>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-sm-4">
                        <label for="brand_theme" class="form-label small fw-medium">Dashboard Base Mode</label>
                        <select name="brand_theme" id="brand_theme" class="form-select rounded-3">
                            <option value="light" {{ $settings->brand_theme === 'light' ? 'selected' : '' }}>Light</option>
                            <option value="dark" {{ $settings->brand_theme === 'dark' ? 'selected' : '' }}>Dark (Glassmorphism)</option>
                        </select>
                    </div>
                    <div class="col-sm-4">
                        <label for="brand_color_palette" class="form-label small fw-medium">System Palette</label>
                        <select name="brand_color_palette" id="brand_color_palette" class="form-select rounded-3">
                            <option value="default" {{ $settings->brand_color_palette === 'default' ? 'selected' : '' }}>Default Blue</option>
                            <option value="blue" {{ $settings->brand_color_palette === 'blue' ? 'selected' : '' }}>Vibrant Blue</option>
                            <option value="green" {{ $settings->brand_color_palette === 'green' ? 'selected' : '' }}>Forest Green</option>
                            <option value="purple" {{ $settings->brand_color_palette === 'purple' ? 'selected' : '' }}>Royal Purple</option>
                            <option value="red" {{ $settings->brand_color_palette === 'red' ? 'selected' : '' }}>Coral Red</option>
                            <option value="orange" {{ $settings->brand_color_palette === 'orange' ? 'selected' : '' }}>Sunset Orange</option>
                            <option value="yellow" {{ $settings->brand_color_palette === 'yellow' ? 'selected' : '' }}>Sunny Yellow</option>
                            <option value="teal" {{ $settings->brand_color_palette === 'teal' ? 'selected' : '' }}>Deep Teal</option>
                        </select>
                    </div>
                    <div class="col-sm-4">
                        <label for="brand_template" class="form-label small fw-medium">Layout Style</label>
                        <select name="brand_template" id="brand_template" class="form-select rounded-3">
                            <option value="classic" {{ $settings->brand_template === 'classic' ? 'selected' : '' }}>Classic Admin</option>
                            <option value="editorial" {{ $settings->brand_template === 'editorial' ? 'selected' : '' }}>Editorial Bold</option>
                            <option value="compact" {{ $settings->brand_template === 'compact' ? 'selected' : '' }}>Compact clean</option>
                            <option value="showcase" {{ $settings->brand_template === 'showcase' ? 'selected' : '' }}>Showcase grid</option>
                        </select>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-sm-6">
                        <label for="brand_primary_color" class="form-label small fw-medium">Primary Accent Color (Hex)</label>
                        <input type="color" name="brand_primary_color" id="brand_primary_color" class="form-control form-control-color w-100 rounded-3" value="{{ old('brand_primary_color', $settings->brand_primary_color ?? '#2091eb') }}">
                    </div>
                    <div class="col-sm-6">
                        <label for="brand_secondary_color" class="form-label small fw-medium">Secondary Accent Color (Hex)</label>
                        <input type="color" name="brand_secondary_color" id="brand_secondary_color" class="form-control form-control-color w-100 rounded-3" value="{{ old('brand_secondary_color', $settings->brand_secondary_color ?? '#c2e59c') }}">
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-sm-6">
                        <label for="brand_gradient_from" class="form-label small fw-medium">Banner Gradient Start (Hex)</label>
                        <input type="text" name="brand_gradient_from" id="brand_gradient_from" class="form-control rounded-3" value="{{ old('brand_gradient_from', $settings->brand_gradient_from ?? '#64b3f4') }}" placeholder="#...">
                    </div>
                    <div class="col-sm-6">
                        <label for="brand_gradient_to" class="form-label small fw-medium">Banner Gradient End (Hex)</label>
                        <input type="text" name="brand_gradient_to" id="brand_gradient_to" class="form-control rounded-3" value="{{ old('brand_gradient_to', $settings->brand_gradient_to ?? '#c2e59c') }}" placeholder="#...">
                    </div>
                </div>

                <button type="submit" class="btn btn-dark rounded-pill px-4 py-2 small">Save Visual Branding</button>
            </form>
        </div>

        <!-- Landing Hero & Wallpaper Settings -->
        <div class="card border-0 shadow-sm p-4 rounded-4 bg-white mb-4">
            <h2 class="h5 fw-bold text-dark mb-4"><i class="bi bi-image me-2"></i> Landing Page Hero &amp; Wallpaper</h2>
            
            <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="mb-3">
                    <label for="hero_badge_text" class="form-label small fw-medium">Hero Badge Tagline</label>
                    <input type="text" name="hero_badge_text" id="hero_badge_text" class="form-control rounded-3" value="{{ old('hero_badge_text', $settings->hero_badge_text ?? 'Streamlined Document Support') }}" placeholder="e.g. Streamlined Document Support">
                </div>

                <div class="mb-3">
                    <label for="hero_title" class="form-label small fw-medium">Hero Heading Title</label>
                    <input type="text" name="hero_title" id="hero_title" class="form-control rounded-3" value="{{ old('hero_title', $settings->hero_title ?? 'All Business & Personal Services In One Place') }}" placeholder="e.g. All Business & Personal Services In One Place">
                </div>

                <div class="mb-3">
                    <label for="hero_subtitle" class="form-label small fw-medium">Hero Subtitle / Description</label>
                    <textarea name="hero_subtitle" id="hero_subtitle" class="form-control rounded-3" rows="3" placeholder="Description sentence...">{{ old('hero_subtitle', $settings->hero_subtitle ?? 'Fast-track passport approvals, visa handling, NIN verification, court affidavits, and more. A secured portal for direct processing and verified document handling.') }}</textarea>
                </div>

                <div class="mb-3">
                    <label for="hero_bg_file" class="form-label small fw-medium">Hero Background Wallpaper</label>
                    @if($settings->hero_bg_image)
                    <div class="mb-2 p-2 border rounded-3 bg-light d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3 overflow-hidden">
                            <img src="{{ asset($settings->hero_bg_image) }}" alt="Hero Wallpaper" class="rounded" style="height: 50px; width: 80px; object-fit: cover;">
                            <span class="small text-dark fw-medium text-truncate">Wallpaper Active</span>
                        </div>
                        <div class="form-check me-2">
                            <input class="form-check-input" type="checkbox" name="remove_hero_bg" value="1" id="remove_hero_bg">
                            <label class="form-check-input-label small text-danger fw-semibold" for="remove_hero_bg">Remove Image</label>
                        </div>
                    </div>
                    @endif
                    <input type="file" name="hero_bg_file" id="hero_bg_file" class="form-control rounded-3" accept="image/*">
                    <small class="text-muted d-block mt-1">Upload custom wallpaper (PNG, JPG, WEBP - Max 10MB). Overlaid with frosted glass effect on landing page.</small>
                </div>

                <div class="mb-4">
                    <label for="hero_glass_style" class="form-label small fw-medium">Hero Glassmorphism Panel Overlay</label>
                    <select name="hero_glass_style" id="hero_glass_style" class="form-select rounded-3">
                        <option value="light_glass" {{ ($settings->hero_glass_style ?? 'light_glass') === 'light_glass' ? 'selected' : '' }}>Light Frosted Glass (75% White Translucent)</option>
                        <option value="dark_glass" {{ ($settings->hero_glass_style ?? '') === 'dark_glass' ? 'selected' : '' }}>Dark Obsidian Glass (80% Dark Translucent)</option>
                        <option value="emerald_glass" {{ ($settings->hero_glass_style ?? '') === 'emerald_glass' ? 'selected' : '' }}>Emerald Gold Glass (Deep Green & Gold Tint)</option>
                        <option value="subtle_glass" {{ ($settings->hero_glass_style ?? '') === 'subtle_glass' ? 'selected' : '' }}>Subtle Translucent Glass (Minimal Overlay)</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-dark rounded-pill px-4 py-2 small">Save Hero Settings</button>
            </form>
        </div>
    </div>

    <!-- Payout Gateway & Key configurations -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm p-4 rounded-4 bg-white mb-4">
            <h2 class="h5 fw-bold text-dark mb-4"><i class="bi bi-wallet2 me-2"></i> Escrow Payment Gateways</h2>
            
            <form action="{{ route('admin.settings.update') }}" method="POST">
                @csrf
                
                <div class="row g-3 mb-3">
                    <div class="col-sm-6">
                        <label for="default_currency" class="form-label small fw-medium">Billing Currency</label>
                        <input type="text" name="default_currency" id="default_currency" class="form-control rounded-3" value="{{ old('default_currency', $settings->default_currency) }}" required>
                    </div>
                    <div class="col-sm-6">
                        <label for="payment_gateway" class="form-label small fw-medium">Primary Gateway</label>
                        <select name="payment_gateway" id="payment_gateway" class="form-select rounded-3">
                            <option value="paystack" {{ $settings->payment_gateway === 'paystack' ? 'selected' : '' }}>Paystack</option>
                            <option value="credo" {{ $settings->payment_gateway === 'credo' ? 'selected' : '' }}>Credo Gateway</option>
                        </select>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-sm-6">
                        <label for="payment_mode" class="form-label small fw-medium">Transaction Mode</label>
                        <select name="payment_mode" id="payment_mode" class="form-select rounded-3">
                            <option value="test" {{ $settings->payment_mode === 'test' ? 'selected' : '' }}>Test Sandbox</option>
                            <option value="live" {{ $settings->payment_mode === 'live' ? 'selected' : '' }}>Live Production</option>
                        </select>
                    </div>
                    <div class="col-sm-6">
                        <label for="payments_enabled" class="form-label small fw-medium">Billing Gate Status</label>
                        <select name="payments_enabled" id="payments_enabled" class="form-select rounded-3">
                            <option value="1" {{ $settings->payments_enabled ? 'selected' : '' }}>Online Processing</option>
                            <option value="0" {{ !$settings->payments_enabled ? 'selected' : '' }}>Disabled</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="paystack_public_key" class="form-label small fw-medium">Paystack Public Key</label>
                    <input type="password" name="paystack_public_key" id="paystack_public_key" class="form-control rounded-3" value="{{ old('paystack_public_key', $settings->paystack_public_key) }}" placeholder="pk_test_...">
                </div>

                <div class="mb-3">
                    <label for="paystack_secret_key" class="form-label small fw-medium">Paystack Secret Key</label>
                    <input type="password" name="paystack_secret_key" id="paystack_secret_key" class="form-control rounded-3" value="{{ old('paystack_secret_key', $settings->paystack_secret_key) }}" placeholder="sk_test_...">
                </div>
                
                <hr class="my-4 border-light">

                <button type="submit" class="btn btn-dark rounded-pill px-4 py-2 small w-100">Save Escrow Gateways</button>
            </form>
        </div>
    </div>
</div>
@endsection
