@extends('layouts.dashboard')

@section('title', 'Add Services - ' . ($settings->platform_name ?? 'Dooter Enterprises'))

@section('content')
<div class="mb-4">
    <h1 class="h3 fw-bold text-dark mb-1">Add Platform Services &amp; Sub-Services</h1>
    <p class="text-secondary small">Browse available primary services and specialized sub-services to enable on your custom storefront catalog</p>
</div>

<!-- Tabs for Primary vs Sub-Services -->
<div class="card border-0 shadow-sm p-3 rounded-4 bg-white mb-4">
    <ul class="nav nav-pills gap-2" id="vendorServicesTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active rounded-pill px-3 py-1.5" id="all-v-tab" data-bs-toggle="pill" data-bs-target="#all-v-view" type="button" role="tab">
                <i class="bi bi-grid me-1"></i> All Available ({{ count($availableServices) }})
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill px-3 py-1.5" id="primary-v-tab" data-bs-toggle="pill" data-bs-target="#primary-v-view" type="button" role="tab">
                <i class="bi bi-star-fill text-warning me-1"></i> Primary Services ({{ count($primaryServices) }})
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill px-3 py-1.5" id="sub-v-tab" data-bs-toggle="pill" data-bs-target="#sub-v-view" type="button" role="tab">
                <i class="bi bi-diagram-3 me-1"></i> Sub-Services ({{ count($subServices) }})
            </button>
        </li>
    </ul>
</div>

<div class="tab-content" id="vendorServicesTabContent">
    <!-- All Available Tab -->
    <div class="tab-pane fade show active" id="all-v-view" role="tabpanel">
        <div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
            @if(count($availableServices) > 0)
                <div class="row g-4">
                    @foreach($availableServices as $service)
                        <div class="col-md-6 col-lg-4">
                            <div class="card border border-light h-100 p-3 rounded-3 bg-light d-flex flex-column justify-content-between">
                                <div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        @if(!$service->parent_id)
                                            <span class="badge bg-warning text-dark rounded-pill px-2.5 py-1 small">
                                                <i class="bi bi-star-fill me-1"></i> Primary Service
                                            </span>
                                        @else
                                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 small">
                                                <i class="bi bi-arrow-return-right me-1"></i> Mother: {{ $service->parent->name ?? 'Primary' }}
                                            </span>
                                        @endif
                                        <span class="fw-bold text-dark small">{{ $settings->default_currency ?? 'NGN' }} {{ number_format($service->price, 2) }}</span>
                                    </div>
                                    
                                    <h3 class="h6 fw-bold mb-1 text-dark">{{ $service->name }}</h3>
                                    <p class="text-secondary small mb-3 flex-grow-1">{{ $service->short_description ?? Str::limit($service->description, 100) }}</p>
                                </div>
                                
                                <div class="pt-3 border-top mt-auto d-flex justify-content-between align-items-center">
                                    <span class="text-muted small">Base Catalog</span>
                                    <button class="btn btn-dark btn-sm rounded-pill px-3" type="button" data-bs-toggle="modal" data-bs-target="#enableModal-{{ $service->id }}">
                                        Enable Service
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Enable Service Modal -->
                        <div class="modal fade" id="enableModal-{{ $service->id }}" tabindex="-1" aria-labelledby="enableModalLabel-{{ $service->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow rounded-4">
                                    <div class="modal-header border-bottom py-3">
                                        <div>
                                            <h5 class="modal-title fw-bold" id="enableModalLabel-{{ $service->id }}">Configure Service: {{ $service->name }}</h5>
                                            @if($service->parent)
                                                <span class="badge bg-success-subtle text-success small">Sub-Service under: {{ $service->parent->name }}</span>
                                            @endif
                                        </div>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="{{ route('vendor.service.enable') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="service_id" value="{{ $service->id }}">
                                        <div class="modal-body p-4">
                                            <div class="alert alert-info rounded-3 small mb-3">
                                                Tailor this service to your business. You can define your own price and delivery terms description below.
                                            </div>
                                            <div class="mb-3">
                                                <label for="name-{{ $service->id }}" class="form-label small fw-medium">Storefront Service Title</label>
                                                <input type="text" name="name" id="name-{{ $service->id }}" class="form-control rounded-3" value="{{ old('name', $service->name . ' Processing Support') }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="price-{{ $service->id }}" class="form-label small fw-medium">Your Custom Price ({{ $settings->default_currency ?? 'NGN' }})</label>
                                                <input type="number" name="price" id="price-{{ $service->id }}" step="0.01" min="0" class="form-control rounded-3" value="{{ old('price', $service->price) }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="description-{{ $service->id }}" class="form-label small fw-medium">Your Description (Requirements, processing timelines, details)</label>
                                                <textarea name="description" id="description-{{ $service->id }}" class="form-control rounded-3" rows="3" placeholder="e.g. Requires NIN Slip scan. Delivery within 24 hours." required>{{ old('description', $service->description) }}</textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-top py-2.5">
                                            <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-dark rounded-pill px-4">Enable on Storefront</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-patch-check fs-1 text-muted"></i>
                    <p class="text-secondary mt-2 small">You have enabled all available platform services on your storefront catalog.</p>
                    <a href="{{ route('vendor.my-services') }}" class="btn btn-dark rounded-pill px-4 mt-3">Manage Enabled Services</a>
                </div>
            @endif
        </div>
    </div>

    <!-- Primary Services Tab -->
    <div class="tab-pane fade" id="primary-v-view" role="tabpanel">
        <div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
            @if(count($primaryServices) > 0)
                <div class="row g-4">
                    @foreach($primaryServices as $service)
                        <div class="col-md-6 col-lg-4">
                            <div class="card border border-light h-100 p-3 rounded-3 bg-light d-flex flex-column justify-content-between">
                                <div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="badge bg-warning text-dark rounded-pill px-2.5 py-1 small">
                                            <i class="bi bi-star-fill me-1"></i> Primary Service
                                        </span>
                                        <span class="fw-bold text-dark small">{{ $settings->default_currency ?? 'NGN' }} {{ number_format($service->price, 2) }}</span>
                                    </div>
                                    <h3 class="h6 fw-bold mb-1 text-dark">{{ $service->name }}</h3>
                                    <p class="text-secondary small mb-3 flex-grow-1">{{ $service->short_description ?? Str::limit($service->description, 100) }}</p>
                                </div>
                                <div class="pt-3 border-top mt-auto d-flex justify-content-between align-items-center">
                                    <span class="text-muted small">{{ $service->subServices->count() }} Sub-Services</span>
                                    <button class="btn btn-dark btn-sm rounded-pill px-3" type="button" data-bs-toggle="modal" data-bs-target="#enableModal-{{ $service->id }}">
                                        Enable Service
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <p class="text-secondary small mb-0">No primary services available to enable.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Sub-Services Tab -->
    <div class="tab-pane fade" id="sub-v-view" role="tabpanel">
        <div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
            @if(count($subServices) > 0)
                <div class="row g-4">
                    @foreach($subServices as $service)
                        <div class="col-md-6 col-lg-4">
                            <div class="card border border-light h-100 p-3 rounded-3 bg-light d-flex flex-column justify-content-between">
                                <div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 small">
                                            <i class="bi bi-arrow-return-right me-1"></i> Mother: {{ $service->parent->name ?? 'Primary' }}
                                        </span>
                                        <span class="fw-bold text-dark small">{{ $settings->default_currency ?? 'NGN' }} {{ number_format($service->price, 2) }}</span>
                                    </div>
                                    <h3 class="h6 fw-bold mb-1 text-dark">{{ $service->name }}</h3>
                                    <p class="text-secondary small mb-3 flex-grow-1">{{ $service->short_description ?? Str::limit($service->description, 100) }}</p>
                                </div>
                                <div class="pt-3 border-top mt-auto d-flex justify-content-between align-items-center">
                                    <span class="text-muted small">Sub-Service</span>
                                    <button class="btn btn-dark btn-sm rounded-pill px-3" type="button" data-bs-toggle="modal" data-bs-target="#enableModal-{{ $service->id }}">
                                        Enable Sub-Service
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <p class="text-secondary small mb-0">No sub-services available to enable.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
