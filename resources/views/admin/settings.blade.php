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

    <!-- Payment Gateways & Currency Settings Form -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm p-4 rounded-4 bg-white mb-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h2 class="h5 fw-bold text-dark mb-0"><i class="bi bi-credit-card-2-front me-2 text-primary"></i> Payment Gateways &amp; Currency</h2>
                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1 small">
                    <i class="bi bi-shield-check me-1"></i> Live Settings
                </span>
            </div>
            
            <p class="text-secondary small mb-3">
                Select your active payment gateway, billing currency (NGN / USD), and configure API keys for Paystack and Credo.
            </p>

            <form action="{{ route('admin.settings.update') }}" method="POST">
                @csrf
                
                @php
                    $currentGateway = old('payment_gateway', $settings->payment_gateway ?? config('services.payment.gateway') ?: env('PAYMENT_GATEWAY', 'paystack'));
                    $currentCurrency = old('default_currency', $settings->default_currency ?? config('services.payment.currency') ?: env('PORTAL_BASE_CURRENCY', 'NGN'));
                    $paymentMode = old('payment_mode', $settings->payment_mode ?? config('services.credo.mode') ?: env('PAYMENT_MODE', 'live'));
                    $paystackPublicKey = old('paystack_public_key', $settings->paystack_public_key ?? config('services.paystack.public_key') ?: env('PAYSTACK_PUBLIC_KEY', ''));
                    $paystackSecretKey = old('paystack_secret_key', $settings->paystack_secret_key ?? config('services.paystack.secret_key') ?: env('PAYSTACK_SECRET_KEY', ''));
                    $credoPublicKey = old('credo_public_key', $settings->credo_public_key ?? config('services.credo.public_key') ?: env('CREDO_PUBLIC_KEY', ''));
                    $credoSecretKey = old('credo_secret_key', $settings->credo_secret_key ?? config('services.credo.secret_key') ?: env('CREDO_SECRET_KEY', ''));
                @endphp

                <div class="row g-3 mb-3">
                    <div class="col-sm-6">
                        <label for="payment_gateway" class="form-label small fw-medium">Primary Active Gateway</label>
                        <select name="payment_gateway" id="payment_gateway" class="form-select rounded-3 fw-semibold">
                            <option value="paystack" {{ strtolower($currentGateway) === 'paystack' ? 'selected' : '' }}>Paystack Gateway</option>
                            <option value="credo" {{ strtolower($currentGateway) === 'credo' ? 'selected' : '' }}>Credo Gateway</option>
                        </select>
                    </div>
                    <div class="col-sm-6">
                        <label for="default_currency" class="form-label small fw-medium">Billing Currency</label>
                        <select name="default_currency" id="default_currency" class="form-select rounded-3 fw-semibold">
                            <option value="NGN" {{ strtoupper($currentCurrency) === 'NGN' ? 'selected' : '' }}>NGN (₦ - Nigerian Naira)</option>
                            <option value="USD" {{ strtoupper($currentCurrency) === 'USD' ? 'selected' : '' }}>USD ($ - US Dollar)</option>
                        </select>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-sm-6">
                        <label for="payment_mode" class="form-label small fw-medium">Transaction Environment</label>
                        <select name="payment_mode" id="payment_mode" class="form-select rounded-3">
                            <option value="live" {{ strtolower($paymentMode) === 'live' ? 'selected' : '' }}>Live Environment</option>
                            <option value="test" {{ strtolower($paymentMode) === 'test' ? 'selected' : '' }}>Test / Sandbox</option>
                        </select>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label small fw-medium text-muted">Gateway Status</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-success"><i class="bi bi-check-circle-fill"></i></span>
                            <input type="text" class="form-control bg-light border-start-0 text-success fw-semibold small" value="Active &amp; Operational" readonly disabled>
                        </div>
                    </div>
                </div>

                <hr class="my-4 border-light">

                <!-- Paystack Credentials Header & Fields -->
                <div class="d-flex align-items-center gap-2 mb-3">
                    <i class="bi bi-wallet2 text-primary fs-5"></i>
                    <h3 class="h6 fw-bold text-dark mb-0">Paystack Gateway Credentials</h3>
                </div>

                <div class="mb-3">
                    <label for="paystack_public_key" class="form-label small fw-medium">Paystack Public Key</label>
                    <div class="input-group">
                        <input type="text" name="paystack_public_key" id="paystack_public_key" class="form-control font-monospace small" value="{{ $paystackPublicKey }}" placeholder="pk_live_...">
                        <span class="input-group-text bg-light text-muted"><i class="bi bi-key-fill"></i></span>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="paystack_secret_key" class="form-label small fw-medium">Paystack Secret Key</label>
                    <div class="input-group">
                        <input type="password" name="paystack_secret_key" id="paystack_secret_key" class="form-control font-monospace small" value="{{ $paystackSecretKey }}" placeholder="sk_live_...">
                        <button type="button" class="btn btn-outline-secondary btn-sm px-3" onclick="toggleEnvSecretVisibility('paystack_secret_key', this)">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                <hr class="my-4 border-light">

                <!-- Credo Credentials Header & Fields -->
                <div class="d-flex align-items-center gap-2 mb-3">
                    <i class="bi bi-box-arrow-up-right text-primary fs-5"></i>
                    <h3 class="h6 fw-bold text-dark mb-0">Credo Gateway Credentials</h3>
                </div>

                <div class="mb-3">
                    <label for="credo_public_key" class="form-label small fw-medium">Credo Public Key</label>
                    <div class="input-group">
                        <input type="text" name="credo_public_key" id="credo_public_key" class="form-control font-monospace small" value="{{ $credoPublicKey }}" placeholder="1PUB...">
                        <span class="input-group-text bg-light text-muted"><i class="bi bi-key-fill"></i></span>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="credo_secret_key" class="form-label small fw-medium">Credo Secret Key</label>
                    <div class="input-group">
                        <input type="password" name="credo_secret_key" id="credo_secret_key" class="form-control font-monospace small" value="{{ $credoSecretKey }}" placeholder="1PRI...">
                        <button type="button" class="btn btn-outline-secondary btn-sm px-3" onclick="toggleEnvSecretVisibility('credo_secret_key', this)">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-dark rounded-pill px-4 py-2 small fw-semibold">Save Gateway &amp; Currency Settings</button>
            </form>
        </div>
    </div>
</div>

<script>
function toggleEnvSecretVisibility(inputId, btn) {
    const input = document.getElementById(inputId);
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'bi bi-eye';
    }
}
</script>
@endsection
