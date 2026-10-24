<?php $__env->startSection('title', $service->name . ' Pre-Enrollment Form - Application ' . $application->reference_number . ' - Step ' . $step); ?>

<?php $__env->startSection('styles'); ?>
<?php echo \Illuminate\View\Factory::parentPlaceholder('styles'); ?>
<style>
    .step-progress-bar {
        height: 6px;
        background: #e2e8f0;
        border-radius: 4px;
        overflow: hidden;
    }
    .step-progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #004225 0%, #00703c 100%);
        transition: width 0.4s ease;
    }
    .step-badge {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 14px;
    }
    .step-badge.active {
        background-color: #004225;
        color: #ffffff;
        box-shadow: 0 0 0 4px rgba(0, 66, 37, 0.15);
    }
    .step-badge.completed {
        background-color: #10b981;
        color: #ffffff;
    }
    .step-badge.pending {
        background-color: #f1f5f9;
        color: #64748b;
        border: 1px solid #cbd5e1;
    }
    .autosave-indicator {
        font-size: 12px;
        font-weight: 600;
        padding: 4px 12px;
        border-radius: 20px;
        transition: all 0.3s ease;
    }
    .autosave-saving {
        background: #fffbebfb;
        color: #b45309;
        border: 1px solid #fde68a;
    }
    .autosave-saved {
        background: #f0fdf4;
        color: #15803d;
        border: 1px solid #bbf7d0;
    }
    .autosave-error {
        background: #fef2f2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }
    .doc-upload-card {
        border: 2px dashed #cbd5e1;
        border-radius: 12px;
        background: #f8fafc;
        transition: all 0.2s ease;
    }
    .doc-upload-card:hover {
        border-color: #004225;
        background: #ffffff;
    }

    /* Mobile view optimizations for step form navigation, document cards, and modal actions */
    .step-nav-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        white-space: nowrap;
        font-weight: 600;
        transition: all 0.2s ease;
    }
    @media (max-width: 575.98px) {
        .step-bottom-nav {
            display: flex !important;
            flex-direction: row !important;
            align-items: center !important;
            gap: 10px !important;
            width: 100% !important;
        }
        .step-bottom-nav > * {
            flex: 1 1 0px !important;
            width: 100% !important;
            min-width: 0 !important;
            padding-left: 8px !important;
            padding-right: 8px !important;
            padding-top: 10px !important;
            padding-bottom: 10px !important;
            font-size: 13px !important;
            text-align: center !important;
        }
        .step-bottom-nav .btn-prev {
            flex: 0 0 auto !important;
            width: auto !important;
            padding-left: 14px !important;
            padding-right: 14px !important;
        }
        .step-bottom-nav .btn-next-action {
            flex: 1 1 auto !important;
        }
        .doc-item-row {
            padding: 10px 12px !important;
        }
        .doc-item-actions {
            flex-shrink: 0 !important;
        }
        .doc-item-actions .btn {
            font-size: 11.5px !important;
            padding: 4px 8px !important;
        }
        .modal-footer-responsive {
            display: flex !important;
            flex-direction: column-reverse !important;
            gap: 8px !important;
            width: 100% !important;
            padding: 0 !important;
        }
        .modal-footer-responsive > * {
            width: 100% !important;
            margin: 0 !important;
            text-align: center !important;
            padding: 9px 16px !important;
            font-size: 13.5px !important;
        }
    }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-0" style="max-width: 900px; margin: 0 auto;">
    <!-- Top Bar with Reference & Auto-Save Indicator -->
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-4 gap-2">
        <div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-dark font-monospace px-2.5 py-1 fs-6"><?php echo e($application->reference_number); ?></span>
                <span class="badge bg-warning text-dark px-2 py-1 font-monospace small"><?php echo e($application->status); ?></span>
            </div>
            <h1 class="h4 fw-bold text-dark mt-2 mb-0"><?php echo e($service->name); ?> Pre-Enrollment Form</h1>
            <?php if($application->sub_service_name): ?>
                <p class="text-secondary small mb-0"><i class="bi bi-diagram-2 me-1"></i> Sub-service: <strong><?php echo e($application->sub_service_name); ?></strong></p>
            <?php endif; ?>
        </div>

        <div class="d-flex align-items-center gap-3">
            <div id="autosaveStatus" class="autosave-indicator autosave-saved d-flex align-items-center gap-1.5">
                <i class="bi bi-check-circle-fill"></i>
                <span id="autosaveText">Draft Saved</span>
            </div>
            <a href="<?php echo e(route('client.dashboard')); ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="bi bi-speedometer2 me-1"></i> Dashboard
            </a>
        </div>
    </div>

    <!-- Stepper Navigation Header (3 Steps) -->
    <div class="card border-0 shadow-sm p-3.5 p-md-4 rounded-4 bg-white mb-4" style="border: 1px solid #eef2f6 !important; border-radius: 20px !important; padding: 22px 20px !important;">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="d-flex align-items-center gap-2">
                <div class="step-badge <?php echo e($step == 1 ? 'active' : ($step > 1 ? 'completed' : 'pending')); ?>">1</div>
                <span class="d-none d-sm-inline fw-semibold small <?php echo e($step == 1 ? 'text-dark' : 'text-muted'); ?>">Biodata</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <div class="step-badge <?php echo e($step == 2 ? 'active' : ($step > 2 ? 'completed' : 'pending')); ?>">2</div>
                <span class="d-none d-sm-inline fw-semibold small <?php echo e($step == 2 ? 'text-dark' : 'text-muted'); ?>">Service Inputs &amp; Documents</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <div class="step-badge <?php echo e($step == 3 ? 'active' : 'pending'); ?>">3</div>
                <span class="d-none d-sm-inline fw-semibold small <?php echo e($step == 3 ? 'text-dark' : 'text-muted'); ?>">Review &amp; Confirm</span>
            </div>
        </div>

        <div class="step-progress-bar">
            <div class="step-progress-fill" style="width: <?php echo e(round(($step / 3) * 100)); ?>%;"></div>
        </div>
    </div>

    <!-- Form Container -->
    <div class="card border-0 shadow-sm p-3.5 p-sm-5 rounded-4 bg-white" style="border: 1px solid #eef2f6 !important; border-radius: 20px !important; padding: 24px 20px !important;">
        <form id="applicationForm" action="<?php echo e(route('client.application.submit', $application->id)); ?>" method="POST" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>

            <!-- STEP 1: Biodata -->
            <?php if($step == 1): ?>
                <h2 class="h5 fw-bold text-dark mb-1">Step 1: Biodata Pre-Enrollment Form</h2>
                <p class="text-secondary small mb-3 border-bottom pb-2">Please enter your personal contact and location information.</p>

                <div class="row g-3 mb-4">
                    <div class="col-sm-4">
                        <label class="form-label small fw-semibold">First Name <span class="text-danger">*</span></label>
                        <input type="text" name="form_data[first_name]" class="form-control autosave-field" value="<?php echo e(old('form_data.first_name', $application->form_data['first_name'] ?? Auth::user()->first_name)); ?>" required>
                    </div>
                    <div class="col-sm-4">
                        <label class="form-label small fw-semibold">Middle Name</label>
                        <input type="text" name="form_data[middle_name]" class="form-control autosave-field" value="<?php echo e(old('form_data.middle_name', $application->form_data['middle_name'] ?? Auth::user()->middle_name)); ?>">
                    </div>
                    <div class="col-sm-4">
                        <label class="form-label small fw-semibold">Last Name <span class="text-danger">*</span></label>
                        <input type="text" name="form_data[last_name]" class="form-control autosave-field" value="<?php echo e(old('form_data.last_name', $application->form_data['last_name'] ?? Auth::user()->last_name)); ?>" required>
                    </div>

                    <div class="col-sm-6">
                        <label class="form-label small fw-semibold">Email Address <span class="text-danger">*</span></label>
                        <input type="email" name="form_data[email]" class="form-control autosave-field" value="<?php echo e(old('form_data.email', $application->form_data['email'] ?? Auth::user()->email)); ?>" required>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label small fw-semibold">Phone Number <span class="text-danger">*</span></label>
                        <input type="text" name="form_data[phone]" class="form-control autosave-field" value="<?php echo e(old('form_data.phone', $application->form_data['phone'] ?? Auth::user()->phone)); ?>" required>
                    </div>

                    <div class="col-sm-6">
                        <label class="form-label small fw-semibold">Country Applying From</label>
                        <select name="country_applying_from" id="country_applying_from" class="form-select african-country-select autosave-field" data-selected="<?php echo e($application->country_applying_from ?? Auth::user()->country_applying_from ?? 'Canada'); ?>" data-division-target="state" data-label-target="state_label" required>
                            <?php $__currentLoopData = $allCountries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($c['name']); ?>" <?php echo e(($application->country_applying_from ?? Auth::user()->country_applying_from ?? 'Canada') == $c['name'] ? 'selected' : ''); ?>><?php echo e($c['name']); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <div class="col-sm-6">
                        <label class="form-label small fw-semibold">Country for Requested Service</label>
                        <select name="country_service_requested" id="country_service_requested" class="form-select autosave-field" required>
                            <?php $__currentLoopData = $allCountries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($c['name']); ?>" <?php echo e(($application->country_service_requested ?? Auth::user()->country_service_requested ?? 'Nigeria') == $c['name'] ? 'selected' : ''); ?>><?php echo e($c['name']); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <div class="col-sm-6">
                        <label id="state_label" class="form-label small fw-semibold african-division-label">State / Province / Region</label>
                        <select name="form_data[state]" id="state" class="form-select african-division-select autosave-field" data-selected="<?php echo e($application->form_data['state'] ?? Auth::user()->state); ?>">
                            <option value="">Loading Divisions...</option>
                        </select>
                    </div>

                    <div class="col-sm-6">
                        <label class="form-label small fw-semibold">City / Town</label>
                        <input type="text" name="form_data[city]" class="form-control autosave-field" value="<?php echo e(old('form_data.city', $application->form_data['city'] ?? Auth::user()->city)); ?>" placeholder="e.g. Toronto, London, Lagos">
                    </div>
                </div>

            <!-- STEP 2: Service Form Inputs & Documents (Combined) -->
            <?php elseif($step == 2): ?>
                <?php
                    $rawRequiredDocs = $service->required_documents;
                    if (empty($rawRequiredDocs) && isset($application->service) && !empty($application->service->required_documents)) {
                        $rawRequiredDocs = $application->service->required_documents;
                    }
                    if (empty($rawRequiredDocs) && isset($service->parent) && !empty($service->parent->required_documents)) {
                        $rawRequiredDocs = $service->parent->required_documents;
                    }
                    if (is_string($rawRequiredDocs)) {
                        $rawRequiredDocs = json_decode($rawRequiredDocs, true) ?: [];
                    }

                    $serviceDocChecklist = [];
                    if (is_array($rawRequiredDocs)) {
                        foreach ($rawRequiredDocs as $item) {
                            if (is_array($item)) {
                                $dName = $item['name'] ?? ($item['document_name'] ?? '');
                                $isComp = isset($item['is_compulsory']) ? (bool)$item['is_compulsory'] : true;
                            } else {
                                $dName = (string)$item;
                                $isComp = true;
                            }
                            if (!empty(trim($dName))) {
                                $serviceDocChecklist[] = [
                                    'name' => trim($dName),
                                    'is_compulsory' => $isComp,
                                ];
                            }
                        }
                    }

                    $uploadedDocNames = $application->requestDocuments->pluck('document_name')->map(function($n) {
                        return strtolower(trim($n));
                    })->toArray();

                    $isDocUploaded = function($docName) use ($uploadedDocNames) {
                        $clean = strtolower(trim($docName));
                        foreach ($uploadedDocNames as $uploaded) {
                            if ($clean === $uploaded || str_contains($uploaded, $clean) || str_contains($clean, $uploaded)) {
                                return true;
                            }
                        }
                        return false;
                    };
                ?>

                <h2 class="h5 fw-bold text-dark mb-1">Step 2: <?php echo e($service->name); ?> Pre-Enrollment Form - Service Inputs &amp; Documents</h2>
                <p class="text-secondary small mb-4 border-bottom pb-2">Complete the customized service questionnaire and upload all supporting documents required for <?php echo e($service->name); ?>.</p>

                <!-- Part A: Form Builder Custom Fields -->
                <div class="mb-4">
                    <h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                        <i class="bi bi-ui-checks-grid text-success"></i>
                        <span>Service Questionnaire &amp; Details</span>
                    </h6>
                    <?php if($service->fields && $service->fields->count() > 0): ?>
                        <div class="row g-3 p-3 bg-light rounded-3 border mb-4">
                            <?php $__currentLoopData = $service->fields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php echo $__env->make('partials.dynamic-form-field', ['field' => $field, 'application' => $application], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php else: ?>
                        <div class="p-3 bg-light rounded-3 border mb-4">
                            <p class="text-secondary small mb-0"><i class="bi bi-info-circle me-1"></i> No custom questionnaire fields required for <?php echo e($service->name); ?>. Please proceed to attach your supporting documents below.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Part B: Supporting Document Uploads -->
                <div class="mb-4">
                    <h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                        <i class="bi bi-file-earmark-arrow-up text-success"></i>
                        <span>Required Supporting Documents</span>
                    </h6>

                    <?php if(count($serviceDocChecklist) > 0): ?>
                        <!-- Document Checklist Box -->
                        <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
                            <div class="card-header bg-light py-2.5 border-bottom">
                                <h6 class="fw-bold text-dark mb-0 small">
                                    <i class="bi bi-card-checklist me-1 text-success"></i> Document Checklist for <?php echo e($service->name); ?>

                                </h6>
                            </div>
                            <div class="card-body p-3">
                                <div class="row g-3">
                                    <?php $__currentLoopData = $serviceDocChecklist; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reqDoc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $uploaded = $isDocUploaded($reqDoc['name']);
                                        ?>
                                        <div class="col-md-6 d-flex">
                                            <div class="p-3 border rounded-3 d-flex align-items-center justify-content-between w-100" style="background-color: <?php echo e($uploaded ? 'rgba(16, 185, 129, 0.05)' : ($reqDoc['is_compulsory'] ? 'rgba(239, 68, 68, 0.03)' : '#ffffff')); ?>; border-color: <?php echo e($uploaded ? '#10b981' : ($reqDoc['is_compulsory'] ? '#fca5a5' : '#e2e8f0')); ?> !important;">
                                                <div class="me-2 flex-grow-1">
                                                    <div class="d-flex flex-wrap align-items-center gap-1.5 mb-1">
                                                        <span class="fw-bold text-dark small me-1"><?php echo e($reqDoc['name']); ?></span>
                                                        <?php if($reqDoc['is_compulsory']): ?>
                                                            <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill extra-small flex-shrink-0" style="font-size: 10px;">Compulsory</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill extra-small flex-shrink-0" style="font-size: 10px;">Optional</span>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="small">
                                                        <?php if($uploaded): ?>
                                                            <span class="text-success fw-semibold extra-small" style="font-size: 11px;"><i class="bi bi-check-circle-fill me-1"></i> File Attached</span>
                                                        <?php else: ?>
                                                            <span class="text-muted extra-small" style="font-size: 11px;"><i class="bi bi-hourglass me-1"></i> Awaiting Upload</span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-center flex-shrink-0 ms-2">
                                                    <input type="file" class="d-none checklist-file-input" id="checkFileInput_<?php echo e($loop->index); ?>" data-doc-name="<?php echo e($reqDoc['name']); ?>" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                                                    <button type="button" class="btn <?php echo e($uploaded ? 'btn-outline-success' : 'btn-success'); ?> btn-sm rounded-pill py-1.5 px-3 select-req-doc-btn text-nowrap" data-input-id="checkFileInput_<?php echo e($loop->index); ?>" data-doc-name="<?php echo e($reqDoc['name']); ?>" style="font-size: 11.5px; white-space: nowrap; background-color: <?php echo e($uploaded ? 'transparent' : '#004225'); ?>; color: <?php echo e($uploaded ? '#004225' : '#ffffff'); ?>; border-color: #004225;">
                                                        <i class="bi bi-upload me-1"></i> <?php echo e($uploaded ? 'Re-upload' : 'Upload'); ?>

                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Document Upload Area -->
                    <div class="doc-upload-card p-4 mb-4">
                        <div class="text-center mb-3">
                            <i class="bi bi-cloud-arrow-up-fill fs-1 text-success mb-2" style="color: #004225 !important;"></i>
                            <h6 class="fw-bold text-dark mb-1">Attach Supporting Document File</h6>
                            <p class="text-muted small mb-0">Allowed formats: PDF, JPG, PNG, DOCX (Max 10MB per file)</p>
                        </div>

                        <div class="row g-2 justify-content-center max-w-md mx-auto" style="max-width: 600px;">
                            <div class="col-12 col-sm-6">
                                <?php if(count($serviceDocChecklist) > 0): ?>
                                    <select id="docNameSelect" class="form-select form-select-sm py-2" onchange="toggleCustomDocInput(this)">
                                        <option value="">-- Select Required Document --</option>
                                        <?php $__currentLoopData = $serviceDocChecklist; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reqDoc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($reqDoc['name']); ?>"><?php echo e($reqDoc['name']); ?> <?php echo e($reqDoc['is_compulsory'] ? '(Compulsory)' : '(Optional)'); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <option value="__custom__">+ Other Custom Document Title</option>
                                    </select>
                                    <input type="text" id="docNameInput" class="form-control form-control-sm mt-2 py-2 d-none" placeholder="e.g. Additional Passport Page">
                                <?php else: ?>
                                    <input type="text" id="docNameInput" class="form-control form-control-sm py-2" placeholder="e.g. Passport Photograph">
                                <?php endif; ?>
                            </div>
                            <div class="col-12 col-sm-4">
                                <input type="file" id="docFileInput" class="form-control form-control-sm py-1.5" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                            </div>
                            <div class="col-12 col-sm-2">
                                <button type="button" id="uploadDocBtn" class="btn btn-brand-primary btn-sm w-100 py-2 fw-semibold d-flex align-items-center justify-content-center gap-1 shadow-sm">
                                    <i class="bi bi-upload"></i>
                                    <span>Upload</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- List of Uploaded Documents -->
                    <h6 class="fw-bold text-dark mb-2">Uploaded Supporting Documents (<span id="docCount"><?php echo e($application->requestDocuments->count()); ?></span>)</h6>
                    <div id="uploadedDocsList" class="d-flex flex-column gap-2 mb-4">
                        <?php $__empty_1 = true; $__currentLoopData = $application->requestDocuments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <div class="p-2.5 p-sm-3 border rounded-3 bg-light d-flex align-items-center justify-content-between doc-item-row" id="docRow<?php echo e($doc->id); ?>">
                                <div class="d-flex align-items-center gap-2.5 min-w-0 me-2" style="min-width: 0;">
                                    <div class="bg-white p-2 rounded-circle border flex-shrink-0 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                        <i class="bi bi-file-earmark-pdf-fill text-danger fs-5"></i>
                                    </div>
                                    <div class="overflow-hidden" style="min-width: 0;">
                                        <h6 class="fw-bold mb-0 text-dark small text-truncate" style="font-size: 13px;"><?php echo e($doc->document_name); ?></h6>
                                        <span class="text-muted d-block text-truncate" style="font-size: 11px;"><?php echo e($doc->file_name ?? 'Uploaded File'); ?> • Status: <strong class="text-success"><?php echo e($doc->status); ?></strong></span>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-1.5 flex-shrink-0 doc-item-actions">
                                    <a href="<?php echo e(app_file_url($doc->file_path)); ?>" target="_blank" class="btn btn-outline-secondary btn-sm py-1 px-2.5 d-inline-flex align-items-center gap-1" style="font-size: 12px;"><i class="bi bi-eye"></i> <span>View</span></a>
                                    <button type="button" class="btn btn-outline-danger btn-sm py-1 px-2 delete-doc-btn d-inline-flex align-items-center justify-content-center" data-id="<?php echo e($doc->id); ?>"><i class="bi bi-trash"></i></button>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div id="emptyDocsMsg" class="text-center p-4 bg-light rounded-3 border">
                                <p class="text-muted small mb-0">No supporting documents uploaded yet. Use the uploader above to attach your files.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

            <!-- STEP 3: Review & Confirm (No Price or Total Fee Displayed) -->
            <?php elseif($step == 3): ?>
                <h2 class="h5 fw-bold text-dark mb-1">Step 3: <?php echo e($service->name); ?> Pre-Enrollment Form - Review &amp; Confirm</h2>
                <p class="text-secondary small mb-4 border-bottom pb-2">Review all information entered and documents attached before proceeding to payment.</p>

                <!-- Biodata & Service Summary (Price / Total Fee completely removed) -->
                <div class="bg-light p-4 rounded-3 border mb-4">
                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="bi bi-card-text me-1 text-success"></i> Application Summary</h6>
                    <div class="row g-3 small">
                        <div class="col-sm-6">
                            <span class="text-muted d-block">Reference Number:</span>
                            <strong class="text-dark font-monospace fs-6"><?php echo e($application->reference_number); ?></strong>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted d-block">Service Requested:</span>
                            <strong class="text-dark"><?php echo e($application->service_name); ?> <?php echo e($application->sub_service_name ? '(' . $application->sub_service_name . ')' : ''); ?></strong>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted d-block">Applicant Full Name:</span>
                            <strong class="text-dark"><?php echo e($application->form_data['first_name'] ?? ''); ?> <?php echo e($application->form_data['middle_name'] ?? ''); ?> <?php echo e($application->form_data['last_name'] ?? ''); ?></strong>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted d-block">Applicant Email &amp; Phone:</span>
                            <strong class="text-dark"><?php echo e($application->form_data['email'] ?? ''); ?> • <?php echo e($application->form_data['phone'] ?? ''); ?></strong>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted d-block">Applying From:</span>
                            <strong class="text-dark"><?php echo e($application->country_applying_from); ?> (<?php echo e($application->form_data['city'] ?? ''); ?>, <?php echo e($application->form_data['state'] ?? ''); ?>)</strong>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted d-block">Country for Service:</span>
                            <strong class="text-dark"><?php echo e($application->country_service_requested); ?></strong>
                        </div>
                        <div class="col-sm-12">
                            <span class="text-muted d-block">Attached Supporting Documents:</span>
                            <strong class="text-dark"><?php echo e($application->requestDocuments->count()); ?> Files Attached</strong>
                        </div>
                    </div>
                </div>

                <!-- Custom Questionnaire Responses Summary -->
                <?php if(is_array($application->form_data)): ?>
                    <?php
                        $customResponses = array_filter($application->form_data, function($val, $key) {
                            return !in_array($key, ['first_name', 'middle_name', 'last_name', 'email', 'phone', 'state', 'city', 'country_applying_from', 'country_service_requested']);
                        }, ARRAY_FILTER_USE_BOTH);
                    ?>
                    <?php if(count($customResponses) > 0): ?>
                        <div class="bg-light p-4 rounded-3 border mb-4">
                            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="bi bi-list-check me-1 text-success"></i> Custom Service Form Responses</h6>
                            <div class="row g-2 small">
                                <?php $__currentLoopData = $customResponses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fieldKey => $fieldVal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="col-sm-6">
                                        <span class="text-muted d-block text-capitalize"><?php echo e(str_replace('_', ' ', $fieldKey)); ?>:</span>
                                        <strong class="text-dark"><?php echo e(is_array($fieldVal) ? implode(', ', $fieldVal) : (string)$fieldVal); ?></strong>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

                <!-- Uploaded Documents Overview -->
                <div class="bg-light p-4 rounded-3 border mb-4">
                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="bi bi-folder-check me-1 text-success"></i> Attached Supporting Documents (<?php echo e($application->requestDocuments->count()); ?>)</h6>
                    <div class="d-flex flex-column gap-2">
                        <?php $__empty_1 = true; $__currentLoopData = $application->requestDocuments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <div class="d-flex align-items-center justify-content-between p-2 rounded bg-white border small">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-file-earmark-check-fill text-success fs-6"></i>
                                    <span class="fw-semibold text-dark"><?php echo e($doc->document_name); ?></span>
                                </div>
                                <span class="badge bg-success-subtle text-success border border-success-subtle">Ready</span>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <p class="text-muted small mb-0">No documents attached.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" id="termsCheck" required checked>
                    <label class="form-check-label small text-secondary" for="termsCheck">
                        I declare that all information provided and supporting documents uploaded are authentic and correct. I authorize DOOTOR ENTERPRISES to process my pre-enrollment.
                    </label>
                </div>
            <?php endif; ?>

            <!-- Bottom Action Navigation (Clean, Touch-Friendly Mobile Layout) -->
            <div class="d-flex justify-content-between align-items-center pt-3 border-top step-bottom-nav">
                <?php if($step > 1): ?>
                    <a href="<?php echo e(route('client.application.step', ['serviceRequest' => $application->id, 'step' => $step - 1])); ?>" class="btn btn-outline-secondary px-3.5 py-2.5 rounded-3 btn-prev d-inline-flex align-items-center justify-content-center gap-1.5 shadow-sm">
                        <i class="bi bi-arrow-left"></i>
                        <span>Previous Step</span>
                    </a>
                <?php else: ?>
                    <div></div>
                <?php endif; ?>

                <?php if($step < 3): ?>
                    <button type="button" id="btnOpenConfirmStepModal" class="btn btn-brand-primary px-4 py-2.5 rounded-3 btn-next-action d-inline-flex align-items-center justify-content-center gap-1.5 shadow-sm fw-bold">
                        <span>Save &amp; Next Step</span>
                        <i class="bi bi-arrow-right"></i>
                    </button>
                <?php else: ?>
                    <!-- Step 3: Continue to Payment Button -->
                    <button type="button" id="btnOpenConfirmPaymentModal" class="btn btn-success px-4 px-sm-5 py-2.5 rounded-3 fw-bold shadow-sm btn-next-action d-inline-flex align-items-center justify-content-center gap-2" style="background-color: #004225; border: none;">
                        <span>Continue to Payment</span>
                        <i class="bi bi-credit-card-2-front"></i>
                    </button>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Confirm Details Before Proceeding to Next Form Step (Step 1 & Step 2) -->
<?php if($step < 3): ?>
<div class="modal fade" id="confirmStepDetailsModal" tabindex="-1" aria-labelledby="confirmStepDetailsLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle bg-success bg-opacity-10 p-2 text-success">
                        <i class="bi bi-shield-check fs-5"></i>
                    </div>
                    <h5 class="modal-title fw-bold text-dark fs-6" id="confirmStepDetailsLabel">Confirm Form Details</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-3">
                <p class="text-secondary small mb-3">
                    Before proceeding to the next step, please confirm that you have reviewed the details provided in this <strong>Step <?php echo e($step); ?> Pre-Enrollment Form</strong>.
                </p>
                <div class="p-3 rounded-3 bg-light border mb-3">
                    <div class="form-check mb-0">
                        <input class="form-check-input" type="checkbox" id="agreeStepCheckbox" checked>
                        <label class="form-check-label small fw-semibold text-dark" for="agreeStepCheckbox">
                            I agree and confirm that all details and documents provided in this step are accurate and true.
                        </label>
                    </div>
                </div>
                <div class="alert alert-info py-2 px-3 mb-0 small border-0 d-flex align-items-center gap-2">
                    <i class="bi bi-info-circle-fill fs-6 flex-shrink-0"></i>
                    <span>Your entries will be automatically saved before advancing to the next stage.</span>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0 modal-footer-responsive">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-3.5 btn-sm" data-bs-dismiss="modal">
                    Review Details
                </button>
                <button type="button" id="confirmNextStepBtn" class="btn btn-success rounded-pill px-4 btn-sm fw-bold" style="background-color: #004225; border: none;">
                    <span>I Agree &amp; Proceed</span>
                    <i class="bi bi-arrow-right ms-1"></i>
                </button>
            </div>
        </div>
    </div>
</div>
<?php else: ?>
<!-- Modal: Confirm Details & Continue to Payment (Step 3) -->
<div class="modal fade" id="confirmPaymentModal" tabindex="-1" aria-labelledby="confirmPaymentLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle bg-success bg-opacity-10 p-2 text-success">
                        <i class="bi bi-credit-card-2-front-fill fs-5"></i>
                    </div>
                    <h5 class="modal-title fw-bold text-dark fs-6" id="confirmPaymentLabel">Confirm Application &amp; Continue to Payment</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-3">
                <p class="text-secondary small mb-3">
                    You are confirming your pre-enrollment application for <strong><?php echo e($service->name); ?></strong> (Reference: <span class="font-monospace fw-bold text-dark"><?php echo e($application->reference_number); ?></span>).
                </p>
                <div class="p-3 rounded-3 bg-light border mb-3">
                    <div class="form-check mb-0">
                        <input class="form-check-input" type="checkbox" id="agreePaymentCheckbox" checked>
                        <label class="form-check-label small fw-semibold text-dark" for="agreePaymentCheckbox">
                            I declare that all entered biodata, custom inputs, and supporting documents are accurate and ready for payment.
                        </label>
                    </div>
                </div>
                <div class="alert alert-success py-2 px-3 mb-0 small border-0 d-flex align-items-center gap-2 bg-success bg-opacity-10 text-success">
                    <i class="bi bi-shield-check fs-6 flex-shrink-0"></i>
                    <span>You will now be redirected directly to the secured payment gateway to complete your transaction.</span>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0 modal-footer-responsive">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-3.5 btn-sm" data-bs-dismiss="modal">
                    Cancel / Review
                </button>
                <button type="button" id="confirmPaymentSubmitBtn" class="btn btn-success rounded-pill px-4 btn-sm fw-bold" style="background-color: #004225; border: none;">
                    <i class="bi bi-lock-fill me-1"></i> Continue to Payment Gateway
                </button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script src="<?php echo e(asset('js/location-loader.js')); ?>"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const appId = "<?php echo e($application->id); ?>";
        const autosaveUrl = "<?php echo e(route('client.application.autosave', $application->id)); ?>";
        const uploadUrl = "<?php echo e(route('client.application.upload-document', $application->id)); ?>";
        const deleteBaseUrl = "<?php echo e(url('/client/application/document')); ?>";
        const csrfToken = "<?php echo e(csrf_token()); ?>";

        const autosaveStatus = document.getElementById('autosaveStatus');
        const autosaveText = document.getElementById('autosaveText');

        let debounceTimer = null;

        function setStatus(type, text) {
            autosaveStatus.className = 'autosave-indicator autosave-' + type;
            autosaveText.innerText = text;
        }

        // Background Auto-Save Functionality (3 Steps Progress: 33%, 66%, 100%)
        function triggerAutoSave() {
            setStatus('saving', 'Saving...');

            const formDataObj = {};
            const fields = document.querySelectorAll('.autosave-field');
            fields.forEach(el => {
                if (el.name) {
                    const cleanName = el.name.replace('form_data[', '').replace(']', '');
                    formDataObj[cleanName] = el.value;
                }
            });

            const currentStepNum = parseInt("<?php echo e($step); ?>", 10) || 1;
            const progressPct = Math.round((currentStepNum / 3) * 100);

            const payload = {
                _token: csrfToken,
                current_step: currentStepNum,
                progress_percent: progressPct,
                country_applying_from: document.getElementById('country_applying_from') ? document.getElementById('country_applying_from').value : "<?php echo e($application->country_applying_from); ?>",
                country_service_requested: document.getElementById('country_service_requested') ? document.getElementById('country_service_requested').value : "<?php echo e($application->country_service_requested); ?>",
                form_data: formDataObj
            };

            fetch(autosaveUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(payload)
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    setStatus('saved', 'Saved at ' + data.last_saved);
                } else {
                    setStatus('error', 'Unable to save');
                }
            })
            .catch(() => {
                setStatus('error', 'Offline / Retrying...');
            });
        }

        // Attach debounced listeners to input fields
        document.querySelectorAll('.autosave-field').forEach(el => {
            el.addEventListener('input', function () {
                setStatus('saving', 'Saving...');
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(triggerAutoSave, 1200);
            });
            el.addEventListener('change', function () {
                triggerAutoSave();
            });
        });

        // Unified Helper function for Document Upload via AJAX
        function uploadDocumentFile(docName, file, triggerBtn = null) {
            if (!docName || !file) {
                alert('Please select a document title and file.');
                return;
            }

            const fd = new FormData();
            fd.append('_token', csrfToken);
            fd.append('document_name', docName);
            fd.append('document_file', file);

            let origHtml = '';
            if (triggerBtn) {
                origHtml = triggerBtn.innerHTML;
                triggerBtn.disabled = true;
                triggerBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-1" role="status"></span> Uploading...`;
            }

            fetch(uploadUrl, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken },
                body: fd
            })
            .then(res => res.json())
            .then(data => {
                if (triggerBtn) {
                    triggerBtn.disabled = false;
                    triggerBtn.innerHTML = origHtml;
                }
                if (data.status === 'success') {
                    window.location.reload();
                } else {
                    alert(data.message || 'Upload failed');
                }
            })
            .catch(err => {
                if (triggerBtn) {
                    triggerBtn.disabled = false;
                    triggerBtn.innerHTML = origHtml;
                }
                alert('Network error during upload');
            });
        }

        // Checklist item button click -> trigger matching hidden file input
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.select-req-doc-btn');
            if (btn) {
                const inputId = btn.dataset.inputId;
                if (inputId) {
                    const inputEl = document.getElementById(inputId);
                    if (inputEl) {
                        inputEl.click();
                    }
                }
            }
        });

        // Auto-upload immediately when file is chosen
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('checklist-file-input')) {
                const fileInput = e.target;
                const docName = fileInput.dataset.docName;
                const file = fileInput.files[0];
                if (file && docName) {
                    const btn = document.querySelector(`.select-req-doc-btn[data-input-id="${fileInput.id}"]`);
                    uploadDocumentFile(docName, file, btn);
                }
            } else if (e.target.classList.contains('dynamic-file-input')) {
                const fileInput = e.target;
                const docName = fileInput.dataset.docName;
                const file = fileInput.files[0];
                if (file && docName) {
                    uploadDocumentFile(docName, file);
                }
            } else if (e.target.classList.contains('dynamic-passport-input')) {
                const fileInput = e.target;
                const file = fileInput.files[0];
                if (file) {
                    uploadDocumentFile('Passport Photograph Upload', file);
                }
            }
        });

        // Bottom manual Document Upload Button Handler
        const uploadBtn = document.getElementById('uploadDocBtn');
        if (uploadBtn) {
            uploadBtn.addEventListener('click', function () {
                const nameSelect = document.getElementById('docNameSelect');
                const nameInput = document.getElementById('docNameInput');
                const fileInput = document.getElementById('docFileInput');

                let docName = '';
                if (nameSelect && nameSelect.value && nameSelect.value !== '__custom__') {
                    docName = nameSelect.value;
                } else if (nameInput && nameInput.value.trim()) {
                    docName = nameInput.value.trim();
                }

                if (!docName || !fileInput.files[0]) {
                    alert('Please select or enter a document title and select a file.');
                    return;
                }

                uploadDocumentFile(docName, fileInput.files[0], uploadBtn);
            });
        }

        window.toggleCustomDocInput = function(selectEl) {
            const nameInput = document.getElementById('docNameInput');
            if (nameInput) {
                if (selectEl.value === '__custom__') {
                    nameInput.classList.remove('d-none');
                    nameInput.focus();
                } else {
                    nameInput.classList.add('d-none');
                }
            }
        };

        // Delete document handler
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('.delete-doc-btn');
            if (btn) {
                const docId = btn.dataset.id;
                if (confirm('Are you sure you want to delete this uploaded document?')) {
                    btn.disabled = true;
                    const origHtml = btn.innerHTML;
                    btn.innerHTML = `<span class="spinner-border spinner-border-sm" role="status"></span>`;

                    fetch(deleteBaseUrl + '/' + docId, {
                        method: 'DELETE',
                        headers: { 
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'success') {
                            window.location.reload();
                        } else {
                            btn.disabled = false;
                            btn.innerHTML = origHtml;
                            alert(data.message || 'Error deleting document');
                        }
                    })
                    .catch(err => {
                        btn.disabled = false;
                        btn.innerHTML = origHtml;
                        alert('Network error while deleting document');
                    });
                }
            }
        });

        // Step Advance & Confirmation Modal Handlers (Step 1 and Step 2)
        const btnOpenStepModal = document.getElementById('btnOpenConfirmStepModal');
        if (btnOpenStepModal) {
            btnOpenStepModal.addEventListener('click', function(e) {
                e.preventDefault();
                const form = document.getElementById('applicationForm');
                if (form && !form.checkValidity()) {
                    form.reportValidity();
                    return;
                }
                const modal = new bootstrap.Modal(document.getElementById('confirmStepDetailsModal'));
                modal.show();
            });
        }

        const confirmNextStepBtn = document.getElementById('confirmNextStepBtn');
        if (confirmNextStepBtn) {
            confirmNextStepBtn.addEventListener('click', function() {
                const agreeCheck = document.getElementById('agreeStepCheckbox');
                if (agreeCheck && !agreeCheck.checked) {
                    alert('Please confirm that you agree with the provided details before proceeding.');
                    return;
                }
                confirmNextStepBtn.disabled = true;
                confirmNextStepBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-1" role="status"></span> Saving & Proceeding...`;

                triggerAutoSave();

                setTimeout(() => {
                    window.location.href = "<?php echo e(route('client.application.step', ['serviceRequest' => $application->id, 'step' => $step + 1])); ?>";
                }, 400);
            });
        }

        // Step 3: Continue to Payment Modal Handlers
        const btnOpenPaymentModal = document.getElementById('btnOpenConfirmPaymentModal');
        if (btnOpenPaymentModal) {
            btnOpenPaymentModal.addEventListener('click', function(e) {
                e.preventDefault();
                const termsCheck = document.getElementById('termsCheck');
                if (termsCheck && !termsCheck.checked) {
                    alert('Please check the declaration checkbox before continuing to payment.');
                    return;
                }
                const modal = new bootstrap.Modal(document.getElementById('confirmPaymentModal'));
                modal.show();
            });
        }

        const confirmPaymentSubmitBtn = document.getElementById('confirmPaymentSubmitBtn');
        if (confirmPaymentSubmitBtn) {
            confirmPaymentSubmitBtn.addEventListener('click', function() {
                const agreeCheck = document.getElementById('agreePaymentCheckbox');
                if (agreeCheck && !agreeCheck.checked) {
                    alert('Please confirm that you agree with all details before continuing to payment.');
                    return;
                }
                confirmPaymentSubmitBtn.disabled = true;
                confirmPaymentSubmitBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-1" role="status"></span> Connecting to Payment Gateway...`;
                document.getElementById('applicationForm').submit();
            });
        }
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\DOOTOR ENTERPRISES\resources\views\client\application-step.blade.php ENDPATH**/ ?>