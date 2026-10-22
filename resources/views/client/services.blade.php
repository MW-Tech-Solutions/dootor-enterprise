@extends('layouts.dashboard')

@section('title', 'Services & Sub-Services Catalog - ' . ($settings->platform_name ?? 'DOOTOR ENTERPRISES'))

@section('styles')
<style>
    .service-card {
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        background: #ffffff;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        position: relative;
        overflow: hidden;
    }
    .service-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 14px 28px -6px rgba(0, 66, 37, 0.12), 0 4px 8px -4px rgba(0, 0, 0, 0.04);
        border-color: #004225;
    }
    .service-icon-box {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        background: linear-gradient(135deg, #004225 0%, #002411 100%);
        color: #d4af37;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        box-shadow: 0 4px 10px rgba(0, 66, 37, 0.2);
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
    .subservice-pill {
        background: #f8fafc;
        color: #334155;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 8px 12px;
        font-size: 13px;
        font-weight: 500;
        transition: all 0.2s ease;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .subservice-pill:hover {
        background: #004225;
        color: #ffffff;
        border-color: #004225;
    }
    .subservice-pill:hover .text-muted {
        color: #d4af37 !important;
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
    .filter-btn.active {
        background-color: #004225 !important;
        color: #ffffff !important;
        border-color: #004225 !important;
    }
</style>
@endsection

@section('content')
<div class="container-fluid px-0">
    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <span class="badge bg-success-subtle text-success px-3 py-1.5 rounded-pill font-monospace small fw-bold text-uppercase mb-2" style="color: #004225 !important; background-color: #e6f4ea !important;">
                <i class="bi bi-shield-check me-1"></i> Verified Consular &amp; Document Services
            </span>
            <h1 class="h3 fw-bold text-dark mb-1">Official Services &amp; Sub-Services Catalog</h1>
            <p class="text-secondary small mb-0">Browse verified primary consular services and specialized sub-services to book your application.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-white border text-dark p-2 px-3 rounded-pill shadow-sm small font-monospace">
                <i class="bi bi-currency-exchange text-success me-1"></i> Billing Currency: <strong>{{ $currencyCode ?? 'NGN' }}</strong>
            </span>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="card border-0 shadow-sm p-3 rounded-4 bg-white mb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
            <div class="d-flex flex-wrap gap-2 w-100 w-md-auto">
                <button type="button" class="btn btn-sm btn-outline-dark rounded-pill px-3 py-1.5 filter-btn active" data-filter="all">
                    <i class="bi bi-grid me-1"></i> All Services
                </button>
                <button type="button" class="btn btn-sm btn-outline-dark rounded-pill px-3 py-1.5 filter-btn" data-filter="primary">
                    <i class="bi bi-star-fill text-warning me-1"></i> Primary Services ({{ $primaryServices->count() }})
                </button>
                <button type="button" class="btn btn-sm btn-outline-dark rounded-pill px-3 py-1.5 filter-btn" data-filter="subservice">
                    <i class="bi bi-diagram-3 me-1"></i> Sub-Services ({{ $subServices->count() }})
                </button>
            </div>
            <div class="w-100 w-md-auto" style="min-width: 260px;">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light border-end-0 rounded-start-pill"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" id="serviceSearchInput" class="form-control bg-light border-start-0 rounded-end-pill" placeholder="Search services or sub-services...">
                </div>
            </div>
        </div>
    </div>

    <!-- Primary Services Section -->
    <div class="mb-5 section-group" id="primaryServicesSection">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="h5 fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <i class="bi bi-star-fill text-warning"></i> Primary Consular Services ({{ $primaryServices->count() }})
            </h2>
            <span class="text-muted small">Select any primary service to begin or view sub-services</span>
        </div>

        @if($primaryServices->count() > 0)
            <div class="row g-4" id="primaryServicesGrid">
                @foreach($primaryServices as $service)
                    <div class="col-12 col-md-6 col-lg-4 service-card-wrapper" data-name="{{ strtolower($service->name . ' ' . $service->description) }}" data-type="primary">
                        <div class="service-card p-4 h-100 d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="service-icon-box">
                                    <i class="bi {{ $service->icon ?? 'bi-box-seam' }}"></i>
                                </div>
                                <div class="d-flex flex-column align-items-end gap-1">
                                    <span class="badge bg-light text-dark border px-2.5 py-1 rounded-pill small fw-semibold">
                                        {{ $service->processing_days ? $service->processing_days . ' Days' : 'Fast-track' }}
                                    </span>
                                    <span class="badge bg-warning-subtle text-dark rounded-pill px-2 py-0.5" style="font-size: 10px;">Primary Mother</span>
                                </div>
                            </div>

                            <h3 class="h5 fw-bold text-dark mb-2">{{ $service->name }}</h3>
                            <p class="text-secondary small mb-3 flex-grow-1" style="line-height: 1.5;">
                                {{ $service->short_description ?? Str::limit($service->description, 110) }}
                            </p>

                            @if($service->subServices->count() > 0)
                                <div class="bg-light p-2.5 rounded-3 mb-3 border">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-uppercase text-muted fw-bold" style="font-size: 10px; letter-spacing: 0.5px;">
                                            <i class="bi bi-diagram-3-fill text-success me-1"></i> Sub-Services ({{ $service->subServices->count() }}):
                                        </span>
                                        <a href="javascript:void(0)" class="text-success small fw-semibold text-decoration-none" data-bs-toggle="modal" data-bs-target="#subServiceModal{{ $service->id }}" style="font-size: 11px;">
                                            View all <i class="bi bi-chevron-right"></i>
                                        </a>
                                    </div>
                                    <div class="d-flex flex-column gap-1.5">
                                        @foreach($service->subServices->take(3) as $sub)
                                            <a href="{{ route('client.book', $sub->id) }}" class="subservice-pill">
                                                <span>{{ $sub->name }}</span>
                                                <span class="text-muted small fw-bold">{{ $currencySymbol ?? '$' }}{{ number_format($sub->price, 2) }}</span>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <div class="pt-3 border-top d-flex justify-content-between align-items-center mt-auto">
                                <div>
                                    <span class="text-muted d-block" style="font-size: 11px;">{{ $service->subServices->count() > 0 ? 'Starting From' : 'Standard Rate' }}</span>
                                    <span class="fw-bold text-dark fs-5">{{ $currencySymbol ?? '$' }}{{ number_format($service->price, 2) }}</span>
                                </div>

                                @if($service->subServices->count() > 0)
                                    <button class="btn btn-brand-primary btn-sm px-3 py-2 rounded-3 d-flex align-items-center gap-1.5" data-bs-toggle="modal" data-bs-target="#subServiceModal{{ $service->id }}">
                                        <span>Choose Option</span>
                                        <i class="bi bi-chevron-right small"></i>
                                    </button>
                                @else
                                    <a href="{{ route('client.book', $service->id) }}" class="btn btn-brand-primary btn-sm px-3 py-2 rounded-3 d-flex align-items-center gap-1.5">
                                        <span>Start Application</span>
                                        <i class="bi bi-arrow-right small"></i>
                                    </a>
                                @endif
                            </div>
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
                        <div class="card border h-100 p-3.5 rounded-3 bg-white shadow-sm d-flex flex-column justify-content-between">
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
