<?php $__env->startSection('title', 'Application Details - ' . ($request->reference_number ?? ('DE-' . $request->id))); ?>

<?php $__env->startSection('styles'); ?>
<?php echo \Illuminate\View\Factory::parentPlaceholder('styles'); ?>
<style>
    /* Application Stepper Component - Elegant Horizontal Track */
    .stepper-container {
        position: relative;
        padding: 12px 0 6px 0;
    }
    .stepper-track-bar {
        position: absolute;
        top: 22px;
        height: 4px;
        background-color: #e2e8f0;
        border-radius: 4px;
        z-index: 1;
    }
    .stepper-progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #004225 0%, #d4af37 100%);
        border-radius: 4px;
        transition: width 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .stepper-node {
        position: relative;
        z-index: 2;
        text-align: center;
        flex: 1 1 0px;
        padding: 0 4px;
    }
    .stepper-circle {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        margin: 0 auto 8px auto;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
        font-weight: 700;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        border: 3px solid #ffffff;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }
    .stepper-node.completed .stepper-circle {
        background: #004225 !important;
        color: #ffffff !important;
        box-shadow: 0 4px 12px rgba(0, 66, 37, 0.3) !important;
    }
    .stepper-node.active .stepper-circle {
        background: #d4af37 !important;
        color: #0f172a !important;
        box-shadow: 0 0 0 4px rgba(212, 175, 55, 0.25), 0 4px 14px rgba(212, 175, 55, 0.4) !important;
        transform: scale(1.08);
        animation: pulseStepperRing 2s infinite;
    }
    .stepper-node.pending .stepper-circle {
        background: #f1f5f9 !important;
        color: #94a3b8 !important;
        border-color: #ffffff !important;
    }
    @keyframes pulseStepperRing {
        0% {
            box-shadow: 0 0 0 0 rgba(212, 175, 55, 0.45);
        }
        70% {
            box-shadow: 0 0 0 8px rgba(212, 175, 55, 0);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(212, 175, 55, 0);
        }
    }
    .stepper-title {
        font-size: 12.5px;
        line-height: 1.3;
        margin-bottom: 3px;
        font-weight: 600;
    }
    .stepper-chip {
        font-size: 10px;
        padding: 2px 8px;
        border-radius: 50px;
        font-weight: 600;
        display: inline-block;
    }
    @media (max-width: 575.98px) {
        .stepper-circle {
            width: 38px;
            height: 38px;
            font-size: 13.5px;
            border-width: 2.5px;
            margin-bottom: 6px;
        }
        .stepper-track-bar {
            top: 19px;
            height: 3px;
        }
        .stepper-title {
            font-size: 11px;
        }
        .stepper-chip {
            font-size: 9px;
            padding: 1.5px 6px;
        }
    }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-3">
    <a href="<?php echo e(route('client.requests')); ?>" class="btn btn-outline-secondary btn-sm rounded-pill mb-2 px-3 py-1 font-monospace" style="font-size: 12px;">
        <i class="bi bi-arrow-left me-1"></i> Back to My Applications
    </a>
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                <h1 class="h4 fw-bold text-dark mb-0"><?php echo e($request->service_name); ?></h1>
                <span class="badge <?php echo e($request->application_method === 'manual' ? 'bg-warning text-dark' : 'bg-info bg-opacity-10 text-dark border'); ?> rounded-pill font-monospace px-2.5 py-1" style="font-size: 11px;">
                    <i class="bi <?php echo e($request->application_method === 'manual' ? 'bi-file-earmark-arrow-down' : 'bi-laptop'); ?> me-1"></i>
                    <?php echo e(ucfirst($request->application_method ?? 'online')); ?>

                </span>
            </div>
            <span class="font-monospace fw-bold text-success small">Tracking Ref: <?php echo e($request->reference_number); ?></span>
        </div>
        <div>
            <span class="badge rounded-pill fs-6 px-3 py-1.5 fw-bold shadow-sm" style="background-color: #004225 !important; color: #ffffff;">
                Status: <?php echo e($request->status); ?>

            </span>
        </div>
    </div>
</div>

<?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show mb-4 rounded-3 shadow-sm" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i><?php echo e(session('success')); ?>

        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php
    $allStages = $request->service && $request->service->workflowStages 
        ? $request->service->workflowStages->where('is_user_visible', true)->sortBy('sort_order')->values() 
        : collect();

    $histories = $request->stageHistories ? $request->stageHistories->where('is_user_visible', true) : collect();
    $completedStageIds = $histories->pluck('stage_id')->filter()->toArray();
    $completedStageNames = $histories->pluck('stage_name')->map(fn($n) => strtolower(trim($n)))->filter()->toArray();

    // Find matching current stage object based on current_stage_id, current_stage_name, or status
    $currentStageObj = $allStages->first(function($stg) use ($request) {
        return ($request->current_stage_id && $stg->id == $request->current_stage_id)
            || (strcasecmp($stg->stage_name, $request->current_stage_name ?? '') === 0)
            || (strcasecmp($stg->stage_name, $request->status ?? '') === 0)
            || (strcasecmp($stg->status_key, $request->status ?? '') === 0);
    });

    $currentSortOrder = $currentStageObj ? $currentStageObj->sort_order : 1;

    // If status is 'Payment Confirmed' or payment_status is 'Paid', at least Stage 2 (sort_order 2) must be active/completed
    if (($request->status === 'Payment Confirmed' || $request->payment_status === 'Paid') && $currentSortOrder < 2) {
        $currentSortOrder = 2;
    }

    // Build normalized steps array
    $steps = [];
    if ($allStages->count() > 0) {
        foreach ($allStages as $idx => $stg) {
            $isStageInHistory = in_array($stg->id, $completedStageIds) || in_array(strtolower(trim($stg->stage_name)), $completedStageNames);
            $isCurrent = ($currentStageObj && $stg->id == $currentStageObj->id)
                || ($stg->sort_order == $currentSortOrder)
                || (strcasecmp($stg->stage_name, $request->current_stage_name ?? '') === 0)
                || (strcasecmp($stg->stage_name, $request->status ?? '') === 0);

            $isCompleted = ($stg->sort_order < $currentSortOrder) 
                || ($isStageInHistory && $stg->sort_order < $currentSortOrder);

            if ($stg->sort_order < $currentSortOrder) {
                $isCompleted = true;
                $isCurrent = false;
            }

            $steps[] = [
                'number' => $idx + 1,
                'name' => $stg->stage_name,
                'completed' => $isCompleted,
                'current' => $isCurrent,
                'estimated_days' => $stg->estimated_days,
            ];
        }
    } else {
        // Standard 3-stage fallback
        $statusLower = strtolower($request->status ?? '');
        $isReqCompleted = in_array($statusLower, ['completed', 'delivered', 'collected']);
        $isReqProcessing = !$isReqCompleted && (
            in_array($statusLower, ['processing', 'payment confirmed', 'in progress', 'under review', 'embassy submitted'])
            || $request->payment_status === 'Paid'
        );

        $steps = [
            [
                'number' => 1,
                'name' => 'Submitted',
                'completed' => $isReqProcessing || $isReqCompleted,
                'current' => !$isReqProcessing && !$isReqCompleted,
                'estimated_days' => null,
            ],
            [
                'number' => 2,
                'name' => 'Processing',
                'completed' => $isReqCompleted,
                'current' => $isReqProcessing,
                'estimated_days' => $request->service->processing_days ?? 5,
            ],
            [
                'number' => 3,
                'name' => 'Completed',
                'completed' => $isReqCompleted,
                'current' => $isReqCompleted,
                'estimated_days' => null,
            ]
        ];
    }

    $numSteps = count($steps);
    $activeStepIdx = 0;
    foreach ($steps as $idx => $s) {
        if ($s['current']) {
            $activeStepIdx = $idx;
            break;
        } elseif ($s['completed']) {
            $activeStepIdx = $idx;
        }
    }
    $trackProgressPct = $numSteps > 1 ? min(100, max(0, round(($activeStepIdx / ($numSteps - 1)) * 100))) : 100;
    $trackInsetPct = round(100 / (2 * $numSteps), 2);
    $activeStepName = $steps[$activeStepIdx]['name'] ?? ($request->current_stage_name ?? $request->status);
?>

<!-- Application Lifecycle Stepper Card -->
<div class="card border-0 shadow-sm p-3.5 p-md-4 rounded-4 bg-white mb-4" style="border: 1px solid #eef2f6 !important; border-radius: 20px !important;">
    <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <div class="rounded-3 text-white d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width: 36px; height: 36px; background: linear-gradient(135deg, #004225, #0a5c36);">
                <i class="bi bi-diagram-3-fill fs-6"></i>
            </div>
            <div>
                <h5 class="fw-bold text-dark mb-0 fs-6">Application Lifecycle Progress</h5>
                <span class="text-secondary small d-none d-sm-inline" style="font-size: 11px;">Track the live status of your application</span>
            </div>
        </div>
        <span class="badge rounded-pill px-3 py-1.5 fw-bold" style="background-color: #fef3c7; color: #92400e; border: 1px solid rgba(212, 175, 55, 0.35); font-size: 11.5px;">
            <i class="bi bi-clock-history me-1"></i> Current Stage: <?php echo e($activeStepName); ?>

        </span>
    </div>

    <!-- Stepper Track & Nodes (Responsive Horizontal Layout on Mobile & Desktop) -->
    <div class="stepper-container">
        <!-- Connecting Line Behind Circles -->
        <div class="stepper-track-bar" style="left: <?php echo e($trackInsetPct); ?>%; right: <?php echo e($trackInsetPct); ?>%;">
            <div class="stepper-progress-fill" style="width: <?php echo e($trackProgressPct); ?>%;"></div>
        </div>

        <!-- Step Circles & Labels -->
        <div class="d-flex justify-content-between align-items-start position-relative">
            <?php $__currentLoopData = $steps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $nodeState = $step['completed'] ? 'completed' : ($step['current'] ? 'active' : 'pending');
                ?>
                <div class="stepper-node <?php echo e($nodeState); ?>">
                    <div class="stepper-circle">
                        <?php if($step['completed']): ?>
                            <i class="bi bi-check-lg fw-bold"></i>
                        <?php else: ?>
                            <span><?php echo e($step['number']); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="stepper-title <?php echo e($step['current'] ? 'text-dark fw-bold' : ($step['completed'] ? 'text-dark fw-semibold' : 'text-muted')); ?>">
                        <?php echo e($step['name']); ?>

                    </div>
                    <?php if($step['completed']): ?>
                        <span class="badge bg-success bg-opacity-10 text-success stepper-chip">Done</span>
                    <?php elseif($step['current']): ?>
                        <span class="badge bg-warning bg-opacity-25 text-dark stepper-chip fw-bold">In Progress</span>
                    <?php else: ?>
                        <span class="badge bg-light text-muted border stepper-chip">Upcoming</span>
                    <?php endif; ?>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>

    <!-- Friendly Contextual Status Message -->
    <div class="mt-3 p-3 rounded-3 d-flex align-items-center gap-2.5" style="background-color: #f8fafc; border: 1px solid #e2e8f0;">
        <div class="rounded-circle d-flex align-items-center justify-content-center text-white flex-shrink-0" style="width: 28px; height: 28px; background-color: #004225; font-size: 13px;">
            <i class="bi bi-info-lg"></i>
        </div>
        <div class="small text-secondary flex-grow-1" style="font-size: 12px; line-height: 1.45;">
            <?php if(in_array(strtolower($activeStepName), ['completed', 'delivered', 'collected'])): ?>
                <strong class="text-success d-block">Application Completed!</strong>
                Your service has been finalized. Final documents have been issued or made available.
            <?php elseif(in_array(strtolower($activeStepName), ['processing', 'payment confirmed', 'in progress'])): ?>
                <strong class="text-dark d-block">Processing Underway</strong>
                Your files and details are actively being processed and verified by our consular administrative team.
            <?php else: ?>
                <strong class="text-dark d-block">Application Received</strong>
                Your application has been logged into the system and queued for processing officer review.
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Left Column: Details & Documents -->
    <div class="col-lg-8">
        <!-- Service & Financial Summary Card (Polished Mobile Tiles) -->
        <div class="card border-0 shadow-sm p-3.5 p-md-4 rounded-4 bg-white mb-4" style="border: 1px solid #eef2f6 !important; border-radius: 20px !important;">
            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom flex-wrap gap-2">
                <h5 class="fw-bold text-dark mb-0 fs-6 d-flex align-items-center gap-2">
                    <i class="bi bi-wallet2 text-success"></i>
                    <span>Service &amp; Financial Summary</span>
                </h5>
                <span class="badge rounded-pill bg-light text-dark border px-2.5 py-1" style="font-size: 11px;">
                    Ref: <span class="font-monospace text-success fw-bold"><?php echo e($request->reference_number); ?></span>
                </span>
            </div>

            <!-- Financial Metrics Highlight Tiles (Responsive Amount Boxes) -->
            <div class="row g-2 mb-3">
                <div class="col-4">
                    <div class="financial-tile p-2 p-sm-2.5 rounded-3 text-center d-flex flex-column justify-content-center h-100" style="background-color: #f8fafc; border: 1px solid #e2e8f0; min-height: 72px;">
                        <span class="text-secondary d-block financial-tile-label" style="font-size: 11px; font-weight: 500; letter-spacing: -0.01em;">Total Fee</span>
                        <div class="financial-tile-amount text-dark fw-bold mt-1" style="font-size: clamp(0.78rem, 3.2vw, 1.05rem); line-height: 1.2; word-break: break-word;">
                            <?php echo e($currencySymbol ?? '$'); ?><?php echo e(number_format($request->price, 2)); ?>

                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="financial-tile p-2 p-sm-2.5 rounded-3 text-center d-flex flex-column justify-content-center h-100" style="background-color: #f0fdf4; border: 1px solid #bbf7d0; min-height: 72px;">
                        <span class="text-success d-block financial-tile-label" style="font-size: 11px; font-weight: 600; letter-spacing: -0.01em;">Amount Paid</span>
                        <div class="financial-tile-amount text-success fw-bold mt-1" style="color: #004225 !important; font-size: clamp(0.78rem, 3.2vw, 1.05rem); line-height: 1.2; word-break: break-word;">
                            <?php echo e($currencySymbol ?? '$'); ?><?php echo e(number_format($request->amount_paid, 2)); ?>

                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="financial-tile p-2 p-sm-2.5 rounded-3 text-center d-flex flex-column justify-content-center h-100 <?php echo e($request->outstanding_balance > 0 ? 'bg-danger bg-opacity-10' : 'bg-light'); ?>" style="border: 1px solid <?php echo e($request->outstanding_balance > 0 ? 'rgba(239,68,68,0.3)' : '#e2e8f0'); ?>; min-height: 72px;">
                        <span class="<?php echo e($request->outstanding_balance > 0 ? 'text-danger' : 'text-secondary'); ?> d-block financial-tile-label" style="font-size: 11px; font-weight: 600; letter-spacing: -0.01em;">Balance</span>
                        <div class="financial-tile-amount <?php echo e($request->outstanding_balance > 0 ? 'text-danger' : 'text-dark'); ?> fw-bold mt-1" style="font-size: clamp(0.78rem, 3.2vw, 1.05rem); line-height: 1.2; word-break: break-word;">
                            <?php echo e($currencySymbol ?? '$'); ?><?php echo e(number_format($request->outstanding_balance, 2)); ?>

                        </div>
                    </div>
                </div>
            </div>

            <!-- Service Details Grid -->
            <div class="row g-3 small">
                <div class="col-12 col-sm-6">
                    <span class="text-muted d-block" style="font-size: 11.5px;">Service Name</span>
                    <span class="fw-bold text-dark fs-6"><?php echo e($request->service_name); ?></span>
                    <?php if($request->sub_service_name): ?>
                        <span class="text-muted d-block small">(<?php echo e($request->sub_service_name); ?>)</span>
                    <?php endif; ?>
                </div>
                <?php if($request->payment_reference): ?>
                    <div class="col-12 col-sm-6">
                        <span class="text-muted d-block" style="font-size: 11.5px;">Transaction / Payment Ref</span>
                        <span class="fw-bold font-monospace text-primary fs-6"><?php echo e($request->payment_reference); ?></span>
                    </div>
                <?php endif; ?>
                <div class="col-12 col-sm-6">
                    <span class="text-muted d-block" style="font-size: 11.5px;">Assigned Officer</span>
                    <span class="fw-semibold text-dark fs-6"><?php echo e($request->assignedStaff->name ?? ($request->assignedRole->name ?? 'Dooter Support Desk')); ?></span>
                </div>
                <div class="col-12 col-sm-6">
                    <span class="text-muted d-block" style="font-size: 11.5px;">Submission Date</span>
                    <span class="fw-semibold text-dark fs-6"><?php echo e($request->created_at->format('M d, Y, h:i A')); ?></span>
                </div>
            </div>

            <?php if($request->manual_form_path || $request->passport_photo_path): ?>
                <div class="mt-4 pt-3 border-top d-flex flex-wrap gap-4 align-items-center">
                    <?php if($request->passport_photo_path): ?>
                        <div>
                            <span class="text-muted d-block small mb-2">Uploaded Passport Photograph</span>
                            <a href="<?php echo e(asset($request->passport_photo_path)); ?>" target="_blank">
                                <img src="<?php echo e(asset($request->passport_photo_path)); ?>" alt="Passport Photo" class="rounded border p-1" style="width: 90px; height: 110px; object-fit: cover;">
                            </a>
                        </div>
                    <?php endif; ?>
                    <?php if($request->manual_form_path): ?>
                        <div>
                            <span class="text-muted d-block small mb-2">Uploaded Scanned Application Form</span>
                            <a href="<?php echo e(asset($request->manual_form_path)); ?>" target="_blank" class="btn btn-outline-success btn-sm font-semibold rounded-pill px-3 py-2" style="border-color: #004225; color: #004225;">
                                <i class="bi bi-file-earmark-pdf me-1"></i> View Scanned Form
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if(is_array($request->form_data) && count($request->form_data) > 0): ?>
                <hr class="my-4 border-light">
                <h5 class="fw-bold text-dark mb-3">Submitted Questionnaire Details</h5>
                <div class="row g-3 small bg-light p-3 rounded-3">
                    <?php $__currentLoopData = $request->form_data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="col-sm-6">
                            <span class="text-muted d-block"><?php echo e(ucwords(str_replace('_', ' ', $key))); ?></span>
                            <span class="fw-semibold text-dark"><?php echo e(is_array($val) ? implode(', ', $val) : $val); ?></span>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Documents Verification Status -->
        <div class="card border-0 shadow-sm p-3.5 p-md-4 rounded-4 bg-white mb-4" style="border: 1px solid #eef2f6 !important; border-radius: 20px !important; padding: 22px 20px !important;">
            <h5 class="fw-bold text-dark mb-3">Submitted Documents & Verification</h5>
            <?php if($request->requestDocuments && $request->requestDocuments->count() > 0): ?>
                <div class="list-group list-group-flush">
                    <?php $__currentLoopData = $request->requestDocuments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-3 border-bottom">
                            <div>
                                <span class="fw-semibold text-dark d-block">
                                    <i class="bi bi-file-earmark-text me-2 text-success"></i> <?php echo e($doc->document_name); ?>

                                </span>
                                <?php if($doc->admin_notes): ?>
                                    <small class="text-danger d-block mt-1"><i class="bi bi-exclamation-triangle me-1"></i> Officer Note: <?php echo e($doc->admin_notes); ?></small>
                                <?php endif; ?>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-<?php echo e($doc->status === 'Accepted' ? 'success' : ($doc->status === 'Requires Correction' || $doc->status === 'Rejected' ? 'danger' : 'warning')); ?> bg-opacity-10 text-<?php echo e($doc->status === 'Accepted' ? 'success' : ($doc->status === 'Requires Correction' || $doc->status === 'Rejected' ? 'danger' : 'warning')); ?> px-3 py-1">
                                    <?php echo e($doc->status); ?>

                                </span>
                                <a href="<?php echo e(asset($doc->file_path)); ?>" target="_blank" class="btn btn-light btn-sm rounded-pill px-3">View File</a>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php else: ?>
                <div class="bg-light p-3 rounded text-center small text-secondary">
                    <i class="bi bi-info-circle me-1"></i> No documents attached to this application.
                </div>
            <?php endif; ?>
        </div>

        <!-- Public Stage Notes & Communications -->
        <?php if($request->applicationNotes && $request->applicationNotes->where('is_internal', false)->count() > 0): ?>
            <div class="card border-0 shadow-sm p-4 rounded-4 bg-white mb-4">
                <h5 class="fw-bold text-dark mb-3"><i class="bi bi-chat-left-text me-2 text-success"></i> Officer Updates & Notes</h5>
                <div class="list-group list-group-flush">
                    <?php $__currentLoopData = $request->applicationNotes->where('is_internal', false); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $note): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="list-group-item px-0 py-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <strong class="small text-dark"><?php echo e($note->user ? $note->user->name : 'Processing Officer'); ?></strong>
                                <span class="text-muted" style="font-size: 11px;"><?php echo e($note->created_at->format('M d, Y H:i')); ?></span>
                            </div>
                            <p class="small text-secondary mb-0"><?php echo e($note->note); ?></p>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Right Column: Status & Credo Payment -->
    <div class="col-lg-4">
        <!-- Status Card -->
        <div class="card border-0 shadow-sm p-3.5 p-md-4 rounded-4 bg-white mb-4" style="border: 1px solid #eef2f6 !important; border-radius: 20px !important; padding: 22px 20px !important;">
            <h5 class="fw-bold text-dark mb-3">Current Application Status</h5>
            
            <div class="p-3 bg-light rounded-3 text-center mb-3">
                <span class="text-muted small d-block">Current Stage</span>
                <h6 class="fw-bold text-dark my-1"><?php echo e($request->current_stage_name ?? $request->status); ?></h6>
            </div>
            
            <div class="small">
                <div class="d-flex justify-content-between py-2 border-bottom border-light">
                    <span class="text-muted">Payment status:</span>
                    <span class="badge bg-<?php echo e($request->payment_status === 'Paid' ? 'success' : 'danger'); ?> bg-opacity-10 text-<?php echo e($request->payment_status === 'Paid' ? 'success' : 'danger'); ?>">
                        <?php echo e($request->payment_status); ?>

                    </span>
                </div>
                <?php if($request->payment_gateway): ?>
                    <div class="d-flex justify-content-between py-2 border-bottom border-light">
                        <span class="text-muted">Payment Gateway:</span>
                        <span class="fw-semibold text-dark text-uppercase"><?php echo e($request->payment_gateway); ?></span>
                    </div>
                <?php endif; ?>
                <?php if($request->payment_reference): ?>
                    <div class="d-flex justify-content-between py-2 border-bottom border-light">
                        <span class="text-muted">Transaction ID:</span>
                        <span class="fw-semibold font-monospace text-dark" style="font-size: 11px;"><?php echo e($request->payment_reference); ?></span>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Payment Checkout Card (Shown if unpaid or has balance) -->
        <?php if($request->payment_status !== 'Paid' || $request->outstanding_balance > 0): ?>
            <?php
                $activeGw = strtolower($settings->payment_gateway ?? config('services.payment.gateway') ?: env('PAYMENT_GATEWAY', 'paystack'));
                $gwLabel = $activeGw === 'credo' ? 'Credo' : 'Paystack';
                $currencySymbol = ($settings->default_currency ?? config('services.payment.currency', 'USD')) === 'NGN' ? '₦' : '$';
                $currencyCode = $settings->default_currency ?? config('services.payment.currency', 'USD');
            ?>
            <div class="card border-0 shadow-sm p-3.5 p-md-4 rounded-4 text-white mb-4" style="background: linear-gradient(135deg, #004225, #006637); border-radius: 20px !important; padding: 22px 20px !important;">
                <h5 class="fw-bold text-white mb-2"><i class="bi bi-credit-card me-2"></i> <?php echo e($gwLabel); ?> Checkout</h5>
                <p class="small text-white-50 mb-3">Pay securely online using credit/debit card or bank transfer via <?php echo e($gwLabel); ?> Gateway.</p>

                <div class="d-flex justify-content-between align-items-center bg-white bg-opacity-10 p-3 rounded-3 mb-4">
                    <span class="small text-white-50">Amount Due:</span>
                    <span class="fw-bold fs-4 text-white"><?php echo e($currencyCode); ?> <?php echo e(number_format($request->outstanding_balance > 0 ? $request->outstanding_balance : $request->price, 2)); ?></span>
                </div>

                <form action="<?php echo e(route('payment.checkout', $request->id)); ?>" method="POST" id="payForm" onsubmit="return handlePaySubmit(event, this)">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="amount" value="<?php echo e($request->outstanding_balance > 0 ? $request->outstanding_balance : $request->price); ?>">
                    <input type="hidden" name="gateway" value="<?php echo e($activeGw); ?>">
                    <button type="submit" id="payBtn" onclick="triggerPaySpinner(this)" class="btn btn-light w-100 py-3 rounded-3 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2" style="color: #004225; transition: all 0.25s ease;">
                        <i class="bi bi-lock-fill me-1"></i>
                        <span>Pay Now with <?php echo e($gwLabel); ?></span>
                    </button>
                </form>
            </div>

            <script>
                function resetPayButton() {
                    const btn = document.getElementById('payBtn');
                    const form = document.getElementById('payForm');
                    if (form) {
                        form.dataset.submitting = 'false';
                    }
                    if (btn) {
                        btn.dataset.spinning = 'false';
                        btn.style.pointerEvents = 'auto';
                        btn.style.opacity = '1';
                        btn.classList.remove('shadow-lg');
                        btn.innerHTML = `
                            <i class="bi bi-lock-fill me-1"></i>
                            <span>Pay Now with <?php echo e($gwLabel); ?></span>
                        `;
                    }
                }

                function triggerPaySpinner(btn) {
                    if (!btn || btn.dataset.spinning === 'true') return;
                    btn.dataset.spinning = 'true';
                    btn.style.pointerEvents = 'none';
                    btn.style.opacity = '0.9';
                    btn.classList.add('shadow-lg');
                    btn.innerHTML = `
                        <div class="d-flex align-items-center justify-content-center gap-2 py-1">
                            <span class="spinner-border spinner-border-sm me-1" role="status" style="width: 1.25rem; height: 1.25rem; border-width: 2.5px; color: #004225 !important;"></span>
                            <span class="spinner-grow spinner-grow-sm me-1" role="status" style="width: 0.75rem; height: 0.75rem; animation-delay: 0.15s; color: #004225 !important;"></span>
                            <span class="spinner-grow spinner-grow-sm me-1" role="status" style="width: 0.5rem; height: 0.5rem; animation-delay: 0.3s; color: #004225 !important;"></span>
                            <span class="fw-bold fs-6 ms-1" style="color: #004225;">Connecting to <?php echo e($gwLabel); ?>...</span>
                        </div>
                    `;
                }

                function handlePaySubmit(event, form) {
                    if (form.dataset.submitting === 'true') {
                        event.preventDefault();
                        return false;
                    }

                    event.preventDefault();
                    form.dataset.submitting = 'true';

                    const btn = document.getElementById('payBtn');
                    triggerPaySpinner(btn);

                    setTimeout(function() {
                        form.submit();
                    }, 1200);
                }

                window.addEventListener('pageshow', function() {
                    resetPayButton();
                });
                document.addEventListener('DOMContentLoaded', function() {
                    resetPayButton();
                });
            </script>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\DOOTOR ENTERPRISES\resources\views\client\request-details.blade.php ENDPATH**/ ?>