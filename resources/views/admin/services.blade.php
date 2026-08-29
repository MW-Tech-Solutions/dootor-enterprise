@extends('layouts.dashboard')

@section('title', 'Platform Services - ' . ($settings->platform_name ?? 'Umar Maher'))

@section('content')
<div class="mb-4">
    <h1 class="h3 fw-bold text-dark mb-1">Global Platform Services</h1>
    <p class="text-secondary small">Define and configure the core document catalog list available for vendors to enable</p>
</div>

<div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h5 fw-bold text-dark mb-0">Base Service Catalog</h2>
        <!-- Add New trigger button -->
        <button class="btn btn-dark btn-sm rounded-pill px-3" type="button" data-bs-toggle="modal" data-bs-target="#createServiceModal"><i class="bi bi-plus-circle me-1"></i> Add Service</button>
    </div>

    @if(count($services) > 0)
        <div class="row g-4">
            @foreach($services as $service)
                <div class="col-md-6 col-lg-4">
                    <div class="card border border-light h-100 p-3 rounded-3 bg-light d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            @if($service->status === 'Active')
                                <span class="badge bg-success-subtle text-success rounded-pill px-2.5 py-1">Active</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger rounded-pill px-2.5 py-1">Inactive</span>
                            @endif
                            <span class="fw-bold text-dark">{{ $settings->default_currency ?? 'USD' }} {{ number_format($service->price, 2) }}</span>
                        </div>
                        
                        <h3 class="h6 fw-bold mb-1">{{ $service->name }}</h3>
                        <p class="text-secondary small mb-3 flex-grow-1">{{ $service->description }}</p>
                        
                        <hr class="my-3 border-light">
                        
                        <div class="d-flex justify-content-between align-items-center mt-auto">
                            <span class="text-muted small">ID: #{{ $service->id }}</span>
                            <div class="d-flex gap-1">
                                <!-- Trigger Edit Modal -->
                                <button class="btn btn-outline-dark btn-sm rounded-pill px-3" type="button" data-bs-toggle="modal" data-bs-target="#editServiceModal-{{ $service->id }}">Edit</button>
                                
                                <form action="{{ route('admin.service.delete', $service->id) }}" method="POST" class="m-0" onsubmit="return confirm('Delete this global platform service permanent? This will delete all custom vendor versions.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Edit Global Service Modal -->
                <div class="modal fade" id="editServiceModal-{{ $service->id }}" tabindex="-1" aria-labelledby="editServiceModalLabel-{{ $service->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0 shadow">
                            <div class="modal-header border-bottom">
                                <h5 class="modal-title fw-bold" id="editServiceModalLabel-{{ $service->id }}">Edit Platform Service</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form action="{{ route('admin.service.update', $service->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="edit-name-{{ $service->id }}" class="form-label small fw-medium">Service Name</label>
                                        <input type="text" name="name" id="edit-name-{{ $service->id }}" class="form-control rounded-3" value="{{ old('name', $service->name) }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="edit-price-{{ $service->id }}" class="form-label small fw-medium">Base Default Price ({{ $settings->default_currency ?? 'USD' }})</label>
                                        <input type="number" name="price" id="edit-price-{{ $service->id }}" step="0.01" min="0" class="form-control rounded-3" value="{{ old('price', $service->price) }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="edit-description-{{ $service->id }}" class="form-label small fw-medium">Vetting Description</label>
                                        <textarea name="description" id="edit-description-{{ $service->id }}" class="form-control rounded-3" rows="3" required>{{ old('description', $service->description) }}</textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="edit-status-{{ $service->id }}" class="form-label small fw-medium">Platform Status</label>
                                        <select name="status" id="edit-status-{{ $service->id }}" class="form-select rounded-3">
                                            <option value="Active" {{ $service->status === 'Active' ? 'selected' : '' }}>Active</option>
                                            <option value="Inactive" {{ $service->status === 'Inactive' ? 'selected' : '' }}>Inactive</option>
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
            <p class="text-secondary mt-2 small">No platform services defined yet.</p>
        </div>
    @endif
</div>

<!-- Create Global Service Modal -->
<div class="modal fade" id="createServiceModal" tabindex="-1" aria-labelledby="createServiceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold" id="createServiceModalLabel">Add Platform Service</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.services.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="create-name" class="form-label small fw-medium">Service Name</label>
                        <input type="text" name="name" id="create-name" class="form-control rounded-3" placeholder="e.g. Birth Certificate (NPC)" required>
                    </div>
                    <div class="mb-3">
                        <label for="create-price" class="form-label small fw-medium">Base Default Price ({{ $settings->default_currency ?? 'USD' }})</label>
                        <input type="number" name="price" id="create-price" step="0.01" min="0" class="form-control rounded-3" value="0.00" required>
                    </div>
                    <div class="mb-3">
                        <label for="create-description" class="form-label small fw-medium">Vetting Description</label>
                        <textarea name="description" id="create-description" class="form-control rounded-3" rows="3" placeholder="Describe the document processing details..." required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="create-status" class="form-label small fw-medium">Platform Status</label>
                        <select name="status" id="create-status" class="form-select rounded-3">
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-dark rounded-pill px-4">Create Service</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
