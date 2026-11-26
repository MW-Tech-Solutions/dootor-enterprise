@extends('layouts.dashboard')

@section('title', 'Services Catalog - ' . ($settings->platform_name ?? 'DOOTOR ENTERPRISES'))

@section('styles')
@parent
<style>
    /* ========================================================
       CLIENT SERVICES CATALOG — Modern Card Design (Matching Homepage)
       ======================================================== */
    .service-catalog-card {
        border: 1px solid #e8edf3 !important;
        border-radius: 22px !important;
        transition: all 0.38s cubic-bezier(0.34,1.56,0.64,1) !important;
        background: #ffffff !important;
        box-shadow: 0 2px 16px -2px rgba(0,0,0,0.05) !important;
        overflow: hidden;
    }
    .service-catalog-card:hover {
        transform: translateY(-8px) scale(1.01);
        box-shadow: 0 24px 48px -8px rgba(0,66,37,0.15), 0 0 0 1.5px rgba(0,66,37,0.12) !important;
        border-color: rgba(0,66,37,0.2) !important;
    }

    /* ---- Service Icon Header (matching homepage fallback) ---- */
    .svc-icon-header {
        height: 150px;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }
    .svc-icon-bg {
        position: absolute;
        inset: 0;
        opacity: 0.92;
    }
    .svc-icon-circle {
        width: 76px;
        height: 76px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        z-index: 2;
        box-shadow: 0 8px 24px rgba(0,0,0,0.3), inset 0 1px 0 rgba(255,255,255,0.2);
        transition: transform 0.4s cubic-bezier(0.34,1.56,0.64,1);
    }
    .service-catalog-card:hover .svc-icon-circle {
        transform: scale(1.12) rotate(5deg);
    }
    .svc-icon-circle i {
        font-size: 2rem;
        color: #fff;
    }
    .svc-corner-seal {
        position: absolute;
        top: 12px;
        right: 12px;
        z-index: 3;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: rgba(10,20,14,0.75);
        backdrop-filter: blur(6px);
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid rgba(212,175,55,0.5);
    }
    .svc-category-pill {
        position: absolute;
        top: 12px;
        left: 12px;
        z-index: 3;
        background: rgba(10,20,14,0.75);
        backdrop-filter: blur(6px);
        color: rgba(255,255,255,0.9);
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0.5px;
        padding: 4px 10px;
        border-radius: 999px;
        border: 1px solid rgba(255,255,255,0.15);
    }
    .svc-icon-bg-dots {
        position: absolute;
        inset: 0;
        background-image: radial-gradient(rgba(255,255,255,0.08) 1px, transparent 1px);
        background-size: 18px 18px;
        z-index: 1;
    }

    /* ---- Sub-services button on card ---- */
    .btn-sub-services {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        border-radius: 999px;
        padding: 7px 14px;
        font-size: 12px;
        font-weight: 600;
        color: #004225;
        cursor: pointer;
        transition: all 0.25s cubic-bezier(0.34,1.56,0.64,1);
        text-decoration: none;
    }
    .btn-sub-services:hover {
        background: #004225;
        color: #fff;
        border-color: #004225;
        transform: scale(1.02);
    }
    .btn-sub-services .sub-count-badge {
        background: #004225;
        color: #fff;
        border-radius: 999px;
        padding: 1px 7px;
        font-size: 10px;
        font-weight: 700;
        transition: background 0.2s ease;
    }
    .btn-sub-services:hover .sub-count-badge {
        background: rgba(255,255,255,0.25);
    }

    /* ========================================================
       iOS-STYLE SUB-SERVICES MODAL (Full Screen Blur + Sheet)
       ======================================================== */
    .ios-modal-overlay {
        position: fixed;
        inset: 0;
        z-index: 9999;
        background: rgba(0, 0, 0, 0.45);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.32s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .ios-modal-overlay.ios-open {
        opacity: 1;
        pointer-events: auto;
    }
    .ios-modal-sheet {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(28px) saturate(180%);
        -webkit-backdrop-filter: blur(28px) saturate(180%);
        border: 1px solid rgba(255, 255, 255, 0.9);
        box-shadow: 0 32px 80px -16px rgba(0, 0, 0, 0.25), 0 0 0 1px rgba(0, 0, 0, 0.05);
        border-radius: 28px;
        width: 100%;
        max-width: 580px;
        max-height: 88vh;
        display: flex;
        flex-direction: column;
        transform: scale(0.92) translateY(24px);
        transition: transform 0.38s cubic-bezier(0.34, 1.56, 0.64, 1);
        overflow: hidden;
    }
    .ios-modal-overlay.ios-open .ios-modal-sheet {
        transform: scale(1) translateY(0);
    }
    .ios-modal-handle-bar {
        width: 40px;
        height: 4.5px;
        background: #cbd5e1;
        border-radius: 999px;
        margin: 12px auto 0;
    }
    .ios-modal-header {
        padding: 18px 24px 14px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .ios-modal-title {
        font-size: 19px;
        font-weight: 700;
        color: #0f172a;
        letter-spacing: -0.4px;
        margin: 0;
    }
    .ios-modal-subtitle {
        font-size: 12.5px;
        color: #64748b;
        margin-top: 2px;
    }
    .ios-modal-close {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #f1f5f9;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #64748b;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .ios-modal-close:hover {
        background: #e2e8f0;
        color: #0f172a;
    }
    .ios-modal-body {
        padding: 16px 24px;
        overflow-y: auto;
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .ios-sub-item {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 13px 16px;
        border-radius: 16px;
        background: #ffffff;
        border: 1px solid #e8edf3;
        box-shadow: 0 1px 4px rgba(0,0,0,0.03);
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        text-decoration: none;
        color: inherit;
    }
    .ios-sub-item:hover {
        background: #f0fdf4;
        border-color: #004225;
        transform: translateY(-2px);
        box-shadow: 0 6px 16px -2px rgba(0,66,37,0.12);
    }
    .ios-sub-item-icon {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        background: rgba(0,66,37,0.08);
        border: 1px solid rgba(0,66,37,0.15);
        color: #004225;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 17px;
    }
    .ios-sub-item-name {
        font-weight: 600;
        font-size: 14px;
        color: #0f172a;
    }
    .ios-sub-item-price {
        font-size: 11.5px;
        color: #64748b;
    }
    .ios-modal-cta {
        padding: 16px 24px 20px;
        border-top: 1px solid #f1f5f9;
        background: #ffffff;
    }
    .ios-modal-cta .btn {
        border-radius: 999px;
        padding: 12px 20px;
        font-weight: 700;
        font-size: 14px;
        background: #004225;
        border: none;
    }
    .ios-modal-cta .btn:hover {
        background: #00331c;
    }
</style>
@endsection

@section('content')
@php
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

<div class="container-fluid px-0">
    <!-- Header Badges -->
    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
        <span class="badge" style="background-color: #e6f4ea; color: #004225; border-radius: 50px; font-weight: 700; font-size: 11px; padding: 6px 14px; border: 1px solid rgba(0, 66, 37, 0.12);">
            <i class="bi bi-shield-check me-1"></i> VERIFIED GLOBAL CONSULAR SERVICES
        </span>
        <span class="badge" style="background-color: #ecfdf5; color: #004225; border-radius: 50px; font-weight: 600; font-size: 11px; padding: 6px 14px; border: 1px solid rgba(0, 66, 37, 0.12);">
            <i class="bi bi-credit-card me-1"></i> Billing Currency: {{ $currencyCode ?? 'USD' }} ($)
        </span>
    </div>

    <!-- Catalog Section Title -->
    <div class="text-center max-w-lg mx-auto mb-4 pt-1">
        <span class="text-uppercase small fw-bold tracking-wide" style="color: #004225; letter-spacing: 1.2px;">Services Catalog</span>
        <h1 class="display-6 fw-bold mt-1 text-dark" style="letter-spacing: -0.02em;">Services We Assist With</h1>
        <p style="color: #334155; font-size: 14.5px;">Browse official administrative documentation and certification services with transparent USD pricing.</p>
    </div>

    <!-- Search and Filter Controls -->
    <div class="row mb-5 justify-content-center">
        <div class="col-md-7 col-lg-6 mb-3 mb-md-0">
            <div class="input-group shadow-sm rounded-pill overflow-hidden border">
                <span class="input-group-text bg-white border-0 ps-3.5"><i class="bi bi-search text-muted"></i></span>
                <input type="text" id="serviceSearch" class="form-control border-0 ps-2 py-2.5 small" placeholder="Search for services or sub-services (e.g. Passport, Visa, Affidavit)..." style="font-size: 14px;">
            </div>
        </div>
        <div class="col-12 mt-3 d-flex flex-wrap justify-content-center gap-2" id="serviceCategories">
            <button type="button" class="btn btn-dark rounded-pill px-4 btn-sm fw-medium active" data-category="all">All Services</button>
            <button type="button" class="btn btn-outline-dark rounded-pill px-4 btn-sm fw-medium" data-category="identity">Identity &amp; Verifications</button>
            <button type="button" class="btn btn-outline-dark rounded-pill px-4 btn-sm fw-medium" data-category="travel">Travel &amp; Visas</button>
            <button type="button" class="btn btn-outline-dark rounded-pill px-4 btn-sm fw-medium" data-category="legal">Legal &amp; Publications</button>
        </div>
    </div>

    <!-- Services Grid (Matching Homepage 1:1) -->
    <div class="row g-4" id="servicesGrid">
        @if($primaryServices->count() > 0)
            @foreach($primaryServices as $service)
                @php
                    $subServiceNames = $service->subServices->pluck('name')->implode(' ');
                    $searchableName = strtolower($service->name . ' ' . $subServiceNames . ' ' . $service->description);
                    $categoryKey = getServiceCategory($service->name);
                    $categoryLabels = [
                        'identity' => 'Identity & Registry',
                        'travel'   => 'Travel & Consular',
                        'legal'    => 'Legal & Sworn',
                    ];

                    $sn = strtolower($service->name);
                    if (str_contains($sn,'passport')) {
                        $icon = 'bi-passport'; $grad = 'linear-gradient(135deg,#004225,#006435)';
                    } elseif (str_contains($sn,'visa')) {
                        $icon = 'bi-ticket-perforated'; $grad = 'linear-gradient(135deg,#1e40af,#1d4ed8)';
                    } elseif (str_contains($sn,'nin')) {
                        $icon = 'bi-fingerprint'; $grad = 'linear-gradient(135deg,#374151,#111827)';
                    } elseif (str_contains($sn,'bvn')) {
                        $icon = 'bi-bank'; $grad = 'linear-gradient(135deg,#b45309,#92400e)';
                    } elseif (str_contains($sn,'police')) {
                        $icon = 'bi-shield-check'; $grad = 'linear-gradient(135deg,#1e3a5f,#1e40af)';
                    } elseif (str_contains($sn,'affidavit') || str_contains($sn,'court')) {
                        $icon = 'bi-journal-check'; $grad = 'linear-gradient(135deg,#4a1942,#7c3aed)';
                    } elseif (str_contains($sn,'birth') || str_contains($sn,'npc')) {
                        $icon = 'bi-file-earmark-medical'; $grad = 'linear-gradient(135deg,#9d174d,#be185d)';
                    } elseif (str_contains($sn,'licence') || str_contains($sn,'license')) {
                        $icon = 'bi-card-list'; $grad = 'linear-gradient(135deg,#064e3b,#065f46)';
                    } elseif (str_contains($sn,'indigene') || str_contains($sn,'lga')) {
                        $icon = 'bi-house-check'; $grad = 'linear-gradient(135deg,#78350f,#92400e)';
                    } elseif (str_contains($sn,'newspaper')) {
                        $icon = 'bi-newspaper'; $grad = 'linear-gradient(135deg,#374151,#4b5563)';
                    } elseif (str_contains($sn,'emergency') || str_contains($sn,'etc')) {
                        $icon = 'bi-file-earmark-badge'; $grad = 'linear-gradient(135deg,#b91c1c,#991b1b)';
                    } else {
                        $icon = $service->icon ?? 'bi-file-earmark-text'; $grad = 'linear-gradient(135deg,#004225,#065f46)';
                    }

                    $subServicesData = $service->subServices->map(fn($sub) => [
                        'id' => $sub->id,
                        'name' => $sub->name,
                        'price' => $sub->price ?? null,
                        'book_url' => route('client.book', $sub->id),
                        'icon' => $sub->icon ?? 'bi-check-circle',
                    ])->toJson();

                    $hasCustomImage = !empty($service->image_url) && (
                        file_exists(public_path($service->image_url)) ||
                        str_starts_with($service->image_url, 'http')
                    );
                @endphp
                <div class="col-12 col-sm-6 col-lg-4 col-xl-3 service-card-container" data-category="{{ $categoryKey }}" data-name="{{ $searchableName }}">
                    <div class="card service-catalog-card h-100 d-flex flex-column position-relative overflow-hidden">

                        <!-- Image Banner with Fallback to Stylized Gradient Icon -->
                        @if($hasCustomImage)
                            <div class="service-card-image-wrap position-relative" style="height: 155px; background: #0f172a; overflow: hidden;">
                                <img src="{{ asset($service->image_url) }}" alt="{{ $service->name }}" class="service-card-img w-100 h-100" style="object-fit: cover;" loading="lazy">
                                <span class="svc-category-pill">{{ $categoryLabels[$categoryKey] ?? 'Official Service' }}</span>
                                <div class="svc-corner-seal" title="Verified Consular Service">
                                    <i class="bi bi-patch-check-fill text-warning" style="font-size:13px;"></i>
                                </div>
                            </div>
                        @else
                            <div class="svc-icon-header">
                                <div class="svc-icon-bg" style="background: {{ $grad }};"></div>
                                <div class="svc-icon-bg-dots"></div>
                                <span class="svc-category-pill">{{ $categoryLabels[$categoryKey] ?? 'Official Service' }}</span>
                                <div class="svc-corner-seal" title="Verified Consular Service">
                                    <i class="bi bi-patch-check-fill text-warning" style="font-size:13px;"></i>
                                </div>
                                <div class="svc-icon-circle" style="background: {{ $grad }};">
                                    <i class="bi {{ $icon }}"></i>
                                </div>
                            </div>
                        @endif

                        <!-- Card Body -->
                        <div class="p-4 d-flex flex-column flex-grow-1">
                            <div class="mb-2">
                                <span class="badge rounded-pill px-2 py-1 fw-bold" style="font-size:10px;background:rgba(0,66,37,0.08);color:#004225;border:1px solid rgba(0,66,37,0.15);">Primary Service</span>
                                <h3 class="h5 fw-bold mb-0 text-dark mt-2" style="letter-spacing:-0.3px;line-height:1.3;">{{ $service->name }}</h3>
                            </div>

                            <p class="small text-secondary mb-3 flex-grow-1" style="line-height:1.6;font-size:13px;">{{ $service->description }}</p>

                            <!-- Sub-services trigger (iOS modal) -->
                            @if($service->subServices && $service->subServices->count() > 0)
                                <div class="mb-3">
                                    <button class="btn-sub-services w-100 justify-content-center"
                                        onclick="openClientSubModal({{ json_encode($service->name) }}, {{ $subServicesData }}, '{{ route('client.book', $service->id) }}')">
                                        <i class="bi bi-diagram-3-fill"></i>
                                        <span>View Sub-Services</span>
                                        <span class="sub-count-badge">{{ $service->subServices->count() }}</span>
                                    </button>
                                </div>
                            @endif

                            <!-- Card Footer with Fee in USD and Action Button -->
                            <div class="pt-3 border-top d-flex align-items-center justify-content-between gap-2 mt-auto">
                                <div>
                                    <span class="text-muted extra-small d-block" style="font-size: 11px; font-weight: 500;">Pre-Enrollment Fee</span>
                                    <strong class="text-dark fs-5 fw-bold" style="letter-spacing: -0.5px;">${{ number_format($service->price, 2) }}</strong>
                                </div>
                                <a href="{{ route('client.book', $service->id) }}" class="btn btn-sm rounded-pill px-3.5 py-2 fw-semibold text-white text-decoration-none d-inline-flex align-items-center gap-1.5 shadow-sm" style="background:#004225;border-color:#004225;">
                                    <span>Apply Now</span> <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="col-12 text-center py-5 bg-white rounded-4 border">
                <i class="bi bi-box-seam fs-1 text-muted"></i>
                <p class="text-secondary mt-2 small">No services currently available for request.</p>
            </div>
        @endif
    </div>
</div>

<!-- ============================================================
     iOS Sub-Services Modal for Client Dashboard
     ============================================================ -->
<div id="clientSubModal" class="ios-modal-overlay" onclick="closeClientSubModalOnBg(event)">
    <div class="ios-modal-sheet" id="clientModalSheet" onclick="event.stopPropagation()">
        <div class="ios-modal-handle-bar"></div>
        <div class="ios-modal-header">
            <div>
                <div class="ios-modal-title" id="clientModalTitle">Sub-Services</div>
                <div class="ios-modal-subtitle" id="clientModalSubtitle">Available administrative options</div>
            </div>
            <button class="ios-modal-close" onclick="closeClientSubModal()" aria-label="Close modal">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="ios-modal-body" id="clientModalBody">
            <!-- Populated dynamically by JS -->
        </div>
        <div class="ios-modal-cta">
            <a href="#" id="clientModalPrimaryBtn" class="btn text-white w-100 d-flex align-items-center justify-content-center gap-2">
                <i class="bi bi-arrow-right-circle"></i>
                <span>Apply for Primary Service</span>
            </a>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function openClientSubModal(title, subServices, primaryBookUrl) {
        var modal = document.getElementById('clientSubModal');
        var modalTitle = document.getElementById('clientModalTitle');
        var modalSubtitle = document.getElementById('clientModalSubtitle');
        var modalBody = document.getElementById('clientModalBody');
        var primaryBtn = document.getElementById('clientModalPrimaryBtn');

        if (!modal || !modalBody) return;

        modalTitle.textContent = title;
        var count = Array.isArray(subServices) ? subServices.length : 0;
        modalSubtitle.textContent = count + ' administrative options available';
        if (primaryBtn) primaryBtn.href = primaryBookUrl || '#';

        // Render iOS list items
        modalBody.innerHTML = '';
        if (count > 0) {
            subServices.forEach(function(sub) {
                var item = document.createElement('a');
                item.href = sub.book_url || '#';
                item.className = 'ios-sub-item';
                
                var priceHtml = '';
                if (sub.price && parseFloat(sub.price) > 0) {
                    var fmtPrice = new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD', maximumFractionDigits: 0 }).format(sub.price);
                    priceHtml = '<span class="badge bg-light text-dark border extra-small fw-bold px-2 py-1">' + fmtPrice + '</span>';
                } else {
                    priceHtml = '<span class="badge bg-light text-secondary border extra-small px-2 py-1">Direct Quote</span>';
                }

                item.innerHTML = 
                    '<div class="ios-sub-item-icon">' +
                        '<i class="bi ' + (sub.icon || 'bi-file-earmark-check') + '"></i>' +
                    '</div>' +
                    '<div class="flex-grow-1 min-w-0">' +
                        '<div class="ios-sub-item-name text-truncate">' + sub.name + '</div>' +
                        '<div class="ios-sub-item-price">Official verification &amp; fast-track</div>' +
                    '</div>' +
                    '<div class="d-flex align-items-center gap-2 flex-shrink-0">' +
                        priceHtml +
                        '<span class="btn btn-sm btn-dark rounded-pill px-2.5 py-1 extra-small fw-bold" style="background:#004225;font-size:11px;">Apply <i class="bi bi-chevron-right ms-0.5"></i></span>' +
                    '</div>';

                modalBody.appendChild(item);
            });
        } else {
            modalBody.innerHTML = '<div class="text-center py-4 text-secondary small"><i class="bi bi-info-circle fs-3 d-block mb-2"></i>No sub-services listed for this item yet.</div>';
        }

        modal.classList.add('ios-open');
        document.body.style.overflow = 'hidden';
    }

    function closeClientSubModal() {
        var modal = document.getElementById('clientSubModal');
        if (modal) {
            modal.classList.remove('ios-open');
            document.body.style.overflow = '';
        }
    }

    function closeClientSubModalOnBg(e) {
        if (e.target.id === 'clientSubModal') {
            closeClientSubModal();
        }
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeClientSubModal();
        }
    });

    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('serviceSearch');
        const filterButtons = document.querySelectorAll('#serviceCategories button');
        const cardContainers = document.querySelectorAll('.service-card-container');

        let currentCategory = 'all';
        let currentSearch = '';

        function filterServices() {
            let visibleCount = 0;
            cardContainers.forEach(container => {
                const name = container.getAttribute('data-name') || '';
                const category = container.getAttribute('data-category') || '';

                const matchesSearch = !currentSearch || name.includes(currentSearch);
                const matchesCategory = currentCategory === 'all' || category === currentCategory;

                if (matchesSearch && matchesCategory) {
                    container.style.display = 'block';
                    visibleCount++;
                } else {
                    container.style.display = 'none';
                }
            });

            let noResultsMsg = document.getElementById('noServicesFound');
            if (visibleCount === 0) {
                if (!noResultsMsg) {
                    noResultsMsg = document.createElement('div');
                    noResultsMsg.id = 'noServicesFound';
                    noResultsMsg.className = 'col-12 text-center py-5 text-secondary';
                    noResultsMsg.innerHTML = '<i class="bi bi-search fs-1 d-block mb-2"></i><p class="small">No services match your criteria. Please try another search.</p>';
                    document.getElementById('servicesGrid').appendChild(noResultsMsg);
                }
            } else if (noResultsMsg) {
                noResultsMsg.remove();
            }
        }

        if (searchInput) {
            searchInput.addEventListener('input', function (e) {
                currentSearch = e.target.value.toLowerCase().trim();
                filterServices();
            });
        }

        filterButtons.forEach(btn => {
            btn.addEventListener('click', function () {
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
