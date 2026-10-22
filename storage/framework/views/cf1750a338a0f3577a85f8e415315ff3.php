<?php $__env->startSection('title', 'Email Template Management'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 fw-bold text-dark mb-1">Notification Email Templates</h1>
        <p class="text-secondary small mb-0">Manage customer and administrative email notification templates and placeholders.</p>
    </div>
</div>

<?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i><?php echo e(session('success')); ?>

        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="row g-4">
    <?php $__currentLoopData = $templates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tmpl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="fw-bold text-dark mb-0"><?php echo e($tmpl->title); ?></h6>
                        <span class="badge bg-secondary font-monospace mt-1" style="font-size: 10px;"><?php echo e($tmpl->code); ?></span>
                    </div>
                    <span class="badge <?php echo e($tmpl->is_active ? 'bg-success' : 'bg-danger'); ?>">
                        <?php echo e($tmpl->is_active ? 'Active' : 'Disabled'); ?>

                    </span>
                </div>
                <div class="card-body p-4">
                    <form action="<?php echo e(route('admin.email-templates.update', $tmpl)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PATCH'); ?>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Email Subject Line</label>
                            <input type="text" name="subject" class="form-control form-control-sm" value="<?php echo e($tmpl->subject); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">HTML Body Template</label>
                            <textarea name="body_html" class="form-control font-monospace" rows="8" style="font-size: 12px;" required><?php echo e($tmpl->body_html); ?></textarea>
                        </div>

                        <div class="bg-light p-2 rounded mb-3" style="font-size: 11px; color: #475569;">
                            <strong>Supported Safe Placeholders:</strong><br>
                            <code><?php echo e($tmpl->variables_description ?? '{full_name}, {service_name}, {reference_number}, {status}, {current_stage}, {company_name}, {dashboard_link}'); ?></code>
                        </div>

                        <div class="d-flex align-items-center justify-content-between border-top pt-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="act_t_<?php echo e($tmpl->id); ?>" <?php echo e($tmpl->is_active ? 'checked' : ''); ?>>
                                <label class="form-check-label small fw-semibold" for="act_t_<?php echo e($tmpl->id); ?>">Enable Template</label>
                            </div>
                            <button type="submit" class="btn btn-success btn-sm px-3" style="background-color: #004225; border: none;">
                                <i class="bi bi-check-circle me-1"></i> Save Template
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\DOOTOR ENTERPRISES\resources\views\admin\email-templates.blade.php ENDPATH**/ ?>