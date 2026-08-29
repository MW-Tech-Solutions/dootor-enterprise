@extends('layouts.dashboard')

@section('title', 'Add Services - ' . ($settings->platform_name ?? 'Umar Maher'))

@section('content')
<div class="mb-4">
    <h1 class="h3 fw-bold text-dark mb-1">Add Platform Services</h1>
    <p class="text-secondary small">Browse available services on the platform and enable them for your custom storefront catalog</p>
</div>

<div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
    @if(count($availableServices) > 0)
        <div class="row g-4">
            @foreach($availableServices as $service)
                <div class="col-md-6 col-lg-4">
                    <div class="card border border-light h-100 p-3 rounded-3 bg-light d-flex flex-column">
                        <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2.5 py-1 text-uppercase align-self-start small mb-2">
                            {{ $service->name }}
                        </span>
                        
                        <h3 class="h6 fw-bold mb-1">{{ $service->name }} Vetting</h3>
                        <p class="text-secondary small mb-3 flex-grow-1">{{ $service->description }}</p>
                        
                        <hr class="my-3 border-light">
                        
                        <div class="d-flex justify-content-between align-items-center mt-auto">
                            <span class="text-muted small">Base Catalog</span>
                            <button class="btn btn-dark btn-sm rounded-pill px-3" type="button" data-bs-toggle="modal" data-bs-target="#enableModal-{{ $service->id }}">Enable Service</button>
                        </div>
                    </div>
                </div>

                <!-- Enable Service Modal -->
                <div class="modal fade" id="enableModal-{{ $service->id }}" tabindex="-1" aria-labelledby="enableModalLabel-{{ $service->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0 shadow">
                            <div class="modal-header border-bottom">
                                <h5 class="modal-title fw-bold" id="enableModalLabel-{{ $service->id }}">Configure Service</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form action="{{ route('vendor.service.enable') }}" method="POST">
                                @csrf
                                <input type="hidden" name="service_id" value="{{ $service->id }}">
                                <div class="modal-body">
                                    <div class="alert alert-info rounded-3 small mb-3">
                                        Tailor this service to your business. You can define your own price and delivery terms description below.
                                    </div>
                                    <div class="mb-3">
                                        <label for="name-{{ $service->id }}" class="form-label small fw-medium">Storefront Service Title</label>
                                        <input type="text" name="name" id="name-{{ $service->id }}" class="form-control rounded-3" value="{{ old('name', $service->name . ' Processing Support') }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="price-{{ $service->id }}" class="form-label small fw-medium">Your Custom Price ({{ $settings->default_currency ?? 'USD' }})</label>
                                        <input type="number" name="price" id="price-{{ $service->id }}" step="0.01" min="0" class="form-control rounded-3" value="{{ old('price', $service->price) }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="description-{{ $service->id }}" class="form-label small fw-medium">Your Description (Requirements, processing timelines, details)</label>
                                        <textarea name="description" id="description-{{ $service->id }}" class="form-control rounded-3" rows="3" placeholder="e.g. Requires NIN Slip scan. Delivery within 24 hours." required>{{ old('description', $service->description) }}</textarea>
                                    </div>
                                </div>
                                <div class="modal-footer border-top">
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
@endsection
