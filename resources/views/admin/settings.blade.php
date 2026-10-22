@extends('layouts.dashboard')

@section('title', 'System Settings - ' . ($settings->platform_name ?? 'Dooter Enterprises'))

@section('styles')
<style>
    .settings-nav-tabs .nav-link {
        color: #475569;
        font-weight: 600;
        font-size: 14px;
        padding: 12px 20px;
        border-radius: 10px;
        border: 1px solid transparent;
        transition: all 0.2s ease;
    }
    .settings-nav-tabs .nav-link:hover {
        background: #f1f5f9;
        color: #004225;
    }
    .settings-nav-tabs .nav-link.active {
        background: #004225 !important;
        color: #ffffff !important;
        box-shadow: 0 4px 12px rgba(0, 66, 37, 0.2);
    }
    .social-input-icon {
        width: 44px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }
    .ck-editor__editable_inline {
        min-height: 250px !important;
        border-radius: 0 0 12px 12px !important;
    }
    .ck-toolbar {
        border-radius: 12px 12px 0 0 !important;
        background: #f8fafc !important;
    }
</style>
@endsection

@section('content')
<div class="mb-4">
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">System Settings</h1>
            <p class="text-secondary small mb-0">Configure branding, landing hero, dynamic social media links, payment gateways, and legal policies</p>
        </div>
        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1.5 small">
            <i class="bi bi-shield-check me-1"></i> Multi-Section Settings
        </span>
    </div>
</div>

<!-- Settings Navigation Tabs Header -->
<div class="card border-0 shadow-sm p-3 rounded-4 bg-white mb-4">
    <ul class="nav nav-pills settings-nav-tabs flex-wrap gap-2" id="settingsTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="tab-branding-btn" data-bs-toggle="pill" data-bs-target="#tab-branding" type="button" role="tab" aria-controls="tab-branding" aria-selected="true">
                <i class="bi bi-palette me-1.5"></i> Branding &amp; Visuals
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-hero-btn" data-bs-toggle="pill" data-bs-target="#tab-hero" type="button" role="tab" aria-controls="tab-hero" aria-selected="false">
                <i class="bi bi-image me-1.5"></i> Hero &amp; Wallpaper
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-social-btn" data-bs-toggle="pill" data-bs-target="#tab-social" type="button" role="tab" aria-controls="tab-social" aria-selected="false">
                <i class="bi bi-share me-1.5"></i> Social Media &amp; Contact
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-payments-btn" data-bs-toggle="pill" data-bs-target="#tab-payments" type="button" role="tab" aria-controls="tab-payments" aria-selected="false">
                <i class="bi bi-credit-card-2-front me-1.5"></i> Payment Gateways
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-legal-btn" data-bs-toggle="pill" data-bs-target="#tab-legal" type="button" role="tab" aria-controls="tab-legal" aria-selected="false">
                <i class="bi bi-shield-check me-1.5"></i> Legal Policies
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-expirations-btn" data-bs-toggle="pill" data-bs-target="#tab-expirations" type="button" role="tab" aria-controls="tab-expirations" aria-selected="false">
                <i class="bi bi-hourglass-split me-1.5"></i> App Expirations
            </button>
        </li>
    </ul>
</div>

<!-- Tab Content Container -->
<div class="tab-content" id="settingsTabsContent">

    <!-- TAB 1: BRANDING & VISUAL DESIGN -->
    <div class="tab-pane fade show active" id="tab-branding" role="tabpanel" aria-labelledby="tab-branding-btn">
        <div class="card border-0 shadow-sm p-4 p-sm-5 rounded-4 bg-white">
            <div class="border-bottom pb-3 mb-4">
                <h2 class="h5 fw-bold text-dark mb-1"><i class="bi bi-palette me-2 text-success"></i> Branding &amp; Visual Design</h2>
                <p class="text-secondary small mb-0">Customize your platform display name, logos, system themes, color palettes, and header banner gradients.</p>
            </div>

            <form action="{{ route('admin.settings.update') }}#tab-branding" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="row g-3 mb-3">
                    <div class="col-sm-6">
                        <label for="platform_name" class="form-label small fw-semibold">Platform Display Name</label>
                        <input type="text" name="platform_name" id="platform_name" class="form-control rounded-3" value="{{ old('platform_name', $settings->platform_name) }}" required>
                    </div>
                    <div class="col-sm-6">
                        <label for="company_name" class="form-label small fw-semibold">Official Company Name</label>
                        <input type="text" name="company_name" id="company_name" class="form-control rounded-3" value="{{ old('company_name', $settings->company_name) }}" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="logo_file" class="form-label small fw-semibold">Upload Platform Logo</label>
                    @if($settings->logo_url)
                    <div class="mb-2 d-flex align-items-center gap-3 p-2.5 border rounded-3 bg-light">
                        <img src="{{ asset($settings->logo_url) }}" alt="Current Logo" class="rounded" style="max-height: 42px; max-width: 140px; object-fit: contain;">
                        <span class="small text-success fw-medium"><i class="bi bi-check-circle-fill me-1"></i> Current Logo Active</span>
                    </div>
                    @endif
                    <input type="file" name="logo_file" id="logo_file" class="form-control rounded-3" accept="image/*">
                    <small class="text-muted d-block mt-1">Recommended format: PNG, SVG, WEBP, or JPG (Max 5MB)</small>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-sm-4">
                        <label for="brand_theme" class="form-label small fw-semibold">Dashboard Base Mode</label>
                        <select name="brand_theme" id="brand_theme" class="form-select rounded-3">
                            <option value="light" {{ $settings->brand_theme === 'light' ? 'selected' : '' }}>Light Mode</option>
                            <option value="dark" {{ $settings->brand_theme === 'dark' ? 'selected' : '' }}>Dark (Glassmorphism)</option>
                        </select>
                    </div>
                    <div class="col-sm-4">
                        <label for="brand_color_palette" class="form-label small fw-semibold">System Palette</label>
                        <select name="brand_color_palette" id="brand_color_palette" class="form-select rounded-3">
                            <option value="default" {{ $settings->brand_color_palette === 'default' ? 'selected' : '' }}>Default Blue</option>
                            <option value="green" {{ $settings->brand_color_palette === 'green' ? 'selected' : '' }}>Forest Green</option>
                            <option value="blue" {{ $settings->brand_color_palette === 'blue' ? 'selected' : '' }}>Vibrant Blue</option>
                            <option value="purple" {{ $settings->brand_color_palette === 'purple' ? 'selected' : '' }}>Royal Purple</option>
                            <option value="red" {{ $settings->brand_color_palette === 'red' ? 'selected' : '' }}>Coral Red</option>
                            <option value="orange" {{ $settings->brand_color_palette === 'orange' ? 'selected' : '' }}>Sunset Orange</option>
                            <option value="yellow" {{ $settings->brand_color_palette === 'yellow' ? 'selected' : '' }}>Sunny Yellow</option>
                            <option value="teal" {{ $settings->brand_color_palette === 'teal' ? 'selected' : '' }}>Deep Teal</option>
                        </select>
                    </div>
                    <div class="col-sm-4">
                        <label for="brand_template" class="form-label small fw-semibold">Layout Style</label>
                        <select name="brand_template" id="brand_template" class="form-select rounded-3">
                            <option value="classic" {{ $settings->brand_template === 'classic' ? 'selected' : '' }}>Classic Admin</option>
                            <option value="editorial" {{ $settings->brand_template === 'editorial' ? 'selected' : '' }}>Editorial Bold</option>
                            <option value="compact" {{ $settings->brand_template === 'compact' ? 'selected' : '' }}>Compact Clean</option>
                            <option value="showcase" {{ $settings->brand_template === 'showcase' ? 'selected' : '' }}>Showcase Grid</option>
                        </select>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-sm-6">
                        <label for="brand_primary_color" class="form-label small fw-semibold">Primary Accent Color (Hex)</label>
                        <input type="color" name="brand_primary_color" id="brand_primary_color" class="form-control form-control-color w-100 rounded-3" value="{{ old('brand_primary_color', $settings->brand_primary_color ?? '#004225') }}">
                    </div>
                    <div class="col-sm-6">
                        <label for="brand_secondary_color" class="form-label small fw-semibold">Secondary Accent Color (Hex)</label>
                        <input type="color" name="brand_secondary_color" id="brand_secondary_color" class="form-control form-control-color w-100 rounded-3" value="{{ old('brand_secondary_color', $settings->brand_secondary_color ?? '#d4af37') }}">
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-sm-6">
                        <label for="brand_gradient_from" class="form-label small fw-semibold">Banner Gradient Start (Hex)</label>
                        <input type="text" name="brand_gradient_from" id="brand_gradient_from" class="form-control rounded-3" value="{{ old('brand_gradient_from', $settings->brand_gradient_from ?? '#004225') }}" placeholder="#004225">
                    </div>
                    <div class="col-sm-6">
                        <label for="brand_gradient_to" class="form-label small fw-semibold">Banner Gradient End (Hex)</label>
                        <input type="text" name="brand_gradient_to" id="brand_gradient_to" class="form-control rounded-3" value="{{ old('brand_gradient_to', $settings->brand_gradient_to ?? '#00703c') }}" placeholder="#00703c">
                    </div>
                </div>

                <div class="d-flex justify-content-end pt-3 border-top">
                    <button type="submit" class="btn btn-success rounded-pill px-4 py-2.5 fw-semibold shadow-sm" style="background-color: #004225; border: none;">
                        <i class="bi bi-check2-circle me-1.5"></i> Save Visual Branding
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- TAB 2: LANDING HERO & WALLPAPER -->
    <div class="tab-pane fade" id="tab-hero" role="tabpanel" aria-labelledby="tab-hero-btn">
        <div class="card border-0 shadow-sm p-4 p-sm-5 rounded-4 bg-white">
            <div class="border-bottom pb-3 mb-4">
                <h2 class="h5 fw-bold text-dark mb-1"><i class="bi bi-image me-2 text-success"></i> Landing Page Hero &amp; Wallpaper</h2>
                <p class="text-secondary small mb-0">Configure the public landing hero banner headlines, taglines, wallpaper backdrop, and frosted glass style.</p>
            </div>

            <form action="{{ route('admin.settings.update') }}#tab-hero" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="mb-3">
                    <label for="hero_badge_text" class="form-label small fw-semibold">Hero Badge Tagline</label>
                    <input type="text" name="hero_badge_text" id="hero_badge_text" class="form-control rounded-3" value="{{ old('hero_badge_text', $settings->hero_badge_text ?? 'Streamlined Pre-Enrollment & Document Support') }}" placeholder="e.g. Streamlined Pre-Enrollment Support">
                </div>

                <div class="mb-3">
                    <label for="hero_title" class="form-label small fw-semibold">Hero Heading Title</label>
                    <input type="text" name="hero_title" id="hero_title" class="form-control rounded-3" value="{{ old('hero_title', $settings->hero_title ?? 'All Business & Personal Services In One Place') }}" placeholder="e.g. All Business & Personal Services In One Place">
                </div>

                <div class="mb-3">
                    <label for="hero_subtitle" class="form-label small fw-semibold">Hero Subtitle / Description</label>
                    <textarea name="hero_subtitle" id="hero_subtitle" class="form-control rounded-3" rows="3" placeholder="Description sentence...">{{ old('hero_subtitle', $settings->hero_subtitle ?? 'Fast-track passport approvals, visa handling, NIN verification, court affidavits, and more. A secured portal for direct processing and verified document handling.') }}</textarea>
                </div>

                <div class="mb-3">
                    <label for="hero_bg_file" class="form-label small fw-semibold">Hero Background Wallpaper</label>
                    @if($settings->hero_bg_image)
                    <div class="mb-2 p-2.5 border rounded-3 bg-light d-flex align-items-center justify-content-between">
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
                    <label for="hero_glass_style" class="form-label small fw-semibold">Hero Glassmorphism Panel Overlay</label>
                    <select name="hero_glass_style" id="hero_glass_style" class="form-select rounded-3">
                        <option value="light_glass" {{ ($settings->hero_glass_style ?? 'light_glass') === 'light_glass' ? 'selected' : '' }}>Light Frosted Glass (75% White Translucent)</option>
                        <option value="dark_glass" {{ ($settings->hero_glass_style ?? '') === 'dark_glass' ? 'selected' : '' }}>Dark Obsidian Glass (80% Dark Translucent)</option>
                        <option value="emerald_glass" {{ ($settings->hero_glass_style ?? '') === 'emerald_glass' ? 'selected' : '' }}>Emerald Gold Glass (Deep Green &amp; Gold Tint)</option>
                        <option value="subtle_glass" {{ ($settings->hero_glass_style ?? '') === 'subtle_glass' ? 'selected' : '' }}>Subtle Translucent Glass (Minimal Overlay)</option>
                    </select>
                </div>

                <div class="d-flex justify-content-end pt-3 border-top">
                    <button type="submit" class="btn btn-success rounded-pill px-4 py-2.5 fw-semibold shadow-sm" style="background-color: #004225; border: none;">
                        <i class="bi bi-check2-circle me-1.5"></i> Save Hero Settings
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- TAB 3: SOCIAL MEDIA & CONTACT INFORMATION -->
    <div class="tab-pane fade" id="tab-social" role="tabpanel" aria-labelledby="tab-social-btn">
        <div class="card border-0 shadow-sm p-4 p-sm-5 rounded-4 bg-white">
            <div class="border-bottom pb-3 mb-4">
                <h2 class="h5 fw-bold text-dark mb-1"><i class="bi bi-share me-2 text-success"></i> Social Media Handles &amp; Contact Details</h2>
                <p class="text-secondary small mb-0">Manage all official social media profile URLs and public contact information displayed dynamically across the website and footer.</p>
            </div>

            <form action="{{ route('admin.settings.update') }}#tab-social" method="POST">
                @csrf

                <!-- Social Handles Section -->
                <h6 class="fw-bold text-dark mb-3"><i class="bi bi-globe2 me-2 text-primary"></i> Official Social Media Handles</h6>
                <div class="row g-3 mb-4">
                    <!-- Facebook -->
                    <div class="col-md-6">
                        <label for="facebook_url" class="form-label small fw-semibold">Facebook URL</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-primary social-input-icon"><i class="bi bi-facebook"></i></span>
                            <input type="url" name="facebook_url" id="facebook_url" class="form-control rounded-end-3" value="{{ old('facebook_url', $settings->facebook_url) }}" placeholder="https://facebook.com/yourpage">
                        </div>
                    </div>

                    <!-- Twitter / X -->
                    <div class="col-md-6">
                        <label for="twitter_url" class="form-label small fw-semibold">Twitter / X URL</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-dark social-input-icon"><i class="bi bi-twitter-x"></i></span>
                            <input type="url" name="twitter_url" id="twitter_url" class="form-control rounded-end-3" value="{{ old('twitter_url', $settings->twitter_url) }}" placeholder="https://x.com/yourprofile">
                        </div>
                    </div>

                    <!-- Instagram -->
                    <div class="col-md-6">
                        <label for="instagram_url" class="form-label small fw-semibold">Instagram URL</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-danger social-input-icon"><i class="bi bi-instagram"></i></span>
                            <input type="url" name="instagram_url" id="instagram_url" class="form-control rounded-end-3" value="{{ old('instagram_url', $settings->instagram_url) }}" placeholder="https://instagram.com/yourprofile">
                        </div>
                    </div>

                    <!-- LinkedIn -->
                    <div class="col-md-6">
                        <label for="linkedin_url" class="form-label small fw-semibold">LinkedIn URL</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-info social-input-icon"><i class="bi bi-linkedin"></i></span>
                            <input type="url" name="linkedin_url" id="linkedin_url" class="form-control rounded-end-3" value="{{ old('linkedin_url', $settings->linkedin_url) }}" placeholder="https://linkedin.com/company/yourcompany">
                        </div>
                    </div>

                    <!-- WhatsApp -->
                    <div class="col-md-6">
                        <label for="whatsapp_number" class="form-label small fw-semibold">WhatsApp Number / Direct Link</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-success social-input-icon"><i class="bi bi-whatsapp"></i></span>
                            <input type="text" name="whatsapp_number" id="whatsapp_number" class="form-control rounded-end-3" value="{{ old('whatsapp_number', $settings->whatsapp_number) }}" placeholder="+1 (647) 123-4567 or wa.me/...">
                        </div>
                    </div>

                    <!-- YouTube -->
                    <div class="col-md-6">
                        <label for="youtube_url" class="form-label small fw-semibold">YouTube Channel URL</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-danger social-input-icon"><i class="bi bi-youtube"></i></span>
                            <input type="url" name="youtube_url" id="youtube_url" class="form-control rounded-end-3" value="{{ old('youtube_url', $settings->youtube_url) }}" placeholder="https://youtube.com/@yourchannel">
                        </div>
                    </div>

                    <!-- TikTok -->
                    <div class="col-md-6">
                        <label for="tiktok_url" class="form-label small fw-semibold">TikTok Profile URL</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-dark social-input-icon"><i class="bi bi-tiktok"></i></span>
                            <input type="url" name="tiktok_url" id="tiktok_url" class="form-control rounded-end-3" value="{{ old('tiktok_url', $settings->tiktok_url) }}" placeholder="https://tiktok.com/@yourhandle">
                        </div>
                    </div>
                </div>

                <hr class="my-4 border-light">

                <!-- Public Contact Information -->
                <h6 class="fw-bold text-dark mb-3"><i class="bi bi-envelope-at me-2 text-primary"></i> Public Contact Information</h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="contact_email" class="form-label small fw-semibold">Customer Support Email</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted social-input-icon"><i class="bi bi-envelope-fill"></i></span>
                            <input type="email" name="contact_email" id="contact_email" class="form-control rounded-end-3" value="{{ old('contact_email', $settings->contact_email) }}" placeholder="support@dooterenterprises.com">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="contact_phone" class="form-label small fw-semibold">Official Phone Number</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted social-input-icon"><i class="bi bi-telephone-fill"></i></span>
                            <input type="text" name="contact_phone" id="contact_phone" class="form-control rounded-end-3" value="{{ old('contact_phone', $settings->contact_phone) }}" placeholder="+1 (437) 819-7431">
                        </div>
                    </div>

                    <div class="col-12">
                        <label for="contact_address" class="form-label small fw-semibold">Physical Office Address</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted social-input-icon"><i class="bi bi-geo-alt-fill"></i></span>
                            <input type="text" name="contact_address" id="contact_address" class="form-control rounded-end-3" value="{{ old('contact_address', $settings->contact_address) }}" placeholder="e.g. 100 University Ave, Toronto, ON, Canada">
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end pt-3 border-top">
                    <button type="submit" class="btn btn-success rounded-pill px-4 py-2.5 fw-semibold shadow-sm" style="background-color: #004225; border: none;">
                        <i class="bi bi-check2-circle me-1.5"></i> Save Social Media &amp; Contact Details
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- TAB 4: PAYMENT GATEWAYS & CURRENCY -->
    <div class="tab-pane fade" id="tab-payments" role="tabpanel" aria-labelledby="tab-payments-btn">
        <div class="card border-0 shadow-sm p-4 p-sm-5 rounded-4 bg-white">
            <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-4">
                <div>
                    <h2 class="h5 fw-bold text-dark mb-1"><i class="bi bi-credit-card-2-front me-2 text-success"></i> Payment Gateways &amp; Currency</h2>
                    <p class="text-secondary small mb-0">Select your active payment gateway, billing currency (NGN / USD), and configure API keys for Paystack and Credo.</p>
                </div>
                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1.5 small">
                    <i class="bi bi-shield-check me-1"></i> Live Settings
                </span>
            </div>

            <form action="{{ route('admin.settings.update') }}#tab-payments" method="POST">
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
                        <label for="payment_gateway" class="form-label small fw-semibold">Primary Active Gateway</label>
                        <select name="payment_gateway" id="payment_gateway" class="form-select rounded-3 fw-semibold">
                            <option value="paystack" {{ strtolower($currentGateway) === 'paystack' ? 'selected' : '' }}>Paystack Gateway</option>
                            <option value="credo" {{ strtolower($currentGateway) === 'credo' ? 'selected' : '' }}>Credo Gateway</option>
                        </select>
                    </div>
                    <div class="col-sm-6">
                        <label for="default_currency" class="form-label small fw-semibold">Billing Currency</label>
                        <select name="default_currency" id="default_currency" class="form-select rounded-3 fw-semibold">
                            <option value="NGN" {{ strtoupper($currentCurrency) === 'NGN' ? 'selected' : '' }}>NGN (₦ - Nigerian Naira)</option>
                            <option value="USD" {{ strtoupper($currentCurrency) === 'USD' ? 'selected' : '' }}>USD ($ - US Dollar)</option>
                        </select>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-sm-6">
                        <label for="payment_mode" class="form-label small fw-semibold">Transaction Environment</label>
                        <select name="payment_mode" id="payment_mode" class="form-select rounded-3">
                            <option value="live" {{ strtolower($paymentMode) === 'live' ? 'selected' : '' }}>Live Environment</option>
                            <option value="test" {{ strtolower($paymentMode) === 'test' ? 'selected' : '' }}>Test / Sandbox</option>
                        </select>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label small fw-semibold text-muted">Gateway Operational Status</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-success"><i class="bi bi-check-circle-fill"></i></span>
                            <input type="text" class="form-control bg-light border-start-0 text-success fw-semibold small" value="Active &amp; Operational" readonly disabled>
                        </div>
                    </div>
                </div>

                <hr class="my-4 border-light">

                <!-- Paystack Credentials -->
                <div class="d-flex align-items-center gap-2 mb-3">
                    <i class="bi bi-wallet2 text-primary fs-5"></i>
                    <h3 class="h6 fw-bold text-dark mb-0">Paystack Gateway Credentials</h3>
                </div>

                <div class="mb-3">
                    <label for="paystack_public_key" class="form-label small fw-semibold">Paystack Public Key</label>
                    <div class="input-group">
                        <input type="text" name="paystack_public_key" id="paystack_public_key" class="form-control font-monospace small" value="{{ $paystackPublicKey }}" placeholder="pk_live_...">
                        <span class="input-group-text bg-light text-muted"><i class="bi bi-key-fill"></i></span>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="paystack_secret_key" class="form-label small fw-semibold">Paystack Secret Key</label>
                    <div class="input-group">
                        <input type="password" name="paystack_secret_key" id="paystack_secret_key" class="form-control font-monospace small" value="{{ $paystackSecretKey }}" placeholder="sk_live_...">
                        <button type="button" class="btn btn-outline-secondary btn-sm px-3" onclick="toggleEnvSecretVisibility('paystack_secret_key', this)">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                <hr class="my-4 border-light">

                <!-- Credo Credentials -->
                <div class="d-flex align-items-center gap-2 mb-3">
                    <i class="bi bi-box-arrow-up-right text-primary fs-5"></i>
                    <h3 class="h6 fw-bold text-dark mb-0">Credo Gateway Credentials</h3>
                </div>

                <div class="mb-3">
                    <label for="credo_public_key" class="form-label small fw-semibold">Credo Public Key</label>
                    <div class="input-group">
                        <input type="text" name="credo_public_key" id="credo_public_key" class="form-control font-monospace small" value="{{ $credoPublicKey }}" placeholder="1PUB...">
                        <span class="input-group-text bg-light text-muted"><i class="bi bi-key-fill"></i></span>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="credo_secret_key" class="form-label small fw-semibold">Credo Secret Key</label>
                    <div class="input-group">
                        <input type="password" name="credo_secret_key" id="credo_secret_key" class="form-control font-monospace small" value="{{ $credoSecretKey }}" placeholder="1PRI...">
                        <button type="button" class="btn btn-outline-secondary btn-sm px-3" onclick="toggleEnvSecretVisibility('credo_secret_key', this)">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="d-flex justify-content-end pt-3 border-top">
                    <button type="submit" class="btn btn-success rounded-pill px-4 py-2.5 fw-semibold shadow-sm" style="background-color: #004225; border: none;">
                        <i class="bi bi-check2-circle me-1.5"></i> Save Gateway &amp; Currency Settings
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- TAB 5: LEGAL & POLICIES -->
    <div class="tab-pane fade" id="tab-legal" role="tabpanel" aria-labelledby="tab-legal-btn">
        <div class="d-flex flex-column gap-4">
            
            <!-- Section 1: Terms & Conditions -->
            <div class="card border-0 shadow-sm p-4 p-sm-5 rounded-4 bg-white">
                <div class="border-bottom pb-3 mb-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <h2 class="h5 fw-bold text-dark mb-0">
                            <i class="bi bi-shield-check me-2 text-success"></i> Client Terms &amp; Conditions Editor
                        </h2>
                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1 small">
                            <i class="bi bi-pencil-square me-1"></i> Visual WYSIWYG Editor
                        </span>
                    </div>
                    <p class="text-secondary small mt-1 mb-0">Manage the dynamic Terms &amp; Conditions displayed inside the signup modal.</p>
                </div>
                
                <form action="{{ route('admin.settings.update') }}#tab-legal" method="POST">
                    @csrf
                    <div class="mb-4">
                        <textarea name="terms_conditions" id="terms_conditions_editor" class="form-control rounded-3">{{ old('terms_conditions', $settings->terms_conditions) }}</textarea>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-success rounded-pill px-4 py-2.5 fw-semibold shadow-sm" style="background-color: #004225; border: none;">
                            <i class="bi bi-check2-circle me-1.5"></i> Save Terms &amp; Conditions
                        </button>
                    </div>
                </form>
            </div>

            <!-- Section 2: Refund & Cancellation Policy -->
            <div class="card border-0 shadow-sm p-4 p-sm-5 rounded-4 bg-white">
                <div class="border-bottom pb-3 mb-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <h2 class="h5 fw-bold text-dark mb-0">
                            <i class="bi bi-cash-stack me-2 text-success"></i> Refund &amp; Cancellation Policy Editor
                        </h2>
                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1 small">
                            <i class="bi bi-pencil-square me-1"></i> Visual WYSIWYG Editor
                        </span>
                    </div>
                    <p class="text-secondary small mt-1 mb-0">Manage the dynamic Refund Policy displayed inside the signup modal and public policy pages.</p>
                </div>
                
                <form action="{{ route('admin.settings.update') }}#tab-legal" method="POST">
                    @csrf
                    <div class="mb-4">
                        <textarea name="refund_policy" id="refund_policy_editor" class="form-control rounded-3">{{ old('refund_policy', $settings->refund_policy) }}</textarea>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-success rounded-pill px-4 py-2.5 fw-semibold shadow-sm" style="background-color: #004225; border: none;">
                            <i class="bi bi-check2-circle me-1.5"></i> Save Refund Policy
                        </button>
                    </div>
                </form>
            </div>

            <!-- Section 3: Data & Privacy Protection Statement -->
            <div class="card border-0 shadow-sm p-4 p-sm-5 rounded-4 bg-white">
                <div class="border-bottom pb-3 mb-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <h2 class="h5 fw-bold text-dark mb-0">
                            <i class="bi bi-lock me-2 text-success"></i> Data &amp; Privacy Protection Statement Editor
                        </h2>
                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1 small">
                            <i class="bi bi-pencil-square me-1"></i> Visual WYSIWYG Editor
                        </span>
                    </div>
                    <p class="text-secondary small mt-1 mb-0">Manage the dynamic Data &amp; Privacy Statement rendered in registration and legal pages.</p>
                </div>
                
                <form action="{{ route('admin.settings.update') }}#tab-legal" method="POST">
                    @csrf
                    <div class="mb-4">
                        <textarea name="privacy_policy" id="privacy_policy_editor" class="form-control rounded-3">{{ old('privacy_policy', $settings->privacy_policy) }}</textarea>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-success rounded-pill px-4 py-2.5 fw-semibold shadow-sm" style="background-color: #004225; border: none;">
                            <i class="bi bi-check2-circle me-1.5"></i> Save Data &amp; Privacy Statement
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <!-- TAB 6: APPLICATION EXPIRATIONS & LIFECYCLE -->
    <div class="tab-pane fade" id="tab-expirations" role="tabpanel" aria-labelledby="tab-expirations-btn">
        <div class="card border-0 shadow-sm p-4 p-sm-5 rounded-4 bg-white">
            <div class="border-bottom pb-4 mb-4 d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-2 text-white d-flex align-items-center justify-content-center shadow-sm" style="width: 44px; height: 44px; background: #004225; color: #d4af37;">
                        <i class="bi bi-hourglass-split fs-4"></i>
                    </div>
                    <div>
                        <h2 class="h5 fw-bold text-dark mb-0.5">Application Expiration &amp; Automated Cleanup</h2>
                        <p class="text-secondary small mb-0">Configure dynamic expiration thresholds for halfway unfinished drafts and paid incomplete applications.</p>
                    </div>
                </div>
                <a href="{{ route('admin.expirations') }}" class="btn btn-dark btn-sm rounded-pill px-3.5 py-2 d-inline-flex align-items-center gap-2 shadow-sm font-monospace text-decoration-none" style="background-color: #004225; border-color: #004225;">
                    <i class="bi bi-grid-3x3-gap-fill text-warning"></i>
                    <span class="fw-semibold">Open Expirations Dashboard</span>
                </a>
            </div>

            <form action="{{ route('admin.settings.update') }}#tab-expirations" method="POST">
                @csrf
                <div class="row g-4 mb-4">
                    <!-- Rule 1: Drafts -->
                    <div class="col-md-6">
                        <div class="p-4 rounded-4 bg-white border shadow-sm h-100 position-relative overflow-hidden" style="border: 1px solid #e2e8f0 !important;">
                            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2.5 py-1 fw-semibold" style="font-size: 11px;">
                                        <i class="bi bi-file-earmark-text me-1"></i> Drafts (Unpaid)
                                    </span>
                                    <h6 class="fw-bold text-dark mb-0" style="font-size: 14.5px;">Unfinished Halfway Applications</h6>
                                </div>
                            </div>
                            <p class="text-secondary small mb-3" style="line-height: 1.6; font-size: 12.5px;">
                                If a client starts an application and abandons it halfway without payment, it will automatically expire and be deleted from the system after this timeframe.
                            </p>
                            <div class="row g-2">
                                <div class="col-7">
                                    <label class="form-label text-secondary fw-semibold extra-small text-uppercase mb-1" style="font-size: 11px;">Duration Value</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-clock-history"></i></span>
                                        <input type="number" name="draft_expiry_value" class="form-control rounded-end-3 border-start-0 fw-semibold" min="1" max="365" value="{{ old('draft_expiry_value', $settings->draft_expiry_value ?? 30) }}" required>
                                    </div>
                                </div>
                                <div class="col-5">
                                    <label class="form-label text-secondary fw-semibold extra-small text-uppercase mb-1" style="font-size: 11px;">Time Unit</label>
                                    <select name="draft_expiry_unit" class="form-select rounded-3 fw-semibold" required>
                                        <option value="days" {{ old('draft_expiry_unit', $settings->draft_expiry_unit ?? 'days') === 'days' ? 'selected' : '' }}>Days</option>
                                        <option value="months" {{ old('draft_expiry_unit', $settings->draft_expiry_unit ?? 'days') === 'months' ? 'selected' : '' }}>Months</option>
                                    </select>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-1.5 text-muted small mt-2.5" style="font-size: 11.5px;">
                                <i class="bi bi-info-circle text-primary"></i>
                                <span>System default: <strong>30 days</strong> inactivity cutoff.</span>
                            </div>
                        </div>
                    </div>

                    <!-- Rule 2: Paid Incomplete -->
                    <div class="col-md-6">
                        <div class="p-4 rounded-4 bg-white border shadow-sm h-100 position-relative overflow-hidden" style="border: 1px solid #e2e8f0 !important;">
                            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1 fw-semibold" style="font-size: 11px;">
                                        <i class="bi bi-credit-card me-1"></i> Paid Applications
                                    </span>
                                    <h6 class="fw-bold text-dark mb-0" style="font-size: 14.5px;">Incomplete After Payment</h6>
                                </div>
                            </div>
                            <p class="text-secondary small mb-3" style="line-height: 1.6; font-size: 12.5px;">
                                If a client makes payment but does not finalize or submit the required details, the application will automatically expire and be cleared after this duration.
                            </p>
                            <div class="row g-2">
                                <div class="col-7">
                                    <label class="form-label text-secondary fw-semibold extra-small text-uppercase mb-1" style="font-size: 11px;">Duration Value</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-calendar-event"></i></span>
                                        <input type="number" name="paid_incomplete_expiry_value" class="form-control rounded-end-3 border-start-0 fw-semibold" min="1" max="365" value="{{ old('paid_incomplete_expiry_value', $settings->paid_incomplete_expiry_value ?? 3) }}" required>
                                    </div>
                                </div>
                                <div class="col-5">
                                    <label class="form-label text-secondary fw-semibold extra-small text-uppercase mb-1" style="font-size: 11px;">Time Unit</label>
                                    <select name="paid_incomplete_expiry_unit" class="form-select rounded-3 fw-semibold" required>
                                        <option value="months" {{ old('paid_incomplete_expiry_unit', $settings->paid_incomplete_expiry_unit ?? 'months') === 'months' ? 'selected' : '' }}>Months</option>
                                        <option value="days" {{ old('paid_incomplete_expiry_unit', $settings->paid_incomplete_expiry_unit ?? 'months') === 'days' ? 'selected' : '' }}>Days</option>
                                    </select>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-1.5 text-muted small mt-2.5" style="font-size: 11.5px;">
                                <i class="bi bi-info-circle text-primary"></i>
                                <span>System default: <strong>3 months</strong> inactivity cutoff.</span>
                            </div>
                        </div>
                    </div>

                    <!-- Bottom Config Card -->
                    <div class="col-12">
                        <div class="p-3.5 rounded-4 bg-light border d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                            <div class="flex-grow-1" style="max-width: 500px;">
                                <label class="form-label small fw-bold text-dark mb-1">
                                    <i class="bi bi-trash3 text-danger me-1"></i> System Expiration Purge Action
                                </label>
                                <select name="purge_action" class="form-select form-select-sm rounded-3">
                                    <option value="delete" {{ old('purge_action', $settings->purge_action ?? 'delete') === 'delete' ? 'selected' : '' }}>
                                        Permanently Delete Records &amp; Storage Uploads (Zero Orphan Files)
                                    </option>
                                    <option value="mark_expired" {{ old('purge_action', $settings->purge_action ?? 'delete') === 'mark_expired' ? 'selected' : '' }}>
                                        Retain in Database &amp; Change Status to 'Expired'
                                    </option>
                                </select>
                            </div>
                            <div class="form-check form-switch m-0 pt-2 pt-md-0">
                                <input type="hidden" name="auto_purge_enabled" value="0">
                                <input class="form-check-input fs-5" type="checkbox" name="auto_purge_enabled" value="1" id="settingsAutoPurgeSwitch" {{ old('auto_purge_enabled', $settings->auto_purge_enabled ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold text-dark ms-2 small d-block" for="settingsAutoPurgeSwitch">
                                    Enable Daily Automated Purge Scheduler
                                    <span class="text-muted fw-normal d-block" style="font-size: 11px;">Executes daily cron cleanup for all expired records</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end pt-2 border-top">
                    <button type="submit" class="btn btn-success rounded-pill px-4 py-2.5 fw-semibold shadow-sm d-flex align-items-center gap-2" style="background-color: #004225; border: none;">
                        <i class="bi bi-check2-circle"></i>
                        <span>Save Expiration Settings</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
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

document.addEventListener('DOMContentLoaded', function() {
    // Preserve active tab across reloads and form submissions via URL hash
    const hash = window.location.hash;
    if (hash) {
        const tabTrigger = document.querySelector(`#settingsTabs button[data-bs-target="${hash}"]`);
        if (tabTrigger) {
            new bootstrap.Tab(tabTrigger).show();
        }
    }

    const triggerTabList = document.querySelectorAll('#settingsTabs button');
    triggerTabList.forEach(triggerEl => {
        triggerEl.addEventListener('shown.bs.tab', event => {
            const targetHash = event.target.getAttribute('data-bs-target');
            if (targetHash) {
                history.replaceState(null, null, targetHash);
            }
        });
    });

    // CKEditor initializations
    if (document.querySelector('#terms_conditions_editor')) {
        ClassicEditor.create(document.querySelector('#terms_conditions_editor'), {
            toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'undo', 'redo']
        }).catch(error => { console.error('CKEditor Terms Error:', error); });
    }
    if (document.querySelector('#refund_policy_editor')) {
        ClassicEditor.create(document.querySelector('#refund_policy_editor'), {
            toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'undo', 'redo']
        }).catch(error => { console.error('CKEditor Refund Error:', error); });
    }
    if (document.querySelector('#privacy_policy_editor')) {
        ClassicEditor.create(document.querySelector('#privacy_policy_editor'), {
            toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'undo', 'redo']
        }).catch(error => { console.error('CKEditor Privacy Error:', error); });
    }
});
</script>
@endsection
