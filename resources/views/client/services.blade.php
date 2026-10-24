@extends('layouts.dashboard')

@section('title', 'Services & Sub-Services Catalog - ' . ($settings->platform_name ?? 'DOOTOR ENTERPRISES'))

@section('styles')
@parent
<style>
    .service-card {
        border: 1px solid #eef2f6 !important;
        border-radius: 20px !important;
        background: #ffffff;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.03);
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        position: relative;
        padding: 22px 20px !important;
    }
    .service-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 14px 28px -6px rgba(0, 66, 37, 0.12), 0 4px 8px -4px rgba(0, 0, 0, 0.04);
        border-color: #004225 !important;
    }
    .service-icon-box {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        background: #004225;
        color: #d4af37;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        flex-shrink: 0;
        box-shadow: 0 4px 10px rgba(0, 66, 37, 0.15);
    }
    .btn-brand-primary {
        background-color: #004225 !important;
        border: none !important;
        color: #ffffff !important;
        font-weight: 600;
        transition: all 0.2s ease;
    }
    .btn-brand-primary:hover {
        background-color: #002b18 !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 66, 37, 0.25);
    }
    .badge-mother-service {
        background-color: #e6f4ea;
        color: #004225;
        font-weight: 600;
        border: 1px solid #b7e1cd;
        border-radius: 9999px;
        padding: 4px 10px;
        font-size: 11px;
    }
    .filter-btn {
        background: #ffffff;
        color: #334155;
        border: 1px solid #e2e8f0 !important;
        font-weight: 500;
        transition: all 0.2s ease;
        white-space: nowrap;
    }
    .filter-btn:hover {
        border-color: #004225 !important;
        color: #004225;
    }
    .filter-btn.active {
        background-color: #004225 !important;
        color: #ffffff !important;
        border-color: #004225 !important;
        font-weight: 600;
    }
</style>
@endsection

@section('content')
<div class="container-fluid px-0">
    <!-- Header Section (Exact Match to Image 2) -->
    <div class="mb-3">
        <div class="d-flex align-items-center justify-content-between mb-2 flex-wrap gap-2">
            <span class="badge" style="background-color: #e6f4ea; color: #004225; border-radius: 50px; font-weight: 700; font-size: 10px; padding: 6px 12px;">
                <i class="bi bi-shield-check me-1"></i> VERIFIED CONSULAR &amp; DOCUMENT SERVICES
            </span>
            <span class="badge" style="background-color: #ecfdf5; color: #004225; border-radius: 50px; font-weight: 600; font-size: 10px; padding: 6px 12px;">
                <i class="bi bi-link-45deg me-1"></i> Billing Currency: {{ $currencyCode ?? 'USD' }}
            </span>
        </div>
        <h1 class="h3 fw-bold text-dark mb-1" style="font-weight: 800; font-size: 1.55rem; letter-spacing: -0.02em;">Official Services &amp; Sub-Services Catalog</h1>
        <p class="text-secondary small mb-3" style="color: #64748b; font-size: 13px; line-height: 1.45;">Browse verified primary consular services and specialized sub-services to book your application.</p>
    </div>

    <!-- Search Bar (Full Width, Matching Image 2) -->
    <div class="position-relative mb-3">
        <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-secondary" style="font-size: 14px;"></i>
        <input type="text" id="serviceSearchInput" class="form-control rounded-pill ps-5 py-2.5 bg-white border" placeholder="Search services or sub-services..." style="border-color: #e2e8f0; font-size: 13.5px; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
    </div>

    <!-- Filter Buttons (Matching Image 2) -->
    <div class="d-flex align-items-center gap-2 mb-4 overflow-x-auto pb-1" style="scrollbar-width: none;">
        <button type="button" class="btn btn-sm rounded-pill px-3 py-1.5 filter-btn" data-filter="all" style="font-size: 12px;">
            <i class="bi bi-grid me-1"></i> All Services
        </button>
        <button type="button" class="btn btn-sm rounded-pill px-3 py-1.5 filter-btn active" data-filter="primary" style="font-size: 12px;">
            <i class="bi bi-star-fill text-warning me-1"></i> Primary Services ({{ $primaryServices->count() }})
        </button>
        <button type="button" class="btn btn-sm rounded-pill px-3 py-1.5 filter-btn" data-filter="subservice" style="font-size: 12px;">
            <i class="bi bi-diagram-3 me-1"></i> Sub-Services ({{ $subServices->count() }})
        </button>
    </div>

    <!-- Primary Services Section -->
    <div class="mb-5 section-group" id="primaryServicesSection">
        @if($primaryServices->count() > 0)
            <div class="row g-3 g-md-4" id="primaryServicesGrid">
                @foreach($primaryServices as $service)
                    @php
                        // Pick icon matching Image 2
                        $nameLower = strtolower($service->name);
                        $serviceIcon = match(true) {
                            str_contains($nameLower, 'authorization') || str_contains($nameLower, 'letter') || str_contains($nameLower, 'attorney') => 'bi-file-earmark-text',
                            str_contains($nameLower, 'emergency') || str_contains($nameLower, 'travel') || str_contains($nameLower, 'certificate') => 'bi-airplane',
                            str_contains($nameLower, 'nin') || str_contains($nameLower, 'identification') => 'bi-card-heading',
                            str_contains($nameLower, 'passport') => 'bi-book',
                            str_contains($nameLower, 'waiver') || str_contains($nameLower, 'appointment') => 'bi-calendar-check',
                            str_contains($nameLower, 'collection') || str_contains($nameLower, 'same day') => 'bi-clock-history',
                            default => ($service->icon ?? 'bi-box-seam')
                        };
                    @endphp
                    <div class="col-12 col-md-6 col-lg-4 service-card-wrapper" data-name="{{ strtolower($service->name . ' ' . $service->description) }}" data-type="primary">
                        <div class="service-card h-100 d-flex flex-column" style="padding: 22px 20px !important;">
                            <div class="d-flex align-items-start gap-3 mb-3">
                                <div class="service-icon-box">
                                    <i class="bi {{ $serviceIcon }}"></i>
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <div class="d-flex align-items-center gap-1.5 mb-1.5 flex-wrap">
                                        <span class="badge rounded-pill px-2.5 py-1" style="background-color: #ecfdf5; color: #004225; font-size: 10px; font-weight: 600;">
                                            <i class="bi bi-clock me-1"></i> {{ $service->processing_days ? $service->processing_days . ' Days' : '5 Days' }}
                                        </span>
                                        <span class="badge rounded-pill px-2.5 py-1" style="background-color: #fef3c7; color: #92400e; font-size: 10px; font-weight: 600;">
                                            Primary Mother
                                        </span>
                                    </div>
                                    <h3 class="h6 fw-bold text-dark mb-0.5" style="font-weight: 800; font-size: 15px;">{{ $service->name }}</h3>
                                    <p class="text-secondary small mb-0" style="font-size: 12px; color: #64748b !important;">
                                        {{ $service->short_description ?? Str::limit($service->description, 80) }}
                                    </p>
                                </div>
                            </div>

                            @if($service->subServices->count() > 0)
                                <div class="p-2.5 rounded-3 mb-3" style="background-color: #f0fdf4; border: 1px solid #dcfce7; border-radius: 12px !important;">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-uppercase fw-bold text-secondary d-flex align-items-center gap-1.5" style="font-size: 10.5px; letter-spacing: 0.03em;">
                                            <i class="bi bi-people text-success"></i> SUB-SERVICES ({{ $service->subServices->count() }})
                                        </span>
                                        <a href="javascript:void(0)" class="text-success small fw-bold text-decoration-none d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#subServiceModal{{ $service->id }}" style="font-size: 11px; color: #004225 !important;">
                                            <span>View all</span> <i class="bi bi-arrow-right"></i>
                                        </a>
                                    </div>
                                    <div class="d-flex flex-column gap-1.5">
                                        @foreach($service->subServices->take(3) as $sub)
                                            <div class="d-flex justify-content-between align-items-center py-1 border-bottom border-light-subtle" style="font-size: 12px;">
                                                <span class="text-secondary text-truncate me-2">{{ $sub->name }}</span>
                                                <span class="fw-bold text-dark flex-shrink-0">{{ $currencySymbol ?? '$' }}{{ number_format($sub->price, 2) }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center pt-2.5 border-top mt-auto" style="border-color: #f1f5f9 !important;">
                                    <div>
                                        <span class="text-secondary d-block" style="font-size: 10px;">Starting From</span>
                                        <span class="fw-bold text-dark fs-5" style="font-weight: 800;">{{ $currencySymbol ?? '$' }}{{ number_format($service->price, 2) }}</span>
                                    </div>
                                    <button class="btn btn-brand-primary rounded-pill px-3.5 py-2 fw-bold d-flex align-items-center gap-1.5" style="font-size: 12.5px;" data-bs-toggle="modal" data-bs-target="#subServiceModal{{ $service->id }}">
                                        <span>Choose Option</span>
                                        <i class="bi bi-arrow-right"></i>
                                    </button>
                                </div>
                            @else
                                <div class="pt-2.5 border-top mt-auto" style="border-color: #f1f5f9 !important;">
                                    <div class="mb-2">
                                        <span class="text-secondary d-block" style="font-size: 10px;">Standard Rate</span>
                                        <span class="fw-bold text-dark fs-5" style="font-weight: 800;">{{ $currencySymbol ?? '$' }}{{ number_format($service->price, 2) }}</span>
                                    </div>
                                    <a href="{{ route('client.book', $service->id) }}" class="btn btn-brand-primary w-100 rounded-pill py-2.5 fw-bold d-flex align-items-center justify-content-center gap-1.5" style="font-size: 13px;">
                                        <span>Start Application</span>
                                        <i class="bi bi-arrow-right"></i>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Modal for Sub-services -->
                    @if($service->subServices->count() > 0)
                        <div class="modal fade" id="subServiceModal{{ $service->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content border-0 shadow rounded-4">
                                    <div class="modal-header border-bottom py-3">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="service-icon-box" style="width: 42px; height: 42px; font-size: 18px;">
                                                <i class="bi {{ $service->icon ?? 'bi-box-seam' }}"></i>
                                            </div>
                                            <div>
                                                <h5 class="modal-title fw-bold text-dark fs-6">{{ $service->name }}</h5>
                                                <p class="text-muted small mb-0">Select your specific sub-service requirement or apply for the base service</p>
                                            </div>
                                        </div>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body p-4">
                                        <!-- Base primary option -->
                                        <div class="p-3 bg-light border rounded-3 mb-3 d-flex align-items-center justify-content-between">
                                            <div>
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="badge bg-warning text-dark rounded-pill px-2 py-0.5" style="font-size: 10px;">Primary Package</span>
                                                    <h6 class="fw-bold mb-0 text-dark">{{ $service->name }} (Standard Base)</h6>
                                                </div>
                                                <p class="text-secondary small mb-0 mt-1" style="font-size: 12px;">{{ $service->short_description ?? 'Complete primary service processing' }}</p>
                                            </div>
                                            <div class="text-end ms-3">
                                                <span class="fw-bold text-dark d-block fs-6">{{ $currencySymbol ?? '$' }}{{ number_format($service->price, 2) }}</span>
                                                <a href="{{ route('client.book', $service->id) }}" class="btn btn-outline-dark btn-sm rounded-pill px-3 py-1 mt-1" style="font-size: 12px;">
                                                    Select Base <i class="bi bi-arrow-right"></i>
                                                </a>
                                            </div>
                                        </div>

                                        <h6 class="text-uppercase text-muted fw-bold mb-2 small" style="font-size: 11px; letter-spacing: 0.5px;">
                                            <i class="bi bi-diagram-3-fill text-success me-1"></i> Available Sub-Services ({{ $service->subServices->count() }})
                                        </h6>

                                        <div class="d-flex flex-column gap-2.5">
                                            @foreach($service->subServices as $sub)
                                                <a href="{{ route('client.book', $sub->id) }}" class="p-3 border rounded-3 text-decoration-none text-dark d-flex align-items-center justify-content-between hover-bg-light transition-all" style="background: #ffffff;">
                                                    <div>
                                                        <div class="d-flex align-items-center gap-2">
                                                            <i class="bi bi-arrow-return-right text-success"></i>
                                                            <h6 class="fw-bold mb-0 text-dark">{{ $sub->name }}</h6>
                                                        </div>
                                                        <p class="text-secondary small mb-0 mt-1 ms-4" style="font-size: 12px;">{{ $sub->short_description ?? 'Official processing & verification' }}</p>
                                                    </div>
                                                    <div class="text-end ms-3">
                                                        <span class="fw-bold text-success d-block" style="color: #004225 !important;">{{ $currencySymbol ?? '$' }}{{ number_format($sub->price, 2) }}</span>
                                                        <span class="btn btn-outline-success btn-sm py-0.5 px-2.5 mt-1 rounded-pill" style="font-size: 11px; border-color: #004225; color: #004225;">Select <i class="bi bi-arrow-right"></i></span>
                                                    </div>
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        @else
            <div class="text-center py-5 bg-white rounded-4 border">
                <i class="bi bi-box-seam fs-1 text-muted"></i>
                <p class="text-secondary mt-2 small">No primary services available at the moment.</p>
            </div>
        @endif
    </div>

    <!-- All Sub-Services Direct Grid Section -->
    @if($subServices->count() > 0)
        <div class="mb-5 section-group" id="subServicesSection">
            <div class="d-flex justify-content-between align-items-center mb-3 border-top pt-4">
                <div>
                    <h2 class="h5 fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-diagram-3-fill text-success"></i> All Sub-Services by Mother Service ({{ $subServices->count() }})
                    </h2>
                    <p class="text-secondary small mb-0">Directly select and initiate processing for any specialized sub-service.</p>
                </div>
            </div>

            <div class="row g-3" id="subServicesGrid">
                @foreach($subServices as $sub)
                    <div class="col-12 col-md-6 col-lg-4 service-card-wrapper" data-name="{{ strtolower($sub->name . ' ' . ($sub->parent->name ?? '') . ' ' . $sub->description) }}" data-type="subservice">
                        <div class="card border-0 shadow-sm h-100 rounded-4 bg-white d-flex flex-column justify-content-between" style="border: 1px solid #eef2f6 !important; border-radius: 20px !important; padding: 22px 20px !important;">
                            <div>
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    @if($sub->parent)
                                        <span class="badge-mother-service">
                                            <i class="bi bi-star-fill text-warning me-1"></i> Mother: {{ $sub->parent->name }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2 py-0.5 small">Sub-Service</span>
                                    @endif
                                    <span class="badge bg-light text-dark border px-2 py-0.5 rounded-pill small">
                                        {{ $sub->processing_days ? $sub->processing_days . ' Days' : 'Fast-track' }}
                                    </span>
                                </div>

                                <h3 class="h6 fw-bold text-dark mb-1">{{ $sub->name }}</h3>
                                <p class="text-secondary small mb-3" style="font-size: 12px; line-height: 1.5;">
                                    {{ $sub->short_description ?? Str::limit($sub->description, 100) }}
                                </p>
                            </div>

                            <div class="pt-2.5 border-top d-flex justify-content-between align-items-center mt-auto">
                                <div>
                                    <span class="text-muted d-block" style="font-size: 10px;">Service Fee</span>
                                    <span class="fw-bold text-dark fs-6">{{ $currencySymbol ?? '$' }}{{ number_format($sub->price, 2) }}</span>
                                </div>
                                <a href="{{ route('client.book', $sub->id) }}" class="btn btn-outline-success btn-sm rounded-pill px-3 py-1" style="border-color: #004225; color: #004225;">
                                    Book Now <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const filterBtns = document.querySelectorAll('.filter-btn');
        const searchInput = document.getElementById('serviceSearchInput');
        const cards = document.querySelectorAll('.service-card-wrapper');
        const primarySection = document.getElementById('primaryServicesSection');
        const subSection = document.getElementById('subServicesSection');

        let currentFilter = 'all';
        let currentSearch = '';

        function applyFilters() {
            cards.forEach(card => {
                const cardType = card.getAttribute('data-type');
                const cardName = card.getAttribute('data-name') || '';

                const matchesFilter = (currentFilter === 'all') || (currentFilter === cardType);
                const matchesSearch = !currentSearch || cardName.includes(currentSearch);

                if (matchesFilter && matchesSearch) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });

            if (currentFilter === 'subservice') {
                if (primarySection) primarySection.style.display = 'none';
                if (subSection) subSection.style.display = '';
            } else if (currentFilter === 'primary') {
                if (primarySection) primarySection.style.display = '';
                if (subSection) subSection.style.display = 'none';
            } else {
                if (primarySection) primarySection.style.display = '';
                if (subSection) subSection.style.display = '';
            }
        }

        filterBtns.forEach(btn => {
            btn.addEventListener('click', function () {
                filterBtns.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                currentFilter = this.getAttribute('data-filter');
                applyFilters();
            });
        });

        if (searchInput) {
            searchInput.addEventListener('input', function () {
                currentSearch = this.value.toLowerCase().trim();
                applyFilters();
            });
        }
    });
</script>
@endsection
