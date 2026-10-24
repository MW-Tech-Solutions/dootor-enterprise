<?php $__env->startSection('title', 'My Requests - ' . ($settings->platform_name ?? 'Dooter Enterprises')); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-4">
    <h1 class="h3 fw-bold text-dark mb-1">My Requests</h1>
    <p class="text-secondary small">View and manage your service orders and tracking</p>
</div>

<?php if(count($requests) > 0): ?>
    <!-- Mobile Card View (Matches Screenshot Exact 1:1) -->
    <div class="d-md-none">
        <?php $__currentLoopData = $requests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="card border-0 shadow-sm rounded-4 bg-white mb-3 client-request-card" style="border: 1px solid #eef2f6 !important; border-radius: 20px !important; padding: 22px 20px !important;">
                <!-- Application Ref Header -->
                <div class="d-flex align-items-center gap-2.5 mb-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background-color: #ecfdf5; color: #059669; border: 1px solid #d1fae5; flex-shrink: 0; border-radius: 12px !important;">
                        <i class="bi bi-file-earmark-text" style="font-size: 19px;"></i>
                    </div>
                    <div>
                        <span class="text-secondary text-uppercase d-block" style="font-size: 10px; font-weight: 600; letter-spacing: 0.03em;">Application Ref</span>
                        <span class="font-monospace fw-bold" style="color: #004225; font-size: 13px;"><?php echo e($request->reference_number ?? ('DE-' . $request->id)); ?></span>
                    </div>
                </div>

                <!-- 2-Column Info Section -->
                <div class="row g-2 mb-3">
                    <div class="col-7 pe-2">
                        <span class="text-secondary d-block" style="font-size: 10.5px;">Service</span>
                        <span class="fw-bold text-dark d-block text-truncate" style="font-size: 13.5px;"><?php echo e($request->service_name); ?></span>
                        
                        <span class="text-secondary d-block mt-2" style="font-size: 10.5px;">Subservice</span>
                        <span class="text-secondary d-block text-truncate" style="font-size: 12px; color: #64748b !important;"><?php echo e($request->sub_service_name ?? '—'); ?></span>
                    </div>
                    <div class="col-5 ps-2">
                        <span class="text-secondary d-block" style="font-size: 10.5px;">Processor</span>
                        <span class="text-dark fw-semibold d-block text-truncate" style="font-size: 12.5px;"><?php echo e($request->vendor_name ?? 'Dootor Enterprises'); ?></span>
                        
                        <span class="text-secondary d-block mt-2" style="font-size: 10.5px;">Transaction ID</span>
                        <span class="text-secondary font-monospace d-block text-truncate" style="font-size: 11px; color: #64748b !important;"><?php echo e($request->payment_reference ?? '—'); ?></span>
                    </div>
                </div>

                <!-- Price and Badges -->
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 pt-2.5 border-top mb-3" style="border-color: #f1f5f9 !important;">
                    <div>
                        <span class="text-secondary d-block" style="font-size: 10.5px;">Price / Paid</span>
                        <span class="fw-bold text-dark" style="font-size: 1.25rem; font-weight: 800;"><?php echo e($currencySymbol ?? '$'); ?><?php echo e(number_format($request->amount_paid > 0 ? $request->amount_paid : $request->price, 2)); ?></span>
                    </div>
                    <div class="d-flex align-items-center gap-1.5 flex-wrap">
                        <?php if($request->payment_status === 'Paid'): ?>
                            <span class="badge fw-bold rounded-pill px-2.5 py-1 d-inline-flex align-items-center gap-1" style="background-color: #ecfdf5; color: #059669; font-size: 10.5px;">
                                <i class="bi bi-shield-check"></i> Paid
                            </span>
                        <?php elseif($request->payment_status === 'Partial'): ?>
                            <span class="badge fw-bold rounded-pill px-2.5 py-1 d-inline-flex align-items-center gap-1" style="background-color: #e0f2fe; color: #0284c7; font-size: 10.5px;">
                                Partial
                            </span>
                        <?php else: ?>
                            <span class="badge fw-bold rounded-pill px-2.5 py-1 d-inline-flex align-items-center gap-1" style="background-color: #fffbeb; color: #d97706; font-size: 10.5px;">
                                <i class="bi bi-exclamation-circle"></i> Unpaid
                            </span>
                        <?php endif; ?>

                        <?php
                            $stageName = $request->current_stage_name ?? $request->status;
                            $stageBadgeStyle = match(true) {
                                in_array($request->status, ['Completed', 'Approved', 'Verified']) => 'background-color: #ecfdf5; color: #059669;',
                                in_array($request->status, ['In Progress', 'Processing', 'Document Verification']) => 'background-color: #e0f2fe; color: #0284c7;',
                                in_array($request->status, ['Application Submitted', 'Submitted', 'Under Review']) => 'background-color: #fef3c7; color: #b45309;',
                                in_array($request->status, ['Awaiting Assignment', 'Pending']) => 'background-color: #fef3c7; color: #b45309;',
                                in_array($request->status, ['Cancelled', 'Rejected']) => 'background-color: #fee2e2; color: #dc2626;',
                                default => 'background-color: #e0f2fe; color: #0284c7;'
                            };
                        ?>
                        <span class="badge fw-bold rounded-pill px-2.5 py-1 d-inline-flex align-items-center gap-1" style="<?php echo e($stageBadgeStyle); ?>; font-size: 10.5px;">
                            <i class="bi bi-clock"></i> <?php echo e($stageName); ?>

                        </span>
                    </div>
                </div>

                <!-- Footer: Submitted date and Action Buttons -->
                <div class="d-flex align-items-center justify-content-between pt-2.5 border-top" style="border-color: #f1f5f9 !important;">
                    <div class="d-flex align-items-center gap-1.5 text-secondary" style="font-size: 11.5px;">
                        <i class="bi bi-calendar3"></i>
                        <span>Submitted <?php echo e($request->created_at->format('M d, Y')); ?></span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <a href="<?php echo e(route('client.request.details', $request->id)); ?>" class="btn btn-sm rounded-pill px-3.5 fw-semibold" style="font-size: 12px; border: 1.5px solid #004225; color: #004225; background: #ffffff;">
                            View
                        </a>
                        <?php if($request->payment_status === 'Unpaid'): ?>
                            <form action="<?php echo e(route('client.request.delete', $request->id)); ?>" method="POST" class="m-0" onsubmit="return confirm('Are you sure you want to cancel this booking?');">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="btn btn-sm rounded-circle d-flex align-items-center justify-content-center p-0" style="width: 32px; height: 32px; border: 1.5px solid #fca5a5; color: #ef4444; background: #ffffff;" title="Delete Unpaid Application">
                                    <i class="bi bi-trash" style="font-size: 13px;"></i>
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <!-- Desktop Table View -->
    <div class="card border-0 shadow-sm p-4 rounded-4 bg-white d-none d-md-block">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr class="text-secondary small">
                        <th>Application Ref</th>
                        <th>Service Requested</th>
                        <th>Processor</th>
                        <th>Transaction ID</th>
                        <th>Price / Paid</th>
                        <th>Payment Status</th>
                        <th>Processing Stage</th>
                        <th>Submitted</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $requests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td>
                                <span class="font-monospace fw-bold text-primary small"><?php echo e($request->reference_number ?? ('DE-' . $request->id)); ?></span>
                            </td>
                            <td>
                                <span class="fw-semibold text-dark"><?php echo e($request->service_name); ?></span>
                                <?php if($request->sub_service_name): ?>
                                    <br><span class="text-muted small"><?php echo e($request->sub_service_name); ?></span>
                                <?php endif; ?>
                            </td>
                            <td><span class="small"><?php echo e($request->vendor_name ?? 'Dootor Admin'); ?></span></td>
                            <td>
                                <?php if($request->payment_reference): ?>
                                    <span class="badge bg-light text-dark font-monospace border small" title="Payment Reference"><i class="bi bi-receipt me-1 text-success"></i><?php echo e($request->payment_reference); ?></span>
                                <?php else: ?>
                                    <span class="text-muted small">&mdash;</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="fw-bold text-dark"><?php echo e($currencySymbol ?? '$'); ?><?php echo e(number_format($request->price, 2)); ?></div>
                                <?php if($request->amount_paid > 0): ?>
                                    <div class="text-success small" style="font-size: 11px;">Paid: <?php echo e($currencySymbol ?? '$'); ?><?php echo e(number_format($request->amount_paid, 2)); ?></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($request->payment_status === 'Paid'): ?>
                                    <span class="badge bg-success-subtle text-success rounded-pill px-2.5 py-1"><i class="bi bi-check-circle me-1"></i>Paid</span>
                                <?php elseif($request->payment_status === 'Partial'): ?>
                                    <span class="badge bg-info-subtle text-info rounded-pill px-2.5 py-1">Partial</span>
                                <?php else: ?>
                                    <span class="badge bg-warning-subtle text-warning rounded-pill px-2.5 py-1"><i class="bi bi-clock me-1"></i>Unpaid</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                    $stageDisplay = $request->current_stage_name ?? $request->status;
                                    $statusBadge = match(true) {
                                        in_array($request->status, ['Completed', 'Approved', 'Verified']) => 'bg-success text-white',
                                        in_array($request->status, ['Processing', 'In Progress', 'Under Review', 'Document Verification']) => 'bg-info text-dark',
                                        in_array($request->status, ['Payment Confirmed', 'Submitted', 'Awaiting Assignment']) => 'bg-warning text-dark',
                                        in_array($request->status, ['Cancelled', 'Rejected']) => 'bg-danger text-white',
                                        default => 'bg-warning text-dark'
                                    };
                                ?>
                                <span class="badge <?php echo e($statusBadge); ?> rounded-pill px-2.5 py-1"><?php echo e($stageDisplay); ?></span>
                            </td>
                            <td class="small text-secondary"><?php echo e($request->created_at->format('M d, Y')); ?></td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="<?php echo e(route('client.request.details', $request->id)); ?>" class="btn btn-outline-dark btn-sm rounded-pill px-3">View</a>
                                    <?php if($request->payment_status === 'Unpaid'): ?>
                                        <form action="<?php echo e(route('client.request.delete', $request->id)); ?>" method="POST" class="m-0" onsubmit="return confirm('Are you sure you want to cancel this booking?');">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill" title="Delete Unpaid Application"><i class="bi bi-trash"></i></button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
<?php else: ?>
    <div class="card border-0 shadow-sm p-4 rounded-4 bg-white text-center py-5">
        <i class="bi bi-clipboard fs-1 text-muted"></i>
        <p class="text-secondary mt-2 small">You haven't ordered any services yet.</p>
        <div>
            <a href="<?php echo e(route('client.services')); ?>" class="btn btn-dark rounded-pill px-4 mt-2">Browse Services</a>
        </div>
    </div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\DOOTOR ENTERPRISES\resources\views\client\requests.blade.php ENDPATH**/ ?>