@extends('layouts.dashboard')

@section('title', 'Apply for ' . $service->name . ' - ' . ($settings->platform_name ?? 'DOOTOR ENTERPRISES'))

@section('content')
<div class="mb-4">
    <a href="{{ route('client.services') }}" class="btn btn-outline-secondary btn-sm rounded-pill mb-2"><i class="bi bi-arrow-left me-1"></i> Back to Services</a>
    <h1 class="h4 fw-bold text-dark mb-1">{{ $service->name }}</h1>
    <p class="text-secondary small mb-0">{{ $service->description }}</p>
</div>

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row g-4">
    <!-- Booking Methods Container -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
            <!-- Application Method Selector Navigation (Requirement 2) -->
            <div class="card-header bg-light p-3 border-bottom">
                <style>
                    #methodTabs .nav-link {
                        color: #475569;
                        background-color: #ffffff;
                        border: 1px solid #cbd5e1;
                        transition: all 0.2s ease;
                    }
                    #methodTabs .nav-link:hover {
                        color: #004225;
                        background-color: #f1f5f9;
                        border-color: #004225;
                    }
                    #methodTabs .nav-link.active {
                        color: #ffffff !important;
                        background-color: #004225 !important;
                        border-color: #004225 !important;
                        box-shadow: 0 4px 12px rgba(0, 66, 37, 0.18);
                    }
                </style>
                <ul class="nav nav-pills nav-fill gap-2" id="methodTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-bold py-2.5 rounded-3 d-flex align-items-center justify-content-center gap-2" id="online-tab" data-bs-toggle="tab" data-bs-target="#onlineTabContent" type="button" role="tab" aria-controls="onlineTabContent" aria-selected="true">
                            <i class="bi bi-laptop fs-5"></i>
                            <span>Method A: Complete Application Online</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold py-2.5 rounded-3 d-flex align-items-center justify-content-center gap-2" id="manual-tab" data-bs-toggle="tab" data-bs-target="#manualTabContent" type="button" role="tab" aria-controls="manualTabContent" aria-selected="false">
                            <i class="bi bi-file-earmark-arrow-down fs-5"></i>
                            <span>Method B: Download Form & Upload Scanned</span>
                        </button>
                    </li>
                </ul>
            </div>

            <div class="card-body p-4">
                <div class="tab-content" id="methodTabsContent">
                    
                    <!-- METHOD A: ONLINE APPLICATION -->
                    <div class="tab-pane fade show active" id="onlineTabContent" role="tabpanel" aria-labelledby="online-tab">
                        <div class="alert alert-success border-0 bg-success bg-opacity-10 text-success rounded-3 mb-4 small" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i> Complete your application questionnaire online. Attach required supporting documents and passport photo.
                        </div>

                        <form action="{{ route('client.book.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="service_id" value="{{ $service->id }}">

                            <!-- Passport Photograph Upload Section -->
                            <div class="mb-4 p-3 bg-light rounded-3 border">
                                <label class="form-label fw-bold text-dark mb-1">Passport Photograph Upload (White Background) *</label>
                                <p class="text-muted small mb-2">Upload a recent color passport-sized photo (JPG, PNG max 5MB).</p>
                                <input type="file" name="passport_photo" class="form-control rounded-3" accept="image/jpeg,image/png">
                            </div>

                            <!-- Custom Dynamic Fields -->
                            @if($service->fields && $service->fields->count() > 0)
                                <div class="mb-4">
                                    <h6 class="fw-bold text-dark mb-3 border-bottom pb-2">1. Service Application Questionnaire</h6>
                                    <div class="row g-3">
                                        @foreach($service->fields as $field)
                                            <div class="col-md-{{ in_array($field->field_type, ['textarea', 'address', 'instructions']) ? '12' : '6' }}">
                                                <label class="form-label small fw-semibold text-dark">
                                                    {{ $field->field_label }}
                                                    @if($field->is_required) <span class="text-danger">*</span> @endif
                                                </label>

                                                @if($field->field_type === 'textarea' || $field->field_type === 'address')
                                                    <textarea name="form_data[{{ $field->field_name }}]" class="form-control rounded-3" rows="3" placeholder="{{ $field->placeholder }}" {{ $field->is_required ? 'required' : '' }}></textarea>
                                                @elseif($field->field_type === 'dropdown' && is_array($field->options))
                                                    <select name="form_data[{{ $field->field_name }}]" class="form-select rounded-3" {{ $field->is_required ? 'required' : '' }}>
                                                        <option value="">-- Select Option --</option>
                                                        @foreach($field->options as $opt)
                                                            <option value="{{ $opt }}">{{ $opt }}</option>
                                                        @endforeach
                                                    </select>
                                                @elseif($field->field_type === 'country')
                                                    <select name="form_data[{{ $field->field_name }}]" class="form-select rounded-3" {{ $field->is_required ? 'required' : '' }}>
                                                        @foreach(\App\Constants\AfricanCountries::all() as $c)
                                                            <option value="{{ $c }}" {{ $c === 'Nigeria' ? 'selected' : '' }}>{{ $c }}</option>
                                                        @endforeach
                                                    </select>
                                                @elseif($field->field_type === 'date')
                                                    <input type="date" name="form_data[{{ $field->field_name }}]" class="form-control rounded-3" {{ $field->is_required ? 'required' : '' }}>
                                                @elseif($field->field_type === 'yes_no')
                                                    <select name="form_data[{{ $field->field_name }}]" class="form-select rounded-3" {{ $field->is_required ? 'required' : '' }}>
                                                        <option value="Yes">Yes</option>
                                                        <option value="No">No</option>
                                                    </select>
                                                @else
                                                    <input type="{{ $field->field_type === 'number' ? 'number' : ($field->field_type === 'email' ? 'email' : 'text') }}" name="form_data[{{ $field->field_name }}]" class="form-control rounded-3" placeholder="{{ $field->placeholder }}" {{ $field->is_required ? 'required' : '' }}>
                                                @endif

                                                @if($field->help_text)
                                                    <div class="form-text small" style="font-size: 11px;">{{ $field->help_text }}</div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Required Supporting Documents Checklist -->
                            <div class="mb-4">
                                <h6 class="fw-bold text-dark mb-3 border-bottom pb-2">2. Upload Required Supporting Documents</h6>
                                @if($service->required_documents && count($service->required_documents) > 0)
                                    <div class="row g-3">
                                        @foreach($service->required_documents as $docName)
                                            @php $slug = \Illuminate\Support\Str::slug($docName, '_'); @endphp
                                            <div class="col-md-6">
                                                <label class="form-label small fw-semibold text-dark">{{ $docName }} <span class="text-danger">*</span></label>
                                                <input type="file" name="documents[{{ $slug }}]" class="form-control rounded-3" required>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="col-12">
                                        <label class="form-label small fw-semibold text-dark">Supporting Identification Document <span class="text-danger">*</span></label>
                                        <input type="file" name="documents[supporting_document]" class="form-control rounded-3" required>
                                    </div>
                                @endif
                            </div>

                            <button type="submit" class="btn text-white w-100 py-3 rounded-3 fw-bold fs-6 shadow-sm" style="background-color: #004225; border: none;">
                                <i class="bi bi-send me-2"></i> Submit Online Application & Generate Reference
                            </button>
                        </form>
                    </div>

                    <!-- METHOD B: MANUAL FORM DOWNLOAD & UPLOAD -->
                    <div class="tab-pane fade" id="manualTabContent" role="tabpanel" aria-labelledby="manual-tab">
                        <div class="alert alert-warning border-0 bg-warning bg-opacity-10 text-dark rounded-3 mb-4 small" role="alert">
                            <i class="bi bi-file-earmark-pdf me-2"></i> Follow the 3-step offline manual application process below:
                        </div>

                        <div class="row g-3 mb-4">
                            <!-- Step 1: Download Form -->
                            <div class="col-md-12">
                                <div class="p-3 border rounded-3 bg-light d-flex align-items-center justify-content-between">
                                    <div>
                                        <h6 class="fw-bold text-dark mb-1"><span class="badge bg-success me-2" style="background-color: #004225 !important;">Step 1</span> Download Official Printable Form</h6>
                                        <p class="text-secondary small mb-0">Download the pre-formatted A4 application form for {{ $service->name }}.</p>
                                    </div>
                                    <a href="{{ route('client.service.download-form', $service) }}" target="_blank" class="btn btn-outline-success btn-sm font-semibold">
                                        <i class="bi bi-download me-1"></i> Download / Print Form
                                    </a>
                                </div>
                            </div>
                        </div>

                        <form action="{{ route('client.service.submit-manual', $service) }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <!-- Step 2: Upload Completed Form -->
                            <div class="mb-4 p-3 border rounded-3 bg-white">
                                <h6 class="fw-bold text-dark mb-2"><span class="badge bg-warning text-dark me-2">Step 2</span> Upload Completed & Signed Form *</h6>
                                <p class="text-secondary small mb-2">Upload your scanned/photographed completed and signed form (PDF, JPG, PNG max 20MB).</p>
                                <input type="file" name="manual_form" class="form-control rounded-3" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" required>
                            </div>

                            <!-- Step 3: Supporting Documents & Passport Photo -->
                            <div class="mb-4 p-3 border rounded-3 bg-white">
                                <h6 class="fw-bold text-dark mb-2"><span class="badge bg-info text-dark me-2">Step 3</span> Attach Passport Photo & Supporting Documents</h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold text-dark">Passport Photograph</label>
                                        <input type="file" name="passport_photo" class="form-control rounded-3" accept="image/jpeg,image/png">
                                    </div>

                                    @if($service->required_documents && count($service->required_documents) > 0)
                                        @foreach($service->required_documents as $docName)
                                            @php $slug = \Illuminate\Support\Str::slug($docName, '_'); @endphp
                                            <div class="col-md-6">
                                                <label class="form-label small fw-semibold text-dark">{{ $docName }}</label>
                                                <input type="file" name="documents[{{ $slug }}]" class="form-control rounded-3">
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>

                            <button type="submit" class="btn text-white w-100 py-3 rounded-3 fw-bold fs-6 shadow-sm" style="background-color: #004225; border: none;">
                                <i class="bi bi-cloud-upload me-2"></i> Submit Completed Manual Application Form
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Right Sidebar: Billing Summary -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
            <h3 class="h6 fw-bold text-dark mb-3">Billing & Processing Summary</h3>
            
            <div class="d-flex justify-content-between mb-2 small text-secondary">
                <span>Base Service Fee:</span>
                <span class="fw-semibold text-dark">${{ number_format($service->price, 2) }}</span>
            </div>
            
            @if($service->service_fee > 0)
            <div class="d-flex justify-content-between mb-2 small text-secondary">
                <span>Official Agency Fee:</span>
                <span class="fw-semibold text-dark">${{ number_format($service->service_fee, 2) }}</span>
            </div>
            @endif

            @if($service->processing_fee > 0)
            <div class="d-flex justify-content-between mb-2 small text-secondary">
                <span>Processing & Handling Fee:</span>
                <span class="fw-semibold text-dark">${{ number_format($service->processing_fee, 2) }}</span>
            </div>
            @endif

            @if($service->processing_days)
            <div class="d-flex justify-content-between mb-2 small text-secondary">
                <span>Est. Processing Period:</span>
                <span class="fw-semibold text-success">{{ $service->processing_days }} Business Days</span>
            </div>
            @endif
            
            <hr class="my-3 border-light">
            
            @php
                $total = $service->price + $service->service_fee + $service->processing_fee;
            @endphp

            @php
                $activeGw = strtolower($settings->payment_gateway ?? config('services.payment.gateway') ?: env('PAYMENT_GATEWAY', 'paystack'));
                $gwLabel = $activeGw === 'credo' ? 'Credo' : 'Paystack';
                $currencyCode = $settings->default_currency ?? config('services.payment.currency', 'NGN');
            @endphp
            <div class="d-flex justify-content-between align-items-center p-3 rounded-3 mb-3" style="background-color: #f8fafc; border: 1px solid #e2e8f0;">
                <span class="fw-medium text-dark">Total Amount Due:</span>
                <span class="fw-bold fs-4" style="color: #004225;">{{ $currencyCode }} {{ number_format($total, 2) }}</span>
            </div>

            <div class="p-3 bg-light rounded-3 small">
                <span class="fw-semibold text-dark d-block mb-1"><i class="bi bi-shield-check text-success"></i> {{ $gwLabel }} Secured Checkout</span>
                <span class="text-secondary small">Your payment is encrypted and safely processed via {{ $gwLabel }} Gateway.</span>
            </div>
        </div>
    </div>
</div>
@endsection
