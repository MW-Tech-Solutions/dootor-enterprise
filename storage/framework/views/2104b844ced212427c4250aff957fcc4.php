<?php $__env->startSection('title', 'Support Desk Management'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="color: #0f172a;">Customer Support Desk</h4>
        <p class="text-muted small mb-0">Manage client enquiries, support tickets, and communication threads.</p>
    </div>
</div>

<div class="card border-0 shadow-sm" style="border-radius: 12px;">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Ticket ID</th>
                        <th>Client</th>
                        <th>Subject</th>
                        <th>Category</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th class="text-end pe-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $tickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="ps-3">
                            <span class="font-monospace fw-bold text-primary"><?php echo e($t->ticket_number); ?></span>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark"><?php echo e($t->user->name ?? 'Client'); ?></div>
                            <small class="text-muted"><?php echo e($t->user->email ?? ''); ?></small>
                        </td>
                        <td>
                            <span class="fw-semibold text-dark"><?php echo e($t->subject); ?></span>
                            <?php if($t->serviceRequest): ?>
                            <div><small class="text-muted">Ref: <?php echo e($t->serviceRequest->reference_number); ?></small></div>
                            <?php endif; ?>
                        </td>
                        <td><span class="badge bg-light text-dark border"><?php echo e($t->category); ?></span></td>
                        <td>
                            <span class="badge bg-<?php echo e($t->priority === 'Urgent' ? 'danger' : ($t->priority === 'High' ? 'warning' : 'secondary')); ?>">
                                <?php echo e($t->priority); ?>

                            </span>
                        </td>
                        <td>
                            <span class="badge bg-<?php echo e($t->status === 'Open' ? 'danger' : ($t->status === 'In Progress' ? 'info' : 'success')); ?> bg-opacity-10 text-<?php echo e($t->status === 'Open' ? 'danger' : ($t->status === 'In Progress' ? 'info' : 'success')); ?>">
                                <?php echo e($t->status); ?>

                            </span>
                        </td>
                        <td class="small text-muted"><?php echo e($t->created_at->format('M d, Y H:i')); ?></td>
                        <td class="text-end pe-3">
                            <a href="<?php echo e(route('support.show', $t)); ?>" class="btn btn-sm text-white fw-semibold" style="background-color: #004225; border-radius: 6px;">
                                <i class="bi bi-chat-text me-1"></i> View Thread
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">No support tickets submitted yet.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\DOOTOR ENTERPRISES\resources\views\admin\support.blade.php ENDPATH**/ ?>