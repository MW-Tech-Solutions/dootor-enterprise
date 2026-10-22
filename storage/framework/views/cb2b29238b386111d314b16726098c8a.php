<?php $__env->startSection('title', 'My Requests - ' . ($settings->platform_name ?? 'Dooter Enterprises')); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-4">
    <h1 class="h3 fw-bold text-dark mb-1">My Requests</h1>
    <p class="text-secondary small">View and manage your service orders and tracking</p>
</div>

<div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
    <?php if(count($requests) > 0): ?>
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
                            <td><span class="small"><?php echo e($request->vendor_name ?? 'Dooter Admin'); ?></span></td>
                            <td>
                                <?php if($request->payment_reference): ?>
                                    <span class="badge bg-light text-dark font-monospace border small" title="Payment Reference"><i class="bi bi-receipt me-1 text-success"></i><?php echo e($request->payment_reference); ?></span>
                                <?php else: ?>
                                    <span class="text-muted small">&mdash;</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="fw-bold text-dark"><?php echo e($currencySymbol); ?><?php echo e(number_format($request->price, 2)); ?></div>
                                <?php if($request->amount_paid > 0): ?>
                                    <div class="text-success small" style="font-size: 11px;">Paid: <?php echo e($currencySymbol); ?><?php echo e(number_format($request->amount_paid, 2)); ?></div>
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
    <?php else: ?>
        <div class="text-center py-5">
            <i class="bi bi-clipboard fs-1 text-muted"></i>
            <p class="text-secondary mt-2 small">You haven't ordered any services yet.</p>
            <a href="<?php echo e(route('client.services')); ?>" class="btn btn-dark rounded-pill px-4 mt-3">Browse Services</a>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\DOOTOR ENTERPRISES\resources\views\client\requests.blade.php ENDPATH**/ ?>