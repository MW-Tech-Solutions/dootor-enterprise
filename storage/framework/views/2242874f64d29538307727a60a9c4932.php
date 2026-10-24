<?php $__env->startSection('title', 'Payment Verification - Dootor Enterprises'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid p-0">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
        <div>
            <h3 class="fw-bold text-dark mb-1">
                <i class="bi bi-credit-card-2-front text-success me-2"></i> Payment Verification & Re-query
            </h3>
            <p class="text-muted small mb-0">Query payment gateway APIs directly to verify client payments and automatically update database records for unconfirmed or delayed transactions.</p>
        </div>
    </div>

    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm border-0 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm border-0 mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> <?php echo e(session('error')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if(session('info')): ?>
        <div class="alert alert-info alert-dismissible fade show rounded-3 shadow-sm border-0 mb-4" role="alert">
            <i class="bi bi-info-circle-fill me-2"></i> <?php echo e(session('info')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

<?php
    $canRequery = auth()->user()->hasPermission('applications.update') || auth()->user()->hasPermission('applications.status.update') || auth()->user()->hasPermission('services.pricing.update');
?>

    <?php if($canRequery): ?>
    <!-- Direct Gateway API Query Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
        <div class="card-body p-4">
            <h5 class="fw-bold text-dark mb-2">
                <i class="bi bi-cpu text-primary me-2"></i> Direct Gateway API Re-query Search
            </h5>
            <p class="text-muted small mb-3">Enter an Application Reference (e.g. <code>DOOTOR-BVRE-2026-113544</code>) or Payment Reference to check payment servers directly.</p>
            
            <form action="<?php echo e(route('admin.payment-verification.query')); ?>" method="POST" class="row g-3 align-items-center">
                <?php echo csrf_field(); ?>
                <div class="col-md-9">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" name="reference" class="form-control bg-light border-start-0 py-2" placeholder="Paste Reference Number or Gateway Payment Reference..." required>
                    </div>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn text-white fw-semibold w-100 py-2" style="background-color: #004225; border-radius: 8px;">
                        <i class="bi bi-arrow-repeat me-1"></i> Query Gateway API Now
                    </button>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <!-- Filter & Table Card -->
    <div class="card border-0 shadow-sm rounded-4 bg-white">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
                <h5 class="fw-bold text-dark mb-0">Service Applications Payment Log</h5>

                <form action="<?php echo e(route('admin.payment-verification')); ?>" method="GET" class="d-flex gap-2">
                    <select name="status_filter" class="form-select form-select-sm rounded-pill" onchange="this.form.submit()" style="width: 160px;">
                        <option value="">All Payments</option>
                        <option value="unpaid" <?php echo e($statusFilter === 'unpaid' ? 'selected' : ''); ?>>Unpaid / Pending</option>
                        <option value="paid" <?php echo e($statusFilter === 'paid' ? 'selected' : ''); ?>>Paid Only</option>
                    </select>

                    <div class="input-group input-group-sm" style="width: 240px;">
                        <input type="text" name="search" class="form-control rounded-start-pill" placeholder="Search reference..." value="<?php echo e($search); ?>">
                        <button class="btn btn-outline-secondary rounded-end-pill" type="submit"><i class="bi bi-search"></i></button>
                    </div>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr class="text-uppercase text-secondary small fw-bold">
                            <th>Tracking Ref</th>
                            <th>Applicant</th>
                            <th>Service</th>
                            <th>Amount</th>
                            <th>Gateway / Ref</th>
                            <th>Payment Status</th>
                            <th>Application Status</th>
                            <th class="text-end">Gateway Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $requests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $req): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td>
                                    <span class="fw-bold text-dark font-monospace" style="font-size: 13px;"><?php echo e($req->reference_number); ?></span>
                                    <span class="d-block text-muted" style="font-size: 10.5px;"><?php echo e($req->created_at->format('M d, Y h:i A')); ?></span>
                                </td>
                                <td>
                                    <span class="fw-semibold text-dark d-block"><?php echo e($req->client_name); ?></span>
                                    <span class="text-secondary small"><?php echo e($req->client_email); ?></span>
                                </td>
                                <td>
                                    <span class="fw-semibold text-dark"><?php echo e($req->service_name); ?></span>
                                </td>
                                <td>
                                    <span class="fw-bold text-dark">$<?php echo e(number_format($req->price, 2)); ?></span>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border me-1"><?php echo e($req->payment_gateway ?? 'Credo'); ?></span>
                                    <span class="d-block text-muted font-monospace" style="font-size: 10.5px;"><?php echo e($req->payment_reference ?? 'N/A'); ?></span>
                                </td>
                                <td>
                                    <?php if($req->payment_status === 'Paid'): ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill">
                                            <i class="bi bi-check-circle me-1"></i> Paid
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-1 rounded-pill">
                                            <i class="bi bi-hourglass-split me-1"></i> <?php echo e($req->payment_status ?? 'Unpaid'); ?>

                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-dark text-white px-2 py-1 rounded-pill small"><?php echo e($req->status); ?></span>
                                </td>
                                <td class="text-end">
                                    <?php if($canRequery): ?>
                                        <form action="<?php echo e(route('admin.payment-verification.query')); ?>" method="POST" class="d-inline">
                                            <?php echo csrf_field(); ?>
                                            <input type="hidden" name="reference" value="<?php echo e($req->reference_number); ?>">
                                            <button type="submit" class="btn btn-outline-dark btn-sm rounded-pill px-3" title="Check Gateway API directly for this payment">
                                                <i class="bi bi-arrow-repeat me-1"></i> Re-query Gateway
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span class="badge bg-light text-secondary border px-3 py-1 rounded-pill"><i class="bi bi-eye me-1"></i> Read Only</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary"></i>
                                    No service applications found.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                <?php echo e($requests->links()); ?>

            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\DOOTOR ENTERPRISES\resources\views\admin\payment-verification.blade.php ENDPATH**/ ?>