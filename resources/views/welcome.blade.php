@extends('layouts.app')

@section('title', $settings->platform_name ?? 'DOOTOR ENTERPRISES')

@section('styles')
<style>
    /* Custom mobile nav - uses inline styles via JS so Tailwind cannot interfere */
    #mainNavbar {
        display: none;
    }
    @media (min-width: 992px) {
        #mainNavbar {
            display: flex !important;
            flex-direction: row;
            align-items: center;
            flex: 1;
        }
    }
</style>
@endsection

@section('body')
@php
    $user = Auth::user();
    
    // Fallback static services matching Next.js if database has none or just standard ones
    $staticServices = [
        ['title' => 'NIN', 'icon' => 'bi-fingerprint', 'description' => 'Secure your National Identification Number (NIN) registration, modification, or verification.'],
        ['title' => 'BVN', 'icon' => 'bi-bank', 'description' => 'Link and update your Bank Verification Number (BVN) across your financial accounts.'],
        ['title' => 'Passport', 'icon' => 'bi-passport', 'description' => 'Fast-track processing for new international passport applications or renewals.'],
        ['title' => 'Emergency Travel Certificate (ETC)', 'icon' => 'bi-file-earmark-badge', 'description' => 'Expedite emergency travel certificate issuance for urgent international travel.'],
        ['title' => 'Driver\'s Licence Authentication', 'icon' => 'bi-card-list', 'description' => 'Verify and authenticate driver\'s license letters and certification reports.'],
        ['title' => 'Visa: SEV, MEV, TWP, STR & eVisa', 'icon' => 'bi-ticket-perforated', 'description' => 'Smooth processing support for Single Entry, Multi Entry, Temporary Work Permits, and eVisas.'],
        ['title' => 'Police Report', 'icon' => 'bi-shield-check', 'description' => 'Apply for official character clearance, loss of documents, or police reports.'],
        ['title' => 'Newspaper Publication', 'icon' => 'bi-newspaper', 'description' => 'Publish official changes of name, lost items, or announcements in national news.'],
        ['title' => 'Court Affidavit of Name and Age', 'icon' => 'bi-journal-check', 'description' => 'Draft and process legally binding sworn court affidavits for name, age, or declarations.'],
        ['title' => 'LGA Certificate of Indigenization', 'icon' => 'bi-house-check', 'description' => 'Obtain official state/local government origin and indigene certificates.'],
        ['title' => 'Birth Certificate/Attestation by the National Population Commission (NPC)', 'icon' => 'bi-file-earmark-medical', 'description' => 'Acquire official birth registration records or attestation documents from the NPC.'],
    ];

    $dbServices = \App\Models\Service::where('status', 'Active')->get();

    if (!function_exists('getServiceCategory')) {
        function getServiceCategory($name) {
            $name = strtolower($name);
            if (str_contains($name, 'nin') || str_contains($name, 'bvn') || str_contains($name, 'licence') || str_contains($name, 'lga') || str_contains($name, 'indigen')) {
                return 'identity';
            }
            if (str_contains($name, 'passport') || str_contains($name, 'travel') || str_contains($name, 'visa') || str_contains($name, 'etc')) {
                return 'travel';
            }
            return 'legal';
        }
    }
@endphp

<!-- Disclaimer Top Banner -->
<div id="disclaimerBanner" class="py-2 text-center text-white position-relative" style="background-color: #003b1c; font-size: 13px; font-weight: 600; letter-spacing: 0.5px; border-bottom: 2px solid #d4af37; z-index: 1060;">
    <div class="container pe-5">
        <i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>WE ARE NOT GOVERNMENT OFFICIALS, AFFILIATES, OR AN ANNEX OF THE NIGERIAN HIGH COMMISSION.
    </div>
    <button type="button" class="btn-close btn-close-white position-absolute top-50 translate-middle-y end-0 me-3" aria-label="Close" onclick="dismissDisclaimer()" style="font-size: 10px;"></button>
</div>

<!-- Navigation Header -->
<header class="bg-white border-bottom sticky-top py-3" style="z-index: 1055;">
    <div class="container d-flex align-items-center justify-content-between">
        <a class="d-flex align-items-center gap-1 fw-bold text-dark text-decoration-none" href="/">
            @if($settings && $settings->logo_url)
                <img src="{{ $settings->logo_url }}" alt="Logo" class="rounded" style="height: 32px; width: 32px; object-fit: contain;">
            @else
                <svg width="34" height="34" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg" class="me-1">
                    <path d="M20 20C40 20 52 32 52 50C52 68 40 80 20 80C14 80 14 74 14 74V26C14 26 14 20 20 20Z" fill="url(#goldLogo)" />
                    <path d="M28 32C38 32 44 40 44 50C44 60 38 68 28 68V32Z" fill="#FFFFFF" />
                    <path d="M50 24H80V35H64V44H76V53H64V63H80V74H50V24Z" fill="#004225" />
                    <defs>
                        <linearGradient id="goldLogo" x1="14" y1="20" x2="52" y2="80" gradientUnits="userSpaceOnUse">
                            <stop offset="0%" stop-color="#FFE57F" />
                            <stop offset="50%" stop-color="#D4AF37" />
                            <stop offset="100%" stop-color="#AA820A" />
                        </linearGradient>
                    </defs>
                </svg>
            @endif
            <span>{{ $settings->platform_name ?? 'DOOTOR ENTERPRISES' }}</span>
        </a>

        {{-- Mobile hamburger button — uses custom JS toggle, NOT Bootstrap collapse --}}
        <button id="navToggleBtn" class="border rounded p-2 bg-transparent d-lg-none" type="button" aria-label="Toggle navigation" style="line-height:1;">
            <i class="bi bi-list fs-3"></i>
        </button>

        {{-- Nav links container --}}
        <div id="mainNavbar" style="display:none;">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gap-3 list-unstyled d-lg-flex flex-lg-row align-items-lg-center">
                <li><a class="nav-link fw-medium text-dark text-decoration-none px-2" href="#">Home</a></li>
                <li><a class="nav-link fw-medium text-dark text-decoration-none px-2" href="#services">Services</a></li>
                <li><a class="nav-link fw-medium text-dark text-decoration-none px-2" href="#faq">FAQ</a></li>
                <li><a class="nav-link fw-medium text-dark text-decoration-none px-2" href="#contact">Contact</a></li>
            </ul>
            <div class="d-flex align-items-center gap-2 mt-3 mt-lg-0">
                @if($user)
                    @php
                        $dashboardRoute = route('client.dashboard');
                        if ($user->role === 'admin') $dashboardRoute = route('admin.dashboard');
                        elseif ($user->role === 'vendor') $dashboardRoute = ($user->status === 'Pending' || $user->status === 'Rejected') ? route('vendor.kyc') : route('vendor.dashboard');
                    @endphp
                    <a href="{{ $dashboardRoute }}" class="btn btn-dark px-4 rounded-pill">
                        <i class="bi bi-grid-fill me-2"></i> Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-dark px-4 rounded-pill">Login</a>
                    <a href="{{ route('register') }}" class="btn btn-dark px-4 rounded-pill">Register</a>
                @endif
            </div>
        </div>
    </div>

    {{-- Mobile dropdown panel (hidden by default, shown below header on toggle) --}}
    <div id="mobileNavDrawer" style="display:none; border-top: 1px solid #dee2e6;" class="bg-white px-3 pb-3 pt-2 d-lg-none">
        <ul class="list-unstyled mb-3">
            <li class="py-2 border-bottom"><a class="text-dark text-decoration-none fw-medium" href="#">Home</a></li>
            <li class="py-2 border-bottom"><a class="text-dark text-decoration-none fw-medium" href="#services">Services</a></li>
            <li class="py-2 border-bottom"><a class="text-dark text-decoration-none fw-medium" href="#faq">FAQ</a></li>
            <li class="py-2"><a class="text-dark text-decoration-none fw-medium" href="#contact">Contact</a></li>
        </ul>
        <div class="d-flex gap-2">
            @if($user)
                <a href="{{ $dashboardRoute ?? route('client.dashboard') }}" class="btn btn-dark px-4 rounded-pill w-100">
                    <i class="bi bi-grid-fill me-2"></i> Dashboard
                </a>
            @else
                <a href="{{ route('login') }}" class="btn btn-outline-dark px-4 rounded-pill flex-fill">Login</a>
                <a href="{{ route('register') }}" class="btn btn-dark px-4 rounded-pill flex-fill">Register</a>
            @endif
        </div>
    </div>
</header>

<!-- Hero Section -->
<section class="py-5 bg-light position-relative overflow-hidden" style="min-height: 70vh; display: flex; align-items: center;">
    <!-- Background Circle styling -->
    <div class="position-absolute bg-brand-gradient rounded-circle opacity-10" style="width: 500px; height: 500px; top: -100px; right: -100px; filter: blur(50px);"></div>
    <div class="position-absolute bg-brand-gradient rounded-circle opacity-10" style="width: 300px; height: 300px; bottom: -50px; left: -50px; filter: blur(30px);"></div>
    
    <div class="container relative-content z-2">
        <div class="row align-items-center g-5">
            <div class="col-lg-6 text-center text-lg-start">
                <span class="badge bg-brand-gradient text-white px-3 py-2 rounded-pill mb-3">
                    <i class="bi bi-sparkles me-2"></i> Streamlined Document Support
                </span>
                <h1 class="display-3 fw-bold mb-4">
                    All Business &amp; Personal <br>
                    <span class="text-gradient">Services In One Place</span>
                </h1>
                <p class="lead text-secondary mb-4">
                    Fast-track passport approvals, visa handling, NIN verification, court affidavits, and more. A secured multi-vendor portal connecting clients to verified agents.
                </p>
                <div class="d-flex flex-column flex-sm-row justify-content-center justify-content-lg-start gap-3">
                    <a href="#services" class="btn btn-dark btn-lg px-4 rounded-pill">Explore Services <i class="bi bi-arrow-right ms-2"></i></a>
                    <a href="{{ route('register.client') }}" class="btn btn-outline-secondary btn-lg px-4 rounded-pill">Register as Client</a>
                </div>
            </div>
            
            <div class="col-lg-6 text-center">
                <!-- Premium Progress Status Tracker Mockup -->
                <div class="card border-0 shadow-lg p-4 rounded-4 bg-white mx-auto text-start position-relative overflow-hidden" style="max-width: 480px; transform: rotate(1deg);">
                    <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-3">
                        <div>
                            <span class="text-muted d-block small fw-bold uppercase tracking-wider" style="font-size: 11px;">TRACKING ID: DE-8947-NG</span>
                            <span class="fw-bold fs-5 text-dark">Application Status</span>
                        </div>
                        <span class="badge px-3 py-2 rounded-pill fw-bold d-flex align-items-center gap-1.5" style="background-color: #fff9db; color: #f59f00;">
                            <span class="spinner-grow spinner-grow-sm text-warning" role="status" style="width: 8px; height: 8px;"></span> Vetting
                        </span>
                    </div>

                    <div class="d-flex flex-column gap-3.5 position-relative">
                        <!-- Connecting Line -->
                        <div class="position-absolute h-75 border-start border-2" style="left: 15px; top: 20px; border-color: #e2e8f0 !important; z-index: 1;"></div>
                        
                        <!-- Step 1 -->
                        <div class="d-flex gap-3 position-relative" style="z-index: 2;">
                            <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center shadow-sm" style="width: 30px; height: 30px; flex-shrink: 0;">
                                <i class="bi bi-check-lg" style="font-size: 14px;"></i>
                            </div>
                            <div>
                                <span class="fw-bold d-block text-dark small" style="font-size: 13px;">Step 1: Order Placed & Secure Payment</span>
                                <span class="text-secondary" style="font-size: 11.5px;">Completed via Credo Payment Gateway</span>
                            </div>
                        </div>

                        <!-- Step 2 -->
                        <div class="d-flex gap-3 position-relative" style="z-index: 2;">
                            <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center shadow-sm" style="width: 30px; height: 30px; flex-shrink: 0;">
                                <i class="bi bi-check-lg" style="font-size: 14px;"></i>
                            </div>
                            <div>
                                <span class="fw-bold d-block text-dark small" style="font-size: 13px;">Step 2: Document Verification</span>
                                <span class="text-secondary" style="font-size: 11.5px;">NIN details verified by processing agent</span>
                            </div>
                        </div>

                        <!-- Step 3 -->
                        <div class="d-flex gap-3 position-relative" style="z-index: 2;">
                            <div class="rounded-circle d-flex align-items-center justify-content-center shadow-sm border border-warning" style="width: 30px; height: 30px; flex-shrink: 0; background-color: #fff9db; color: #f59f00;">
                                <span class="spinner-border spinner-border-sm" role="status" style="width: 12px; height: 12px;"></span>
                            </div>
                            <div>
                                <span class="fw-bold d-block text-dark small" style="font-size: 13px;">Step 3: Document Vetting</span>
                                <span class="text-secondary" style="font-size: 11.5px;">Liaising with High Commission & government officials</span>
                            </div>
                        </div>

                        <!-- Step 4 -->
                        <div class="d-flex gap-3 position-relative" style="z-index: 2;">
                            <div class="rounded-circle bg-light text-muted d-flex align-items-center justify-content-center shadow-sm border" style="width: 30px; height: 30px; flex-shrink: 0;">
                                <i class="bi bi-send-fill" style="font-size: 12px;"></i>
                            </div>
                            <div>
                                <span class="fw-semibold d-block text-muted small" style="font-size: 13px;">Step 4: Dispatch & Delivery</span>
                                <span class="text-muted" style="font-size: 11.5px;">Tracking code will be shared via Email/SMS</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features section -->
<section class="py-5 bg-white">
    <div class="container py-4">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="p-4 rounded-4 bg-light text-center h-100 d-flex flex-column align-items-center">
                    <div class="bg-dark text-white rounded-circle p-3 mb-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                        <i class="bi bi-archive fs-3"></i>
                    </div>
                    <h3 class="h5 fw-bold mb-2">All Services In One Place</h3>
                    <p class="text-secondary small mb-0">From NIN and BVN to passports and visas, manage all documentation in a single unified platform.</p>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="p-4 rounded-4 bg-light text-center h-100 d-flex flex-column align-items-center">
                    <div class="bg-dark text-white rounded-circle p-3 mb-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                        <i class="bi bi-lightning-charge fs-3"></i>
                    </div>
                    <h3 class="h5 fw-bold mb-2">Fast Processing</h3>
                    <p class="text-secondary small mb-0">We prioritize efficiency and vetting to get your urgent requests completed quickly by verified agents.</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="p-4 rounded-4 bg-light text-center h-100 d-flex flex-column align-items-center">
                    <div class="bg-dark text-white rounded-circle p-3 mb-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                        <i class="bi bi-shield-check fs-3"></i>
                    </div>
                    <h3 class="h5 fw-bold mb-2">Secure &amp; Trackable</h3>
                    <p class="text-secondary small mb-0">Monitor every step of your application request from your secure dashboard with encrypted data uploads.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Services Grid -->
<section id="services" class="py-5 bg-light">
    <div class="container py-4">
        <div class="text-center max-w-lg mx-auto mb-4">
            <span class="text-uppercase small fw-bold text-gradient tracking-wide">Catalog</span>
            <h2 class="display-5 fw-bold mt-2">Services We Assist With</h2>
            <p class="text-secondary">Explore the documentation and certification services offered by our verified vendors.</p>
        </div>

        <!-- Search and Filter Controls -->
        <div class="row mb-5 justify-content-center">
            <div class="col-md-6 col-lg-5 mb-3 mb-md-0">
                <div class="input-group shadow-sm rounded-pill overflow-hidden border">
                    <span class="input-group-text bg-white border-0 ps-3"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" id="serviceSearch" class="form-control border-0 ps-2 py-2.5 small" placeholder="Search for services (e.g. NIN, Visa, Birth)..." style="font-size: 14.5px;">
                </div>
            </div>
            <div class="col-12 mt-3 d-flex flex-wrap justify-content-center gap-2" id="serviceCategories">
                <button class="btn btn-dark rounded-pill px-4 btn-sm fw-medium active" data-category="all">All Services</button>
                <button class="btn btn-outline-dark rounded-pill px-4 btn-sm fw-medium" data-category="identity">Identity & Verifications</button>
                <button class="btn btn-outline-dark rounded-pill px-4 btn-sm fw-medium" data-category="travel">Travel & Visas</button>
                <button class="btn btn-outline-dark rounded-pill px-4 btn-sm fw-medium" data-category="legal">Legal & Publications</button>
            </div>
        </div>

        <div class="row g-4">
            @if($dbServices->count() > 0)
                @foreach($dbServices as $service)
                    <div class="col-md-6 col-lg-3 service-card-container" data-category="{{ getServiceCategory($service->name) }}" data-name="{{ strtolower($service->name) }}">
                        <div class="card border-0 shadow-sm h-100 p-3 rounded-4 bg-white d-flex flex-column position-relative overflow-hidden">
                            <!-- Gold Checkmark Badge -->
                            <div class="position-absolute top-0 end-0 mt-3 me-3" title="Verified Service">
                                <i class="bi bi-patch-check-fill fs-5" style="color: #d4af37;"></i>
                            </div>
                            <div class="rounded-3 bg-brand-gradient text-white p-3 mb-3 d-flex align-items-center justify-content-center align-self-start" style="width: 50px; height: 50px;">
                                <i class="bi bi-file-earmark-check fs-4"></i>
                            </div>
                            <h3 class="h6 fw-bold mb-2">{{ $service->name }}</h3>
                            <p class="text-secondary small flex-grow-1">{{ $service->description }}</p>
                            <hr class="my-2 border-light">
                            <div class="d-flex justify-content-between align-items-center">
                                @if($service->price > 0)
                                    <span class="fw-bold text-dark">{{ $settings->default_currency ?? 'USD' }} {{ number_format($service->price, 2) }}</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary rounded-pill">Custom Rate</span>
                                @endif
                                <a href="{{ route('login') }}" class="btn btn-outline-dark btn-sm rounded-pill px-3">Order</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                @foreach($staticServices as $service)
                    <div class="col-md-6 col-lg-3 service-card-container" data-category="{{ getServiceCategory($service['title']) }}" data-name="{{ strtolower($service['title']) }}">
                        <div class="card border-0 shadow-sm h-100 p-3 rounded-4 bg-white d-flex flex-column position-relative overflow-hidden">
                            <!-- Gold Checkmark Badge -->
                            <div class="position-absolute top-0 end-0 mt-3 me-3" title="Verified Service">
                                <i class="bi bi-patch-check-fill fs-5" style="color: #d4af37;"></i>
                            </div>
                            <div class="rounded-3 bg-brand-gradient text-white p-3 mb-3 d-flex align-items-center justify-content-center align-self-start" style="width: 50px; height: 50px;">
                                <i class="bi {{ $service['icon'] }} fs-4"></i>
                            </div>
                            <h3 class="h6 fw-bold mb-2">{{ $service['title'] }}</h3>
                            <p class="text-secondary small flex-grow-1">{{ $service['description'] }}</p>
                            <hr class="my-2 border-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge bg-secondary-subtle text-secondary rounded-pill">Varies by Vendor</span>
                                <a href="{{ route('login') }}" class="btn btn-outline-dark btn-sm rounded-pill px-3">Order</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section id="faq" class="py-5 bg-white">
    <div class="container py-4">
        <div class="row align-items-center g-5">
            <div class="col-lg-5">
                <span class="text-uppercase small fw-bold text-gradient">Information</span>
                <h2 class="display-5 fw-bold mt-2 mb-4">Frequently Asked Questions</h2>
                <p class="text-secondary">Have questions about processing times, document requirements, payments, or security? Find fast answers here.</p>
                <a href="#contact" class="btn btn-dark px-4 rounded-pill">Still have questions?</a>
            </div>
            
            <div class="col-lg-7">
                <div id="customFaqAccordion">

                    <div class="faq-item border-bottom py-2">
                        <button class="faq-question w-100 text-start bg-transparent border-0 fw-semibold fs-6 d-flex justify-content-between align-items-center py-2 px-0" type="button">
                            How long does document processing take?
                            <i class="bi bi-chevron-down faq-icon ms-2" style="transition: transform 0.25s ease; flex-shrink:0;"></i>
                        </button>
                        <div class="faq-answer text-secondary small px-1" style="display:none; padding-top: 8px; padding-bottom: 8px;">
                            Processing times vary depending on the service. Routine certificates like Court Affidavits are usually ready in 24 hours, while passport approvals and visa vetted applications can take between 3 to 10 working days.
                        </div>
                    </div>

                    <div class="faq-item border-bottom py-2">
                        <button class="faq-question w-100 text-start bg-transparent border-0 fw-semibold fs-6 d-flex justify-content-between align-items-center py-2 px-0" type="button">
                            Are the service providers verified?
                            <i class="bi bi-chevron-down faq-icon ms-2" style="transition: transform 0.25s ease; flex-shrink:0;"></i>
                        </button>
                        <div class="faq-answer text-secondary small px-1" style="display:none; padding-top: 8px; padding-bottom: 8px;">
                            Yes. All vendors on this platform must complete a strict KYC profile, upload official government IDs, submit banking details, and pass a vetting approval process by system administrators before they can list services.
                        </div>
                    </div>

                    <div class="faq-item border-bottom py-2">
                        <button class="faq-question w-100 text-start bg-transparent border-0 fw-semibold fs-6 d-flex justify-content-between align-items-center py-2 px-0" type="button">
                            What payment gateways are supported?
                            <i class="bi bi-chevron-down faq-icon ms-2" style="transition: transform 0.25s ease; flex-shrink:0;"></i>
                        </button>
                        <div class="faq-answer text-secondary small px-1" style="display:none; padding-top: 8px; padding-bottom: 8px;">
                            We support secure billing via Paystack and Credo for rapid local and international payments. All transactions are protected via industry-standard encryption protocols.
                        </div>
                    </div>

                    <div class="faq-item py-2">
                        <button class="faq-question w-100 text-start bg-transparent border-0 fw-semibold fs-6 d-flex justify-content-between align-items-center py-2 px-0" type="button">
                            Is my personal information secure?
                            <i class="bi bi-chevron-down faq-icon ms-2" style="transition: transform 0.25s ease; flex-shrink:0;"></i>
                        </button>
                        <div class="faq-answer text-secondary small px-1" style="display:none; padding-top: 8px; padding-bottom: 8px;">
                            Absolutely. We use end-to-end data encryption, secure server infrastructure, and strict access controls. Your data is never shared with third parties without your consent.
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>


<!-- Contact & Footer -->
<footer id="contact" class="py-5 bg-dark text-white position-relative">
    <div class="container py-4">
        <div class="row g-5">
            <div class="col-lg-5">
                <h3 class="fw-bold mb-3 d-flex align-items-center gap-1">
                    <svg width="28" height="28" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg" class="me-1">
                        <!-- Stylized D (Gold) -->
                        <path d="M20 20C40 20 52 32 52 50C52 68 40 80 20 80C14 80 14 74 14 74V26C14 26 14 20 20 20Z" fill="url(#goldLogoFooter)" />
                        <path d="M28 32C38 32 44 40 44 50C44 60 38 68 28 68V32Z" fill="#1A1A1A" />
                        <!-- Stylized E (Dark Green) -->
                        <path d="M50 24H80V35H64V44H76V53H64V63H80V74H50V24Z" fill="#2E7D32" />
                        <defs>
                            <linearGradient id="goldLogoFooter" x1="14" y1="20" x2="52" y2="80" gradientUnits="userSpaceOnUse">
                                <stop offset="0%" stop-color="#FFE57F" />
                                <stop offset="50%" stop-color="#D4AF37" />
                                <stop offset="100%" stop-color="#AA820A" />
                            </linearGradient>
                        </defs>
                    </svg>
                    <span>{{ $settings->platform_name ?? 'DOOTOR ENTERPRISES' }}</span>
                </h3>
                <p class="text-white-50 small mb-4">
                    The leading portal for quick, verified, and secured document applications. Connecting business services with professional agents.
                </p>
                <div class="d-flex flex-column gap-2 text-white-50 small">
                    <span class="d-flex align-items-center gap-2">
                        <i class="bi bi-whatsapp text-success"></i>
                        <a href="https://wa.me/14164589707" class="text-white-50 text-decoration-none" style="transition: color 0.2s;" target="_blank">Direct/WhatsApp: +1 416-458-9707</a>
                    </span>
                    <span class="d-flex align-items-center gap-2">
                        <i class="bi bi-envelope text-white"></i>
                        <a href="mailto:support@dootor-enterprises.com" class="text-white-50 text-decoration-none">support@dootor-enterprises.com</a>
                    </span>
                    <span class="d-flex align-items-center gap-2">
                        <i class="bi bi-google text-warning"></i>
                        <span>Google Page: NIN & OTHER NIGERIAN TRAVEL DOCUMENTS CONSULTANCY</span>
                    </span>
                </div>
            </div>
            
            <div class="col-lg-7">
                <div class="bg-secondary bg-opacity-10 p-4 rounded-4">
                    <h4 class="h5 fw-bold mb-3">Send Us a Quick Message</h4>

                    @if(session('contact_success'))
                        <div class="alert alert-success border-0 rounded-3 mb-3 small">
                            <i class="bi bi-check-circle-fill me-2"></i>{{ session('contact_success') }}
                        </div>
                    @endif
                    @if(session('contact_error'))
                        <div class="alert alert-danger border-0 rounded-3 mb-3 small">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('contact_error') }}
                        </div>
                    @endif

                    <form action="{{ route('contact.send') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <input type="text" name="first_name" class="form-control bg-dark text-white border-secondary small" placeholder="First Name" value="{{ old('first_name') }}" required style="color:#fff; --bs-form-control-color:#fff;" autocomplete="given-name">
                                <style>#contact .form-control::placeholder { color: rgba(255,255,255,0.55) !important; }</style>
                            </div>
                            <div class="col-sm-6">
                                <input type="text" name="last_name" class="form-control bg-dark text-white border-secondary small" placeholder="Last Name" value="{{ old('last_name') }}" required style="color:#fff;">
                            </div>
                            <div class="col-12">
                                <input type="email" name="email" class="form-control bg-dark text-white border-secondary small" placeholder="Email Address" value="{{ old('email') }}" required style="color:#fff;">
                            </div>
                            <div class="col-12">
                                <input type="text" name="subject" class="form-control bg-dark text-white border-secondary small" placeholder="Subject" value="{{ old('subject') }}" required style="color:#fff;">
                            </div>
                            <div class="col-12">
                                <textarea name="message" class="form-control bg-dark text-white border-secondary small" rows="4" placeholder="How can we help you?" required style="color:#fff;">{{ old('message') }}</textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-light rounded-pill px-4 small w-100 fw-semibold">
                                    <i class="bi bi-send me-2"></i>Send Message
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
        
        <hr class="my-5 border-secondary">
        
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 text-white-50 small">
            <span>&copy; {{ date('Y') }} {{ $settings->platform_name ?? 'DOOTOR ENTERPRISES' }}. All rights reserved.</span>
            <div class="d-flex gap-3">
                <a href="#" class="text-white-50 text-decoration-none hover-white">Terms of Service</a>
                <a href="#" class="text-white-50 text-decoration-none hover-white">Privacy Policy</a>
            </div>
        </div>
    </div>
</footer>

<!-- Floating WhatsApp Widget -->
<a href="https://wa.me/14164589707?text=Hello%20Dootor%20Enterprises,%20I%20would%20like%20to%20inquire%20about%20your%20services." 
   target="_blank" 
   class="position-fixed d-flex align-items-center justify-content-center rounded-circle shadow-lg text-white" 
   style="width: 56px; height: 56px; background-color: #25D366; z-index: 9999; bottom: 25px; right: 25px; transition: all 0.3s ease; text-decoration: none;"
   onmouseover="this.style.transform='scale(1.1)';" 
   onmouseout="this.style.transform='scale(1)';"
   title="Chat on WhatsApp">
    <i class="bi bi-whatsapp" style="font-size: 30px;"></i>
</a>
@endsection

@section('scripts')
<script>
    // Disclaimer Dismissal
    function dismissDisclaimer() {
        document.getElementById('disclaimerBanner').style.display = 'none';
        sessionStorage.setItem('disclaimerDismissed', 'true');
    }

    document.addEventListener("DOMContentLoaded", function() {
        // Restore Disclaimer state
        if (sessionStorage.getItem('disclaimerDismissed') === 'true') {
            const banner = document.getElementById('disclaimerBanner');
            if (banner) banner.style.display = 'none';
        }

        // ---- Custom Mobile Nav Toggle ----
        // Uses inline style.display to bypass Tailwind CSS conflict with Bootstrap's .collapse
        var navToggleBtn = document.getElementById('navToggleBtn');
        var mobileNavDrawer = document.getElementById('mobileNavDrawer');
        var isOpen = false;

        if (navToggleBtn && mobileNavDrawer) {
            navToggleBtn.addEventListener('click', function() {
                isOpen = !isOpen;
                mobileNavDrawer.style.display = isOpen ? 'block' : 'none';
                // Toggle icon between hamburger and X
                var icon = navToggleBtn.querySelector('i');
                if (icon) {
                    icon.className = isOpen ? 'bi bi-x fs-3' : 'bi bi-list fs-3';
                }
            });

            // Close menu when a nav link is clicked
            mobileNavDrawer.querySelectorAll('a').forEach(function(link) {
                link.addEventListener('click', function() {
                    isOpen = false;
                    mobileNavDrawer.style.display = 'none';
                    var icon = navToggleBtn.querySelector('i');
                    if (icon) icon.className = 'bi bi-list fs-3';
                });
            });
        }

        // ---- Custom FAQ Accordion ----
        // Pure JS toggle using inline styles — completely avoids Bootstrap/Tailwind .collapse conflict
        document.querySelectorAll('#customFaqAccordion .faq-question').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var answer = this.nextElementSibling;
                var icon = this.querySelector('.faq-icon');
                var isOpen = answer.style.display === 'block';

                // Close all other open answers first
                document.querySelectorAll('#customFaqAccordion .faq-answer').forEach(function(a) {
                    a.style.display = 'none';
                });
                document.querySelectorAll('#customFaqAccordion .faq-icon').forEach(function(i) {
                    i.style.transform = 'rotate(0deg)';
                });

                // Toggle current
                if (!isOpen) {
                    answer.style.display = 'block';
                    if (icon) icon.style.transform = 'rotate(180deg)';
                }
            });
        });


        const searchInput = document.getElementById('serviceSearch');
        const filterButtons = document.querySelectorAll('#serviceCategories button');
        const cardContainers = document.querySelectorAll('.service-card-container');

        let currentSearch = "";
        let currentCategory = "all";

        function filterServices() {
            let visibleCount = 0;
            cardContainers.forEach(container => {
                const name = container.getAttribute('data-name');
                const category = container.getAttribute('data-category');
                
                const matchesSearch = name.includes(currentSearch);
                const matchesCategory = currentCategory === "all" || category === currentCategory;

                if (matchesSearch && matchesCategory) {
                    container.style.display = "block";
                    visibleCount++;
                } else {
                    container.style.display = "none";
                }
            });
            
            // Handle "No services found" message
            let noResultsMsg = document.getElementById('noServicesFound');
            if (visibleCount === 0) {
                if (!noResultsMsg) {
                    noResultsMsg = document.createElement('div');
                    noResultsMsg.id = 'noServicesFound';
                    noResultsMsg.className = 'col-12 text-center py-5 text-secondary';
                    noResultsMsg.innerHTML = '<i class="bi bi-search fs-1 d-block mb-2"></i><p class="small">No services match your criteria. Please try another search.</p>';
                    document.querySelector('#services .row.g-4').appendChild(noResultsMsg);
                }
            } else if (noResultsMsg) {
                noResultsMsg.remove();
            }
        }

        if (searchInput) {
            searchInput.addEventListener('input', function(e) {
                currentSearch = e.target.value.toLowerCase().trim();
                filterServices();
            });
        }

        filterButtons.forEach(btn => {
            btn.addEventListener('click', function(e) {
                filterButtons.forEach(b => {
                    b.classList.remove('btn-dark', 'active');
                    b.classList.add('btn-outline-dark');
                });
                
                this.classList.remove('btn-outline-dark');
                this.classList.add('btn-dark', 'active');
                
                currentCategory = this.getAttribute('data-category');
                filterServices();
            });
        });
    });
</script>
@endsection
