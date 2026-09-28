<?php $__env->startSection('title', 'Application ' . $application->reference_number . ' - Step ' . $step); ?>

<?php $__env->startSection('styles'); ?>
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
            <h1 class="h4 fw-bold text-dark mt-2 mb-0"><?php echo e($service->name); ?></h1>
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

    <!-- Stepper Navigation Header -->
    <div class="card border-0 shadow-sm p-4 rounded-4 bg-white mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="d-flex align-items-center gap-2">
                <div class="step-badge <?php echo e($step == 1 ? 'active' : ($step > 1 ? 'completed' : 'pending')); ?>">1</div>
                <span class="d-none d-sm-inline fw-semibold small <?php echo e($step == 1 ? 'text-dark' : 'text-muted'); ?>">Applicant Info</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <div class="step-badge <?php echo e($step == 2 ? 'active' : ($step > 2 ? 'completed' : 'pending')); ?>">2</div>
                <span class="d-none d-sm-inline fw-semibold small <?php echo e($step == 2 ? 'text-dark' : 'text-muted'); ?>">Service Details</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <div class="step-badge <?php echo e($step == 3 ? 'active' : ($step > 3 ? 'completed' : 'pending')); ?>">3</div>
                <span class="d-none d-sm-inline fw-semibold small <?php echo e($step == 3 ? 'text-dark' : 'text-muted'); ?>">Supporting Docs</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <div class="step-badge <?php echo e($step == 4 ? 'active' : 'pending'); ?>">4</div>
                <span class="d-none d-sm-inline fw-semibold small <?php echo e($step == 4 ? 'text-dark' : 'text-muted'); ?>">Review & Submit</span>
            </div>
        </div>

        <div class="step-progress-bar">
            <div class="step-progress-fill" style="width: <?php echo e($step * 25); ?>%;"></div>
        </div>
    </div>

    <!-- Form Container -->
    <div class="card border-0 shadow-sm p-4 p-sm-5 rounded-4 bg-white">
        <form id="applicationForm" action="<?php echo e(route('client.application.submit', $application->id)); ?>" method="POST">
            <?php echo csrf_field(); ?>

            <!-- STEP 1: Personal & Location Details -->
            <?php if($step == 1): ?>
                <h2 class="h5 fw-bold text-dark mb-3 border-bottom pb-2">Step 1: Personal & Location Details</h2>
                <div class="row g-3 mb-4">
                    <div class="col-sm-4">
                        <label class="form-label small fw-semibold">First Name</label>
                        <input type="text" name="form_data[first_name]" class="form-control autosave-field" value="<?php echo e(old('form_data.first_name', $application->form_data['first_name'] ?? Auth::user()->first_name)); ?>" required>
                    </div>
                    <div class="col-sm-4">
                        <label class="form-label small fw-semibold">Middle Name</label>
                        <input type="text" name="form_data[middle_name]" class="form-control autosave-field" value="<?php echo e(old('form_data.middle_name', $application->form_data['middle_name'] ?? Auth::user()->middle_name)); ?>">
                    </div>
                    <div class="col-sm-4">
                        <label class="form-label small fw-semibold">Last Name</label>
                        <input type="text" name="form_data[last_name]" class="form-control autosave-field" value="<?php echo e(old('form_data.last_name', $application->form_data['last_name'] ?? Auth::user()->last_name)); ?>" required>
                    </div>

                    <div class="col-sm-6">
                        <label class="form-label small fw-semibold">Email Address</label>
                        <input type="email" name="form_data[email]" class="form-control autosave-field" value="<?php echo e(old('form_data.email', $application->form_data['email'] ?? Auth::user()->email)); ?>" required>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label small fw-semibold">Phone Number</label>
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

            <!-- STEP 2: Service Specific Forms & Custom Fields -->
            <?php elseif($step == 2): ?>
                <h2 class="h5 fw-bold text-dark mb-3 border-bottom pb-2">Step 2: <?php echo e($service->name); ?> Application Inputs</h2>
                <?php if($service->fields && $service->fields->count() > 0): ?>
                    <div class="row g-3 mb-4">
                        <?php $__currentLoopData = $service->fields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="col-12 <?php echo e(in_array($field->field_type, ['textarea', 'address', 'instructions']) ? 'col-12' : 'col-md-6'); ?>">
                                <label class="form-label small fw-semibold">
                                    <?php echo e($field->field_label); ?>

                                    <?php if($field->is_required): ?> <span class="text-danger">*</span> <?php endif; ?>
                                </label>

                                <?php
                                    $fieldVal = $application->form_data[$field->field_name] ?? '';
                                ?>

                                <?php if($field->field_type === 'textarea'): ?>
                                    <textarea name="form_data[<?php echo e($field->field_name); ?>]" class="form-control autosave-field" rows="3" placeholder="<?php echo e($field->placeholder); ?>"><?php echo e($fieldVal); ?></textarea>
                                <?php elseif($field->field_type === 'dropdown'): ?>
                                    <select name="form_data[<?php echo e($field->field_name); ?>]" class="form-select autosave-field">
                                        <option value="">Select <?php echo e($field->field_label); ?></option>
                                        <?php if(is_array($field->options)): ?>
                                            <?php $__currentLoopData = $field->options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $opt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($opt); ?>" <?php echo e($fieldVal == $opt ? 'selected' : ''); ?>><?php echo e($opt); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php endif; ?>
                                    </select>
                                <?php elseif($field->field_type === 'date'): ?>
                                    <input type="date" name="form_data[<?php echo e($field->field_name); ?>]" class="form-control autosave-field" value="<?php echo e($fieldVal); ?>">
                                <?php else: ?>
                                    <input type="text" name="form_data[<?php echo e($field->field_name); ?>]" class="form-control autosave-field" value="<?php echo e($fieldVal); ?>" placeholder="<?php echo e($field->placeholder); ?>">
                                <?php endif; ?>

                                <?php if($field->help_text): ?>
                                    <span class="d-block text-muted" style="font-size: 11px;"><?php echo e($field->help_text); ?></span>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php else: ?>
                    <div class="p-4 bg-light rounded-3 mb-4 border">
                        <p class="text-secondary small mb-0">No additional custom input fields required for <?php echo e($service->name); ?>. Please proceed to upload supporting documents.</p>
                    </div>
                <?php endif; ?>

            <!-- STEP 3: Supporting Document Uploads -->
            <?php elseif($step == 3): ?>
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

                <h2 class="h5 fw-bold text-dark mb-1">Step 3: Upload Supporting Documents</h2>
                <p class="text-secondary small mb-3">Upload your required official documents for <strong><?php echo e($service->name); ?></strong>. Documents marked as compulsory must be attached before final submission.</p>

                <?php if(count($serviceDocChecklist) > 0): ?>
                    <!-- Admin Configured Required Document Checklist Box -->
                    <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
                        <div class="card-header bg-light py-3 border-bottom">
                            <h6 class="fw-bold text-dark mb-0 small">
                                <i class="bi bi-card-checklist me-1 text-success"></i> Required Supporting Documents Checklist for <?php echo e($service->name); ?>

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
                        <h6 class="fw-bold text-dark mb-1">Attach Document File</h6>
                        <p class="text-muted small mb-0">Allowed formats: PDF, JPG, PNG, DOCX (Max 10MB per file)</p>
                    </div>

                    <div class="row g-2 justify-content-center max-w-md mx-auto" style="max-width: 600px;">
                        <div class="col-sm-6">
                            <?php if(count($serviceDocChecklist) > 0): ?>
                                <select id="docNameSelect" class="form-select form-select-sm" onchange="toggleCustomDocInput(this)">
                                    <option value="">-- Select Required Document --</option>
                                    <?php $__currentLoopData = $serviceDocChecklist; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reqDoc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($reqDoc['name']); ?>"><?php echo e($reqDoc['name']); ?> <?php echo e($reqDoc['is_compulsory'] ? '(Compulsory)' : '(Optional)'); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <option value="__custom__">+ Other Custom Document Title</option>
                                </select>
                                <input type="text" id="docNameInput" class="form-control form-control-sm mt-2 d-none" placeholder="e.g. Additional Passport Page">
                            <?php else: ?>
                                <input type="text" id="docNameInput" class="form-control form-control-sm" placeholder="e.g. Passport Photograph">
                            <?php endif; ?>
                        </div>
                        <div class="col-sm-4">
                            <input type="file" id="docFileInput" class="form-control form-control-sm" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                        </div>
                        <div class="col-sm-2">
                            <button type="button" id="uploadDocBtn" class="btn btn-brand-primary btn-sm w-100">Upload</button>
                        </div>
                    </div>
                </div>

                <!-- List of Uploaded Documents -->
                <h6 class="fw-bold text-dark mb-2">Uploaded Supporting Documents (<span id="docCount"><?php echo e($application->requestDocuments->count()); ?></span>)</h6>
                <div id="uploadedDocsList" class="d-flex flex-column gap-2 mb-4">
                    <?php $__empty_1 = true; $__currentLoopData = $application->requestDocuments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="p-3 border rounded-3 bg-light d-flex align-items-center justify-content-between" id="docRow<?php echo e($doc->id); ?>">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-white p-2 rounded-circle border">
                                    <i class="bi bi-file-earmark-pdf-fill text-danger fs-5"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-0 text-dark small"><?php echo e($doc->document_name); ?></h6>
                                    <span class="text-muted" style="font-size: 11px;"><?php echo e($doc->file_name ?? 'Uploaded File'); ?> • Status: <strong class="text-success"><?php echo e($doc->status); ?></strong></span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <a href="<?php echo e(app_file_url($doc->file_path)); ?>" target="_blank" class="btn btn-outline-secondary btn-sm py-1 px-2.5" style="font-size: 12px;"><i class="bi bi-eye"></i> View</a>
                                <button type="button" class="btn btn-outline-danger btn-sm py-1 px-2 delete-doc-btn" data-id="<?php echo e($doc->id); ?>"><i class="bi bi-trash"></i></button>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div id="emptyDocsMsg" class="text-center p-4 bg-light rounded-3 border">
                            <p class="text-muted small mb-0">No supporting documents uploaded yet. Use the uploader above to attach your files.</p>
                        </div>
                    <?php endif; ?>
                </div>

            <!-- STEP 4: Review & Final Submission -->
            <?php elseif($step == 4): ?>
                <h2 class="h5 fw-bold text-dark mb-3 border-bottom pb-2">Step 4: Application Review & Final Submission</h2>

                <div class="bg-light p-4 rounded-3 border mb-4">
                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">Application Summary</h6>
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
                            <span class="text-muted d-block">Applicant Email & Phone:</span>
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
                        <div class="col-sm-6">
                            <span class="text-muted d-block">Total Fees:</span>
                            <strong class="text-success fs-6">₦<?php echo e(number_format($application->price)); ?></strong>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted d-block">Uploaded Documents:</span>
                            <strong class="text-dark"><?php echo e($application->requestDocuments->count()); ?> Files Attached</strong>
                        </div>
                    </div>
                </div>

                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" id="termsCheck" required checked>
                    <label class="form-check-label small text-secondary" for="termsCheck">
                        I declare that all information provided and supporting documents uploaded are authentic and correct. I authorize DOOTOR ENTERPRISES to process my application.
                    </label>
                </div>
            <?php endif; ?>

            <!-- Bottom Action Navigation -->
            <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                <?php if($step > 1): ?>
                    <a href="<?php echo e(route('client.application.step', ['serviceRequest' => $application->id, 'step' => $step - 1])); ?>" class="btn btn-outline-secondary px-4 py-2 rounded-3">
                        <i class="bi bi-arrow-left me-1"></i> Previous Step
                    </a>
                <?php else: ?>
                    <div></div>
                <?php endif; ?>

                <?php if($step < 4): ?>
                    <a href="<?php echo e(route('client.application.step', ['serviceRequest' => $application->id, 'step' => $step + 1])); ?>" class="btn btn-brand-primary px-4 py-2 rounded-3">
                        <span>Save & Next Step</span>
                        <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                <?php else: ?>
                    <button type="submit" class="btn btn-success px-5 py-2.5 rounded-3 fw-bold shadow-sm" style="background-color: #004225; border: none;">
                        <i class="bi bi-check-circle-fill me-1.5"></i> Submit Official Application
                    </button>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>
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

        // Background Auto-Save Functionality (Requirement #7)
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

            const payload = {
                _token: csrfToken,
                current_step: "<?php echo e($step); ?>",
                progress_percent: "<?php echo e($step * 25); ?>",
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

        // Auto-upload immediately when file is chosen in a checklist card input
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('checklist-file-input')) {
                const fileInput = e.target;
                const docName = fileInput.dataset.docName;
                const file = fileInput.files[0];
                if (file && docName) {
                    const btn = document.querySelector(`.select-req-doc-btn[data-input-id="${fileInput.id}"]`);
                    uploadDocumentFile(docName, file, btn);
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
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\DOOTOR ENTERPRISES\resources\views/client/application-step.blade.php ENDPATH**/ ?>