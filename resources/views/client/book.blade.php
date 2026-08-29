@extends('layouts.dashboard')

@section('title', 'Book Service - ' . ($settings->platform_name ?? 'Umar Maher'))

@section('content')
<div class="mb-4">
    <a href="{{ route('client.services') }}" class="btn btn-outline-secondary btn-sm rounded-pill mb-2"><i class="bi bi-arrow-left me-1"></i> Back to Services</a>
    <h1 class="h3 fw-bold text-dark mb-1">Book Service</h1>
    <p class="text-secondary small">Submit your application files to begin processing</p>
</div>

<div class="row g-4">
    <!-- Booking form -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
            <h2 class="h5 fw-bold text-dark mb-4">Application Details</h2>

            <form action="{{ route('client.book.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="vendor_service_id" value="{{ $vendorService->id }}">

                <!-- Requirements note -->
                <div class="alert alert-info rounded-3 mb-4 small" role="alert">
                    <i class="bi bi-info-circle-fill me-2"></i> Ensure all uploaded documents are clearly scanned and under 5MB (PDF, PNG, JPG, or JPEG format).
                </div>

                <!-- Document Upload Section -->
                <div class="mb-4">
                    <label class="form-label small fw-semibold text-dark">Upload Required Documents</label>
                    <div id="fileInputsContainer">
                        <div class="input-group mb-2">
                            <input type="file" name="documents[]" class="form-control rounded-3" required>
                            <button class="btn btn-outline-danger" type="button" onclick="removeInput(this)"><i class="bi bi-trash"></i></button>
                        </div>
                    </div>
                    
                    <button type="button" class="btn btn-outline-dark btn-sm rounded-pill mt-2 small" onclick="addFileInput()">
                        <i class="bi bi-plus-circle me-1"></i> Add Another File
                    </button>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-dark w-100 py-2.5 rounded-3 fw-semibold">Submit Booking Application</button>
            </form>
        </div>
    </div>

    <!-- Right Sidebar: Billing Summary -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
            <h3 class="h6 fw-bold text-dark mb-3">Billing Summary</h3>
            
            <div class="d-flex justify-content-between mb-2 small text-secondary">
                <span>Service Price:</span>
                <span>{{ $settings->default_currency ?? 'USD' }} {{ number_format($vendorService->price, 2) }}</span>
            </div>
            <div class="d-flex justify-content-between mb-2 small text-secondary">
                <span>System processing fee:</span>
                <span>FREE</span>
            </div>
            
            <hr class="my-3 border-light">
            
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="fw-bold text-dark small">Total Charged:</span>
                <span class="fw-bold fs-5 text-dark">{{ $settings->default_currency ?? 'USD' }} {{ number_format($vendorService->price, 2) }}</span>
            </div>

            <div class="p-3 bg-light rounded-3 small">
                <span class="fw-semibold text-dark d-block mb-1"><i class="bi bi-shield-check text-success"></i> Secure Checkout</span>
                <span class="text-secondary small">Your payment will be safely held in escrow until the vendor processes your document details.</span>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function addFileInput() {
        var container = document.getElementById('fileInputsContainer');
        var div = document.createElement('div');
        div.className = 'input-group mb-2';
        div.innerHTML = `
            <input type="file" name="documents[]" class="form-control rounded-3" required>
            <button class="btn btn-outline-danger" type="button" onclick="removeInput(this)"><i class="bi bi-trash"></i></button>
        `;
        container.appendChild(div);
    }

    function removeInput(btn) {
        var container = document.getElementById('fileInputsContainer');
        // Prevent deleting the only input
        if (container.children.length > 1) {
            btn.closest('.input-group').remove();
        } else {
            alert('At least one file upload input is required.');
        }
    }
</script>
@endsection
