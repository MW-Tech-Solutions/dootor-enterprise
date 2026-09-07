@extends('layouts.dashboard')

@section('title', 'Email Template Management')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 fw-bold text-dark mb-1">Notification Email Templates</h1>
        <p class="text-secondary small mb-0">Manage customer and administrative email notification templates and placeholders.</p>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row g-4">
    @foreach($templates as $tmpl)
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="fw-bold text-dark mb-0">{{ $tmpl->title }}</h6>
                        <span class="badge bg-secondary font-monospace mt-1" style="font-size: 10px;">{{ $tmpl->code }}</span>
                    </div>
                    <span class="badge {{ $tmpl->is_active ? 'bg-success' : 'bg-danger' }}">
                        {{ $tmpl->is_active ? 'Active' : 'Disabled' }}
                    </span>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.email-templates.update', $tmpl) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Email Subject Line</label>
                            <input type="text" name="subject" class="form-control form-control-sm" value="{{ $tmpl->subject }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">HTML Body Template</label>
                            <textarea name="body_html" class="form-control font-monospace" rows="8" style="font-size: 12px;" required>{{ $tmpl->body_html }}</textarea>
                        </div>

                        <div class="bg-light p-2 rounded mb-3" style="font-size: 11px; color: #475569;">
                            <strong>Supported Safe Placeholders:</strong><br>
                            <code>{{ $tmpl->variables_description ?? '{full_name}, {service_name}, {reference_number}, {status}, {current_stage}, {company_name}, {dashboard_link}' }}</code>
                        </div>

                        <div class="d-flex align-items-center justify-content-between border-top pt-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="act_t_{{ $tmpl->id }}" {{ $tmpl->is_active ? 'checked' : '' }}>
                                <label class="form-check-label small fw-semibold" for="act_t_{{ $tmpl->id }}">Enable Template</label>
                            </div>
                            <button type="submit" class="btn btn-success btn-sm px-3" style="background-color: #004225; border: none;">
                                <i class="bi bi-check-circle me-1"></i> Save Template
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection
