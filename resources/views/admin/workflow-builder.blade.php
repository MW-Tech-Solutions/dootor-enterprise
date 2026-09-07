@extends('layouts.dashboard')

@section('title', 'Service Workflow Builder - ' . $service->name)

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ route('admin.services') }}" class="text-decoration-none text-success">Services</a></li>
                <li class="breadcrumb-item active" aria-current="page">Workflow Builder</li>
            </ol>
        </nav>
        <h1 class="h3 fw-bold text-dark mb-0">Workflow Lifecycle Builder: {{ $service->name }}</h1>
        <p class="text-secondary small mb-0">Define custom progress tracking stages, assigned roles, estimated timelines, and notification triggers.</p>
    </div>
    <a href="{{ route('admin.services') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back to Services
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<form action="{{ route('admin.service.workflow.save', $service) }}" method="POST">
    @csrf

    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
            <h5 class="card-title fw-bold mb-0 text-dark fs-6">Workflow Stages ({{ $stages->count() }})</h5>
            <button type="button" class="btn btn-success btn-sm" id="addStageBtn" style="background-color: #004225; border: none;">
                <i class="bi bi-plus-lg me-1"></i> Add Workflow Stage
            </button>
        </div>
        <div class="card-body p-0">
            <div id="stagesContainer">
                @forelse($stages as $index => $stage)
                    <div class="stage-row p-3 border-bottom bg-light bg-opacity-50">
                        <input type="hidden" name="stages[{{ $index }}][id]" value="{{ $stage->id }}">

                        <div class="row g-3">
                            <div class="col-md-1 text-center d-flex align-items-center justify-content-center">
                                <div class="badge rounded-circle bg-success fs-6 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background-color: #004225 !important;">
                                    {{ $index + 1 }}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Stage Name *</label>
                                <input type="text" name="stages[{{ $index }}][stage_name]" class="form-control form-control-sm bg-white" value="{{ $stage->stage_name }}" required placeholder="e.g. Documents Under Review">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Status Key *</label>
                                <select name="stages[{{ $index }}][status_key]" class="form-select form-select-sm bg-white">
                                    <option value="Submitted" {{ $stage->status_key == 'Submitted' ? 'selected' : '' }}>Submitted</option>
                                    <option value="Payment Confirmed" {{ $stage->status_key == 'Payment Confirmed' ? 'selected' : '' }}>Payment Confirmed</option>
                                    <option value="In Review" {{ $stage->status_key == 'In Review' ? 'selected' : '' }}>In Review</option>
                                    <option value="Verification" {{ $stage->status_key == 'Verification' ? 'selected' : '' }}>Verification</option>
                                    <option value="Processing" {{ $stage->status_key == 'Processing' ? 'selected' : '' }}>Processing</option>
                                    <option value="External Agency" {{ $stage->status_key == 'External Agency' ? 'selected' : '' }}>External Agency</option>
                                    <option value="Ready" {{ $stage->status_key == 'Ready' ? 'selected' : '' }}>Ready for Collection</option>
                                    <option value="Completed" {{ $stage->status_key == 'Completed' ? 'selected' : '' }}>Completed</option>
                                </select>
                            </div>
                            <div class="col-md-4 d-flex align-items-end gap-3">
                                <div class="form-check me-2">
                                    <input class="form-check-input" type="checkbox" name="stages[{{ $index }}][is_user_visible]" value="1" id="vis_{{ $index }}" {{ $stage->is_user_visible ? 'checked' : '' }}>
                                    <label class="form-check-label small fw-semibold" for="vis_{{ $index }}">User Visible</label>
                                </div>
                                <div class="form-check me-2">
                                    <input class="form-check-input" type="checkbox" name="stages[{{ $index }}][notification_enabled]" value="1" id="ntf_{{ $index }}" {{ $stage->notification_enabled ? 'checked' : '' }}>
                                    <label class="form-check-label small fw-semibold" for="ntf_{{ $index }}">Notify Email</label>
                                </div>
                                <button type="button" class="btn btn-outline-danger btn-sm remove-stage-btn ms-auto">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>

                        <div class="row g-3 mt-1">
                            <div class="col-md-5 ms-md-5">
                                <label class="form-label small text-secondary">Stage Description</label>
                                <input type="text" name="stages[{{ $index }}][description]" class="form-control form-control-sm bg-white" value="{{ $stage->description }}" placeholder="Details of activity at this stage">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small text-secondary">Assigned Role</label>
                                <select name="stages[{{ $index }}][assigned_role_id]" class="form-select form-select-sm bg-white">
                                    <option value="">-- Any Staff --</option>
                                    @foreach($roles as $r)
                                        <option value="{{ $r->id }}" {{ $stage->assigned_role_id == $r->id ? 'selected' : '' }}>{{ $r->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small text-secondary">Est. Days</label>
                                <input type="number" name="stages[{{ $index }}][estimated_days]" class="form-control form-control-sm bg-white" value="{{ $stage->estimated_days }}" placeholder="Days">
                            </div>
                            <div class="col-md-1">
                                <input type="hidden" name="stages[{{ $index }}][sort_order]" value="{{ $stage->sort_order ?? ($index + 1) }}">
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-5 text-center text-muted" id="noStagesNotice">
                        <i class="bi bi-diagram-3 fs-1 d-block mb-2 text-secondary"></i>
                        <p class="mb-2">No workflow stages configured yet.</p>
                    </div>
                @endforelse
            </div>
        </div>
        <div class="card-footer bg-white py-3 text-end">
            <button type="submit" class="btn btn-success px-4" style="background-color: #004225; border: none;">
                <i class="bi bi-check-circle me-1"></i> Save Workflow Lifecycle
            </button>
        </div>
    </div>
</form>

<template id="stageTemplate">
    <div class="stage-row p-3 border-bottom bg-light bg-opacity-50">
        <input type="hidden" name="stages[__INDEX__][id]" value="">
        <div class="row g-3">
            <div class="col-md-1 text-center d-flex align-items-center justify-content-center">
                <div class="badge rounded-circle bg-success fs-6 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background-color: #004225 !important;">
                    +
                </div>
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-bold">Stage Name *</label>
                <input type="text" name="stages[__INDEX__][stage_name]" class="form-control form-control-sm bg-white" required placeholder="e.g. Verification in Progress">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold">Status Key *</label>
                <select name="stages[__INDEX__][status_key]" class="form-select form-select-sm bg-white">
                    <option value="Submitted">Submitted</option>
                    <option value="Payment Confirmed">Payment Confirmed</option>
                    <option value="In Review">In Review</option>
                    <option value="Verification" selected>Verification</option>
                    <option value="Processing">Processing</option>
                    <option value="External Agency">External Agency</option>
                    <option value="Ready">Ready for Collection</option>
                    <option value="Completed">Completed</option>
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end gap-3">
                <div class="form-check me-2">
                    <input class="form-check-input" type="checkbox" name="stages[__INDEX__][is_user_visible]" value="1" id="vis___INDEX__" checked>
                    <label class="form-check-label small fw-semibold" for="vis___INDEX__">User Visible</label>
                </div>
                <div class="form-check me-2">
                    <input class="form-check-input" type="checkbox" name="stages[__INDEX__][notification_enabled]" value="1" id="ntf___INDEX__" checked>
                    <label class="form-check-label small fw-semibold" for="ntf___INDEX__">Notify Email</label>
                </div>
                <button type="button" class="btn btn-outline-danger btn-sm remove-stage-btn ms-auto">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>

        <div class="row g-3 mt-1">
            <div class="col-md-5 ms-md-5">
                <label class="form-label small text-secondary">Stage Description</label>
                <input type="text" name="stages[__INDEX__][description]" class="form-control form-control-sm bg-white" placeholder="Details of activity at this stage">
            </div>
            <div class="col-md-3">
                <label class="form-label small text-secondary">Assigned Role</label>
                <select name="stages[__INDEX__][assigned_role_id]" class="form-select form-select-sm bg-white">
                    <option value="">-- Any Staff --</option>
                    @foreach($roles as $r)
                        <option value="{{ $r->id }}">{{ $r->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small text-secondary">Est. Days</label>
                <input type="number" name="stages[__INDEX__][estimated_days]" class="form-control form-control-sm bg-white" placeholder="Days">
            </div>
            <div class="col-md-1">
                <input type="hidden" name="stages[__INDEX__][sort_order]" value="__SORT__">
            </div>
        </div>
    </div>
</template>

<script>
document.addEventListener("DOMContentLoaded", function() {
    let stageCount = {{ $stages->count() }};
    const addBtn = document.getElementById("addStageBtn");
    const container = document.getElementById("stagesContainer");
    const templateHtml = document.getElementById("stageTemplate").innerHTML;
    const notice = document.getElementById("noStagesNotice");

    addBtn.addEventListener("click", function() {
        if (notice) notice.style.display = "none";
        stageCount++;
        let html = templateHtml.replace(/__INDEX__/g, stageCount).replace(/__SORT__/g, stageCount);
        container.insertAdjacentHTML("beforeend", html);
    });

    container.addEventListener("click", function(e) {
        if (e.target.closest(".remove-stage-btn")) {
            const row = e.target.closest(".stage-row");
            row.remove();
        }
    });
});
</script>
@endsection
