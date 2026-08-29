@extends('layouts.dashboard')

@section('title', 'Browse Services - ' . ($settings->platform_name ?? 'Umar Maher'))

@section('content')
<div class="mb-4">
    <h1 class="h3 fw-bold text-dark mb-1">Browse Services</h1>
    <p class="text-secondary small">Select a service offered by our verified agents to get started</p>
</div>

<div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
    @if(count($services) > 0)
        <div class="row g-4">
            @foreach($services as $vendorService)
                <div class="col-md-6 col-lg-4">
                    <div class="card border border-light h-100 p-3 rounded-3 bg-light d-flex flex-column shadow-sm-hover transition-all">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2.5 py-1 text-uppercase small">
                                {{ $vendorService->service->name ?? 'Service' }}
                            </span>
                            <span class="fw-bold text-dark">{{ $settings->default_currency ?? 'USD' }} {{ number_format($vendorService->price, 2) }}</span>
                        </div>
                        
                        <h3 class="h6 fw-bold mb-1">{{ $vendorService->name }}</h3>
                        <p class="text-secondary small mb-3 flex-grow-1">{{ $vendorService->description }}</p>
                        
                        <hr class="my-3 border-light">
                        
                        <div class="d-flex justify-content-between align-items-center mt-auto">
                            <!-- Link to storefront -->
                            <a href="{{ route('vendor.storefront', $vendorService->vendor_id) }}" class="text-decoration-none text-dark small d-flex align-items-center gap-1.5 fw-semibold">
                                @if($vendorService->vendor->avatar_url)
                                    <img src="{{ $vendorService->vendor->avatar_url }}" alt="avatar" class="rounded-circle" style="width: 24px; height: 24px; object-fit: cover;">
                                @else
                                    <div class="rounded-circle bg-dark text-white d-flex align-items-center justify-content-center fw-bold" style="width: 24px; height: 24px; font-size: 10px;">
                                        {{ strtoupper(substr($vendorService->vendor->first_name ?? 'V', 0, 1)) }}
                                    </div>
                                @endif
                                <span>{{ $vendorService->vendor->storefrontSetting->storefront_name ?? $vendorService->vendor->name }}</span>
                            </a>
                            
                            <a href="{{ route('client.book', $vendorService->id) }}" class="btn btn-dark btn-sm rounded-pill px-3">Book Now</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-5">
            <i class="bi bi-search fs-1 text-muted"></i>
            <p class="text-secondary mt-2 small">No active services or approved vendors available on the platform currently.</p>
        </div>
    @endif
</div>
@endsection
