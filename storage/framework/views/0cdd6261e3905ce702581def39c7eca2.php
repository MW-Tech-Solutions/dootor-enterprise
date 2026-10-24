<?php $__env->startSection('title', 'Support Tickets'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3 mb-4">
    <div>
        <h1 class="h3 fw-bold text-dark mb-1">Customer Support</h1>
        <p class="text-secondary small mb-0">Need help with your application or services? Create a support ticket to chat with our team.</p>
    </div>
    <button type="button" class="btn text-white fw-semibold px-4 py-2.5 rounded-pill shadow-sm" style="background-color: #004225;" data-bs-toggle="modal" data-bs-target="#newTicketModal">
        <i class="bi bi-plus-lg me-2"></i> New Support Ticket
    </button>
</div>

<?php if(session('success')): ?>
<div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
    <?php echo e(session('success')); ?>

    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="card border-0 shadow-sm rounded-4 bg-white" style="border: 1px solid #eef2f6 !important; border-radius: 20px !important; overflow: hidden;">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Ticket ID</th>
                        <th>Subject</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Last Updated</th>
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
                            <span class="fw-semibold text-dark"><?php echo e($t->subject); ?></span>
                            <?php if($t->serviceRequest): ?>
                            <div><small class="text-muted">Ref: <?php echo e($t->serviceRequest->reference_number); ?></small></div>
                            <?php endif; ?>
                        </td>
                        <td><span class="badge bg-light text-dark border"><?php echo e($t->category); ?></span></td>
                        <td>
                            <span class="badge bg-<?php echo e($t->status === 'Open' ? 'danger' : ($t->status === 'In Progress' ? 'info' : 'success')); ?> bg-opacity-10 text-<?php echo e($t->status === 'Open' ? 'danger' : ($t->status === 'In Progress' ? 'info' : 'success')); ?>">
                                <?php echo e($t->status); ?>

                            </span>
                        </td>
                        <td class="small text-muted"><?php echo e($t->updated_at->format('M d, Y H:i')); ?></td>
                        <td class="text-end pe-3">
                            <a href="<?php echo e(route('support.show', $t)); ?>" class="btn btn-sm text-white fw-semibold" style="background-color: #004225; border-radius: 6px;">
                                <i class="bi bi-chat-dots me-1"></i> View Discussion
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">
                            <i class="bi bi-headset fs-1 d-block mb-2 text-muted"></i>
                            You have not submitted any support tickets yet.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- New Support Ticket Modal -->
<div class="modal fade" id="newTicketModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 12px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" style="color: #0f172a;">Submit Support Ticket</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?php echo e(route('support.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Subject</label>
                        <input type="text" name="subject" class="form-control" placeholder="Brief summary of your enquiry" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Category</label>
                        <select name="category" class="form-select" required>
                            <option value="General">General Enquiry</option>
                            <option value="Payment">Payment & Billing</option>
                            <option value="Document Correction">Document Correction / Upload</option>
                            <option value="Service Status">Application Status</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Related Application (Optional)</label>
                        <select name="service_request_id" class="form-select">
                            <option value="">-- None / General --</option>
                            <?php $__currentLoopData = $requests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $req): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($req->id); ?>"><?php echo e($req->reference_number ?? ('DE-'.$req->id)); ?> - <?php echo e($req->service_name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Message</label>
                        <textarea name="message" class="form-control" rows="4" placeholder="Describe your question or issue in detail..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn text-white fw-semibold px-4" style="background-color: #004225;">Submit Ticket</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\DOOTOR ENTERPRISES\resources\views\client\support.blade.php ENDPATH**/ ?>