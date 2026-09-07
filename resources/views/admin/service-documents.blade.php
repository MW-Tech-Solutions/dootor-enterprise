@extends('layouts.dashboard')

@section('title', 'Service Document Checklists - Service Assignment')

@section('content')
<div class="mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <h4 class="fw-bold text-dark mb-1">Service Document Checklists</h4>
        <p class="text-secondary small mb-0">Select an active service and assign the required upload documents using checkboxes.</p>
    </div>
    <a href="{{ route('admin.document-types') }}" class="btn btn-outline-dark rounded-pill px-4 btn-sm">
        <i class="bi bi-folder-plus me-1"></i> Manage Master Document Types
    </a>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@php
    $rawAssigned = optional($selectedService)->required_documents;
    if (is_string($rawAssigned)) {
        $rawAssigned = json_decode($rawAssigned, true) ?: [];
    }
    $currentAssigned = is_array($rawAssigned) ? array_map('trim', $rawAssigned) : [];

    $isDocAssigned = function($typeName) use ($currentAssigned) {
        if (in_array($typeName, $currentAssigned)) {
            return true;
        }
        $cleanType = strtolower(preg_replace('/[^a-z0-9]/i', '', $typeName));
        foreach ($currentAssigned as $assignedItem) {
            $cleanAssigned = strtolower(preg_replace('/[^a-z0-9]/i', '', $assignedItem));
            if ($cleanType === $cleanAssigned) return true;
            if (strlen($cleanAssigned) > 3 && (str_contains($cleanType, $cleanAssigned) || str_contains($cleanAssigned, $cleanType))) {
                return true;
            }
        }
        return false;
    };

    $tickedCount = 0;
    if ($selectedService) {
        foreach ($documentTypes as $cat => $typesGroup) {
            foreach ($typesGroup as $dt) {
                if ($isDocAssigned($dt->name)) {
                    $tickedCount++;
                }
            }
        }
    }
@endphp

<div class="row g-4">
    <!-- Service Selector Column -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
            <h5 class="fw-bold text-dark mb-3">1. Select Target Service</h5>
            <form action="{{ route('admin.service-documents') }}" method="GET">
                <div class="mb-3">
                    <label class="form-label small text-muted">Active Platform Services ({{ $services->count() }} Total)</label>
                    <select name="service_id" class="form-select form-select-lg rounded-3" onchange="this.form.submit()">
                        @foreach($services as $svc)
                            <option value="{{ $svc->id }}" {{ optional($selectedService)->id === $svc->id ? 'selected' : '' }}>
                                {{ $svc->name }} (${{ number_format($svc->price, 2) }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>

            @if($selectedService)
            <div class="p-3 bg-light rounded-3 mt-3">
                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1 mb-2">{{ $selectedService->category ?? 'General' }}</span>
                <h6 class="fw-bold text-dark mb-1">{{ $selectedService->name }}</h6>
                <p class="small text-secondary mb-2">{{ $selectedService->description }}</p>
                
                <div class="small fw-semibold text-dark mt-2 border-top pt-2">
                    Current Assigned Checklists:
                    <span class="badge bg-dark text-white rounded-pill ms-1">
                        {{ $tickedCount }} Ticked Documents
                    </span>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Document Checkboxes Column -->
    <div class="col-lg-8">
        @if($selectedService)
        <div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                <div>
                    <h5 class="fw-bold text-dark mb-0">2. Assign Document Requirements</h5>
                    <span class="small text-muted">Check all documents client must upload for {{ $selectedService->name }}</span>
                </div>
                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" onclick="toggleAllCheckboxes(true)">Select All</button>
            </div>

            <form action="{{ route('admin.service-documents.update', $selectedService->id) }}" method="POST">
                @csrf
                @method('PATCH')

                <div class="d-flex flex-column gap-4 my-3">
                    @foreach($documentTypes as $category => $types)
                    <div>
                        <h6 class="fw-bold text-uppercase text-secondary small tracking-wider mb-3 px-2 py-1 bg-light rounded-3 border-start border-3 border-success">
                            <i class="bi bi-folder2-open me-2"></i> {{ $category }} Documents
                        </h6>

                        <div class="row g-3">
                            @foreach($types as $docType)
                            @php
                                $isChecked = $isDocAssigned($docType->name);
                            @endphp
                            <div class="col-md-6">
                                <div class="p-3 border rounded-3 h-100 bg-white hover-shadow transition" style="border-color: {{ $isChecked ? '#004225' : '#e2e8f0' }}; background-color: {{ $isChecked ? 'rgba(0, 66, 37, 0.03)' : '#ffffff' }};">
                                    <div class="form-check">
                                        <input class="form-check-input doc-checkbox" type="checkbox" name="required_documents[]" value="{{ $docType->name }}" id="docCheck{{ $docType->id }}" {{ $isChecked ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold text-dark small ms-1" for="docCheck{{ $docType->id }}">
                                            {{ $docType->name }}
                                        </label>
                                    </div>
                                    @if($docType->description)
                                    <p class="small text-muted mb-0 mt-1 ms-4" style="font-size: 11.5px;">{{ $docType->description }}</p>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="border-top pt-3 mt-4 d-flex justify-content-between align-items-center">
                    <span class="small text-muted"><i class="bi bi-info-circle me-1"></i> Changes will immediately take effect on client booking pages.</span>
                    <button type="submit" class="btn text-white rounded-pill px-4 py-2 fw-bold" style="background-color: #004225;">
                        <i class="bi bi-check2-circle me-1"></i> Save Required Documents Checklist
                    </button>
                </div>
            </form>
        </div>
        @else
        <div class="card border-0 shadow-sm p-5 rounded-4 text-center bg-white">
            <i class="bi bi-arrow-left-circle display-4 text-muted mb-2"></i>
            <h5 class="fw-bold text-dark">No Active Service Selected</h5>
            <p class="small text-secondary mb-0">Please select a service from the left dropdown panel to manage document requirements.</p>
        </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
    function toggleAllCheckboxes(state) {
        document.querySelectorAll('.doc-checkbox').forEach(cb => {
            cb.checked = state;
        });
    }
</script>
@endsection
