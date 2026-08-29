@extends('layouts.dashboard')

@section('title', 'My Services - ' . ($settings->platform_name ?? 'Umar Maher'))

@section('content')
<div class="mb-4">
    <h1 class="h3 fw-bold text-dark mb-1">My Managed Services</h1>
    <p class="text-secondary small">Edit rates, custom details, and active status for your enabled storefront services</p>
</div>

<div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h5 fw-bold text-dark mb-0">Active Storefront Catalog</h2>
        <a href="{{ route('vendor.services') }}" class="btn btn-dark btn-sm rounded-pill px-3"><i class="bi bi-plus-circle me-1"></i> Add Service</a>
    </div>

    @if(count($vendorServices) > 0)
        <div class="row g-4">
            @foreach($vendorServices as $vendorService)
                <div class="col-md-6 col-lg-4">
                    <div class="card border border-light h-100 p-3 rounded-3 bg-light d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2.5 py-1 text-uppercase small">
                                {{ $vendorService->service->name }}
                            </span>
                            @if($vendorService->status === 'Active')
                                <span class="badge bg-success-subtle text-success rounded-pill px-2.5 py-1">Active</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger rounded-pill px-2.5 py-1">Inactive</span>
                            @endif
                        </div>
                        
                        <h3 class="h6 fw-bold mb-1">{{ $vendorService->name }}</h3>
                        <p class="text-secondary small mb-3 flex-grow-1">{{ $vendorService->description }}</p>
                        
                        <hr class="my-3 border-light">
                        
                        <div class="d-flex justify-content-between align-items-center mt-auto">
                            <span class="fw-bold fs-5 text-dark">{{ $settings->default_currency ?? 'USD' }} {{ number_format($vendorService->price, 2) }}</span>
                            
                            <div class="d-flex gap-1">
                                <!-- Trigger Edit Modal -->
                                <button class="btn btn-outline-dark btn-sm rounded-pill" type="button" data-bs-toggle="modal" data-bs-target="#editModal-{{ $vendorService->id }}">Edit</button>
                                
                                <form action="{{ route('vendor.service.delete', $vendorService->id) }}" method="POST" class="m-0" onsubmit="return confirm('Are you sure you want to disable this service?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Edit Modal -->
                <div class="modal fade" id="editModal-{{ $vendorService->id }}" tabindex="-1" aria-labelledby="editModalLabel-{{ $vendorService->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0 shadow">
                            <div class="modal-header border-bottom">
                                <h5 class="modal-title fw-bold" id="editModalLabel-{{ $vendorService->id }}">Edit Service Details</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form action="{{ route('vendor.service.update', $vendorService->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="name-{{ $vendorService->id }}" class="form-label small fw-medium">Service Title</label>
                                        <input type="text" name="name" id="name-{{ $vendorService->id }}" class="form-control rounded-3" value="{{ old('name', $vendorService->name) }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="price-{{ $vendorService->id }}" class="form-label small fw-medium">Price ({{ $settings->default_currency ?? 'USD' }})</label>
                                        <input type="number" name="price" id="price-{{ $vendorService->id }}" step="0.01" min="0" class="form-control rounded-3" value="{{ old('price', $vendorService->price) }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="description-{{ $vendorService->id }}" class="form-label small fw-medium">Description</label>
                                        <textarea name="description" id="description-{{ $vendorService->id }}" class="form-control rounded-3" rows="3" required>{{ old('description', $vendorService->description) }}</textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="status-{{ $vendorService->id }}" class="form-label small fw-medium">Catalog Status</label>
                                        <select name="status" id="status-{{ $vendorService->id }}" class="form-select rounded-3">
                                            <option value="Active" {{ $vendorService->status === 'Active' ? 'selected' : '' }}>Active</option>
                                            <option value="Inactive" {{ $vendorService->status === 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer border-top">
                                    <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-dark rounded-pill px-4">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-5">
            <i class="bi bi-box-seam fs-1 text-muted"></i>
            <p class="text-secondary mt-2 small mb-4">You have not enabled any services on your storefront catalog yet.</p>
            <a href="{{ route('vendor.services') }}" class="btn btn-dark rounded-pill px-4">Browse Platform Services</a>
        </div>
    @endif
</div>
@endsection
