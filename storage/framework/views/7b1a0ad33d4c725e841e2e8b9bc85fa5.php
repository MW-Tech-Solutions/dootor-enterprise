<?php $__env->startSection('title', 'Process Application - ' . $serviceRequest->reference_number); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <a href="<?php echo e(route('admin.work-queue')); ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i> Back to Work Queue
            </a>
            <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle font-monospace px-3 py-1 rounded-pill" style="color: #004225 !important; background-color: #e6f4ea !important;">
                <?php echo e($serviceRequest->reference_number); ?>

            </span>
        </div>
        <h4 class="fw-bold text-dark mb-0">Processing: <?php echo e($serviceRequest->service_name); ?></h4>
        <?php if($serviceRequest->subService): ?>
            <span class="badge bg-light text-dark border mt-1">Sub-Service: <?php echo e($serviceRequest->subService->name); ?></span>
        <?php endif; ?>
    </div>
    <div class="d-flex align-items-center gap-2">
        <span class="badge bg-dark px-3 py-2 fs-6 rounded-pill">
            Status: <?php echo e($serviceRequest->status); ?>

        </span>
    </div>
</div>

<?php if(session('success')): ?>
<div class="alert alert-success alert-dismissible fade show mb-4 rounded-3 shadow-sm" role="alert">
    <i class="bi bi-check-circle-fill me-2"></i> <?php echo e(session('success')); ?>

    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="row g-4">
    <!-- Left Column: Applicant Info, Form Data & Uploaded Documents -->
    <div class="col-lg-8">
        
        <!-- 1. Applicant & Location Overview -->
        <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white overflow-hidden">
            <div class="card-header bg-light py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
                <h5 class="fw-bold text-dark mb-0 fs-6"><i class="bi bi-person-vcard text-success me-2" style="color: #004225 !important;"></i> Applicant &amp; Routing Details</h5>
                <span class="badge bg-white border text-dark small font-monospace">Client ID: #<?php echo e($serviceRequest->client_id); ?></span>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="text-secondary small fw-medium d-block">Full Name</label>
                        <span class="fw-bold text-dark fs-6"><?php echo e($serviceRequest->client_name); ?></span>
                    </div>
                    <div class="col-md-6">
                        <label class="text-secondary small fw-medium d-block">Email Address</label>
                        <span class="fw-semibold text-dark"><?php echo e($serviceRequest->client_email); ?></span>
                    </div>
                    <div class="col-md-6">
                        <label class="text-secondary small fw-medium d-block">Phone Number</label>
                        <span class="fw-semibold text-dark"><?php echo e($serviceRequest->client_phone ?? $serviceRequest->client?->phone ?? 'N/A'); ?></span>
                    </div>
                    <div class="col-md-6">
                        <label class="text-secondary small fw-medium d-block">Application Method</label>
                        <span class="badge <?php echo e($serviceRequest->application_method === 'manual' ? 'bg-warning text-dark' : 'bg-info text-dark'); ?>">
                            <?php echo e(ucfirst($serviceRequest->application_method ?? 'online')); ?>

                        </span>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3 border">
                            <label class="text-muted small fw-bold d-block mb-1"><i class="bi bi-geo-alt-fill text-danger me-1"></i> Country Applying From</label>
                            <span class="fw-bold text-dark fs-6"><?php echo e($serviceRequest->country_applying_from ?? $serviceRequest->client?->country_applying_from ?? 'Nigeria'); ?></span>
                            <div class="small text-muted mt-1">State/Region: <?php echo e($serviceRequest->client?->state ?? 'N/A'); ?></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3 border">
                            <label class="text-muted small fw-bold d-block mb-1"><i class="bi bi-globe-americas text-primary me-1"></i> Country for Requested Service</label>
                            <span class="fw-bold text-dark fs-6"><?php echo e($serviceRequest->country_service_requested ?? $serviceRequest->client?->country_service_requested ?? 'Nigeria'); ?></span>
                            <div class="small text-muted mt-1">City: <?php echo e($serviceRequest->client?->city ?? 'N/A'); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Dynamic Form Responses / Application Fields -->
        <?php if($serviceRequest->form_data || $serviceRequest->manual_form_path): ?>
            <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white overflow-hidden">
                <div class="card-header bg-light py-3 px-4 border-bottom">
                    <h5 class="fw-bold text-dark mb-0 fs-6"><i class="bi bi-file-earmark-text text-primary me-2"></i> Application Form Responses</h5>
                </div>
                <div class="card-body p-4">
                    <?php if($serviceRequest->manual_form_path): ?>
                        <div class="p-3 bg-warning bg-opacity-10 border border-warning rounded-3 mb-3 d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="fw-bold text-dark mb-1"><i class="bi bi-file-earmark-pdf-fill text-danger me-1"></i> Scanned Application Form Attached</h6>
                                <p class="text-secondary small mb-0">Client submitted a handwritten/printed application scan.</p>
                            </div>
                            <a href="<?php echo e(asset($serviceRequest->manual_form_path)); ?>" target="_blank" class="btn btn-dark btn-sm rounded-pill px-3 fw-semibold">
                                <i class="bi bi-eye me-1"></i> View Scanned Form
                            </a>
                        </div>
                    <?php endif; ?>

                    <?php if(!empty($serviceRequest->form_data) && is_array($serviceRequest->form_data)): ?>
                        <div class="row g-3">
                            <?php $__currentLoopData = $serviceRequest->form_data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fieldKey => $fieldValue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="col-md-6">
                                    <div class="p-2.5 bg-light rounded-3 border">
                                        <label class="text-muted small fw-semibold d-block mb-1"><?php echo e(ucwords(str_replace(['_', '-'], ' ', $fieldKey))); ?></label>
                                        <span class="fw-bold text-dark">
                                            <?php if(is_array($fieldValue)): ?>
                                                <?php echo e(implode(', ', $fieldValue)); ?>

                                            <?php else: ?>
                                                <?php echo e($fieldValue ?: 'N/A'); ?>

                                            <?php endif; ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- 3. Uploaded Documents Verification Panel -->
        <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white overflow-hidden">
            <div class="card-header bg-light py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
                <h5 class="fw-bold text-dark mb-0 fs-6"><i class="bi bi-folder-check text-success me-2" style="color: #004225 !important;"></i> Uploaded Documents &amp; Verification</h5>
                <span class="badge bg-white border text-dark font-monospace"><?php echo e($serviceRequest->requestDocuments->count()); ?> Documents Uploaded</span>
            </div>
            <div class="card-body p-4">
                <?php if($serviceRequest->passport_photo_path): ?>
                    <div class="p-3 mb-4 rounded-3 border bg-light d-flex align-items-center gap-3">
                        <img src="<?php echo e(asset($serviceRequest->passport_photo_path)); ?>" alt="Passport Photo" class="rounded-3 border shadow-sm" style="width: 70px; height: 80px; object-fit: cover;">
                        <div>
                            <h6 class="fw-bold text-dark mb-1">Passport Photograph Uploaded</h6>
                            <p class="text-muted small mb-1">Official passport photo uploaded by client during application step.</p>
                            <a href="<?php echo e(asset($serviceRequest->passport_photo_path)); ?>" target="_blank" class="btn btn-sm btn-outline-dark rounded-pill px-3">
                                <i class="bi bi-zoom-in me-1"></i> Inspect Full Resolution
                            </a>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if($serviceRequest->requestDocuments->count() > 0): ?>
                    <div class="d-flex flex-column gap-3">
                        <?php $__currentLoopData = $serviceRequest->requestDocuments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="p-3 border rounded-3 bg-white shadow-2xs">
                                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-2">
                                    <div>
                                        <h6 class="fw-bold text-dark mb-0">
                                            <i class="bi bi-file-earmark-pdf text-danger me-1"></i> <?php echo e($doc->document_name); ?>

                                        </h6>
                                        <small class="text-muted">Uploaded <?php echo e($doc->created_at->format('M d, Y H:i')); ?></small>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <?php
                                            $docBadge = match($doc->status) {
                                                'Accepted' => 'bg-success text-white',
                                                'Requires Correction' => 'bg-warning text-dark',
                                                'Rejected' => 'bg-danger text-white',
                                                default => 'bg-secondary text-white'
                                            };
                                        ?>
                                        <span class="badge <?php echo e($docBadge); ?> rounded-pill px-3 py-1 font-monospace" style="font-size: 11px;">
                                            <?php echo e($doc->status); ?>

                                        </span>
                                        <a href="<?php echo e(asset($doc->file_path)); ?>" target="_blank" class="btn btn-outline-dark btn-sm rounded-pill px-3" title="View Document">
                                            <i class="bi bi-eye me-1"></i> View
                                        </a>
                                        <a href="<?php echo e(asset($doc->file_path)); ?>" download class="btn btn-outline-secondary btn-sm rounded-pill px-3" title="Download Document">
                                            <i class="bi bi-download me-1"></i> Download
                                        </a>
                                    </div>
                                </div>

                                <!-- Inline Document Verification Form -->
                                <form action="<?php echo e(route('admin.work-queue.document.verify', $doc->id)); ?>" method="POST" class="row g-2 pt-2 border-top mt-2">
                                    <?php echo csrf_field(); ?>
                                    <div class="col-md-5">
                                        <select name="status" class="form-select form-select-sm rounded-3">
                                            <option value="Pending" <?php echo e($doc->status === 'Pending' ? 'selected' : ''); ?>>Pending Verification</option>
                                            <option value="Received" <?php echo e($doc->status === 'Received' ? 'selected' : ''); ?>>Received</option>
                                            <option value="Accepted" <?php echo e($doc->status === 'Accepted' ? 'selected' : ''); ?>>Accepted ✅</option>
                                            <option value="Requires Correction" <?php echo e($doc->status === 'Requires Correction' ? 'selected' : ''); ?>>Requires Correction ⚠️</option>
                                            <option value="Rejected" <?php echo e($doc->status === 'Rejected' ? 'selected' : ''); ?>>Rejected ❌</option>
                                        </select>
                                    </div>
                                    <div class="col-md-5">
                                        <input type="text" name="admin_notes" class="form-control form-control-sm rounded-3" placeholder="Officer notes or correction reason..." value="<?php echo e($doc->admin_notes); ?>">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" class="btn btn-dark btn-sm w-100 rounded-3 font-monospace">Update</button>
                                    </div>
                                </form>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php else: ?>
                    <div class="p-4 text-center text-muted bg-light rounded-3">
                        <i class="bi bi-folder-x fs-2 d-block mb-1"></i>
                        No specific document files uploaded for this application yet.
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- 4. Client Safe Progress Timeline -->
        <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
            <div class="card-header bg-light py-3 px-4 border-bottom">
                <h5 class="fw-bold text-dark mb-0 fs-6"><i class="bi bi-diagram-3 text-success me-2" style="color: #004225 !important;"></i> Application Stage History &amp; Audit Logs</h5>
            </div>
            <div class="card-body p-4">
                <div class="timeline position-relative ps-3">
                    <?php $__empty_1 = true; $__currentLoopData = $serviceRequest->stageHistories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $history): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="timeline-item pb-3 border-start border-2 ps-3 position-relative">
                            <div class="position-absolute top-0 start-0 translate-middle rounded-circle bg-success" style="width: 10px; height: 10px; background-color: #004225 !important;"></div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-dark small"><?php echo e($history->stage_name); ?></span>
                                <span class="text-muted font-monospace" style="font-size: 11px;"><?php echo e($history->created_at->format('M d, Y H:i A')); ?></span>
                            </div>
                            <span class="badge bg-light text-dark border small mt-1"><?php echo e($history->status); ?></span>
                            <?php if($history->notes): ?>
                                <p class="text-secondary small mb-0 mt-1 bg-light p-2 rounded-2"><?php echo e($history->notes); ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="text-muted small">No stage transition history recorded yet.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>

    <!-- Right Column: Status Update, Stage Control & Assignment -->
    <div class="col-lg-4">
        
        <!-- Processing Action Card -->
        <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white overflow-hidden sticky-top" style="top: 90px;">
            <div class="card-header text-white py-3 px-4 border-bottom" style="background-color: #004225;">
                <h5 class="fw-bold mb-0 fs-6"><i class="bi bi-sliders me-2"></i> Update Application Status</h5>
            </div>
            <div class="card-body p-4">
                <form action="<?php echo e(route('admin.work-queue.update', $serviceRequest->id)); ?>" method="POST">
                    <?php echo csrf_field(); ?>

                    <!-- Current Responsible Staff Display -->
                    <div class="mb-3 p-3 bg-light rounded-3 border">
                        <label class="text-muted small fw-bold d-block mb-1">Assigned Staff Member</label>
                        <?php if($serviceRequest->assignedStaff): ?>
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-person-badge text-success fs-5" style="color: #004225 !important;"></i>
                                <div>
                                    <span class="fw-bold text-dark d-block small"><?php echo e($serviceRequest->assignedStaff->name); ?></span>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle font-monospace" style="font-size: 10px; color: #004225 !important; background-color: #e6f4ea !important;">
                                        File #: <?php echo e($serviceRequest->assignedStaff->staff_file_number ?? 'DE/STF/2026/000001'); ?>

                                    </span>
                                </div>
                            </div>
                        <?php else: ?>
                            <span class="text-warning fw-bold small"><i class="bi bi-exclamation-circle me-1"></i> Unassigned</span>
                        <?php endif; ?>
                    </div>

                    <!-- Reassign Option -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Reassign Staff (Optional)</label>
                        <select name="assigned_staff_id" class="form-select form-select-sm rounded-3">
                            <option value="">-- Keep Current Assigned Staff --</option>
                            <?php $__currentLoopData = $staffMembers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($s->id); ?>" <?php echo e($serviceRequest->assigned_staff_id == $s->id ? 'selected' : ''); ?>>
                                    <?php echo e($s->name); ?> (<?php echo e($s->staff_file_number ?? 'DE/STF/2026/000001'); ?>)
                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <!-- Main Status Select -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Processing Status</label>
                        <select name="status" class="form-select rounded-3 fw-bold">
                            <option value="Assigned" <?php echo e($serviceRequest->status === 'Assigned' ? 'selected' : ''); ?>>Assigned</option>
                            <option value="Documents Under Review" <?php echo e($serviceRequest->status === 'Documents Under Review' ? 'selected' : ''); ?>>Documents Under Review</option>
                            <option value="Verification in Progress" <?php echo e($serviceRequest->status === 'Verification in Progress' ? 'selected' : ''); ?>>Verification in Progress</option>
                            <option value="Processing" <?php echo e($serviceRequest->status === 'Processing' ? 'selected' : ''); ?>>Processing</option>
                            <option value="Awaiting External Agency" <?php echo e($serviceRequest->status === 'Awaiting External Agency' ? 'selected' : ''); ?>>Awaiting External Agency</option>
                            <option value="Action Required" <?php echo e($serviceRequest->status === 'Action Required' ? 'selected' : ''); ?>>Action Required (Client)</option>
                            <option value="Returned" <?php echo e($serviceRequest->status === 'Returned' ? 'selected' : ''); ?>>Returned for Info</option>
                            <option value="Escalated" <?php echo e($serviceRequest->status === 'Escalated' ? 'selected' : ''); ?>>Escalated</option>
                            <option value="Ready for Collection" <?php echo e($serviceRequest->status === 'Ready for Collection' ? 'selected' : ''); ?>>Ready for Collection</option>
                            <option value="Completed" <?php echo e($serviceRequest->status === 'Completed' ? 'selected' : ''); ?>>Completed ✅</option>
                            <option value="Rejected" <?php echo e($serviceRequest->status === 'Rejected' ? 'selected' : ''); ?>>Rejected ❌</option>
                            <option value="Cancelled" <?php echo e($serviceRequest->status === 'Cancelled' ? 'selected' : ''); ?>>Cancelled</option>
                        </select>
                    </div>

                    <!-- Workflow Stage Select -->
                    <?php if(isset($workflowStages) && $workflowStages->count() > 0): ?>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-dark">Workflow Stage</label>
                            <select name="stage_id" class="form-select form-select-sm rounded-3">
                                <option value="">-- Select Specific Stage --</option>
                                <?php $__currentLoopData = $workflowStages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($stg->id); ?>" <?php echo e($serviceRequest->current_stage_id == $stg->id ? 'selected' : ''); ?>>
                                        Step <?php echo e($stg->sort_order); ?>: <?php echo e($stg->stage_name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    <?php endif; ?>

                    <!-- Officer Remarks / Notes -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Officer Remarks &amp; Notes</label>
                        <textarea name="admin_notes" class="form-control rounded-3" rows="4" placeholder="Enter details of action taken, missing documents requested, or processing updates..."></textarea>
                    </div>

                    <!-- Send Client Email Notification Checkbox -->
                    <div class="form-check mb-4 bg-light p-2.5 rounded-3 border">
                        <input class="form-check-input ms-0 me-2" type="checkbox" name="is_user_visible" value="1" id="visCheck" checked>
                        <label class="form-check-label small fw-semibold text-dark" for="visCheck">
                            Send status update &amp; notes notification email to applicant
                        </label>
                    </div>

                    <button type="submit" class="btn text-white w-100 rounded-pill py-2.5 fw-bold shadow-sm" style="background-color: #004225;">
                        <i class="bi bi-save me-1"></i> Commit Status Update
                    </button>
                </form>
            </div>
        </div>

        <!-- Assignment History Card -->
        <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
            <div class="card-header bg-light py-3 px-4 border-bottom">
                <h5 class="fw-bold text-dark mb-0 fs-6"><i class="bi bi-clock-history me-2 text-secondary"></i> Assignment Audit Trail</h5>
            </div>
            <div class="card-body p-4">
                <?php if($serviceRequest->assignmentHistories->count() > 0): ?>
                    <div class="d-flex flex-column gap-3">
                        <?php $__currentLoopData = $serviceRequest->assignmentHistories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assign): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="p-2.5 border-start border-3 border-success bg-light rounded-2" style="border-color: #004225 !important;">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-bold text-dark small">Assigned to: <?php echo e($assign->newStaff?->name ?? 'Staff'); ?></span>
                                    <span class="text-muted font-monospace" style="font-size: 10px;"><?php echo e($assign->created_at->format('M d, H:i')); ?></span>
                                </div>
                                <div class="small text-muted" style="font-size: 11px;">
                                    By: <?php echo e($assign->assignedBy?->name ?? 'System'); ?> | Staff File #: <?php echo e($assign->newStaff?->staff_file_number ?? 'N/A'); ?>

                                </div>
                                <?php if($assign->notes): ?>
                                    <div class="small text-secondary mt-1 fst-italic">"<?php echo e($assign->notes); ?>"</div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php else: ?>
                    <div class="small text-muted text-center py-3">No reassignment history recorded yet.</div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\DOOTOR ENTERPRISES\resources\views\admin\work-queue-process.blade.php ENDPATH**/ ?>