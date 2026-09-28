<?php $__env->startSection('title', 'Services Catalog - ' . ($settings->platform_name ?? 'DOOTOR ENTERPRISES')); ?>

<?php $__env->startSection('styles'); ?>
<style>
    .service-card {
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        background: #ffffff;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        position: relative;
        overflow: hidden;
    }
    .service-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px -6px rgba(0, 66, 37, 0.12), 0 4px 8px -4px rgba(0, 0, 0, 0.04);
        border-color: #004225;
    }
    .service-icon-box {
        width: 54px;
        height: 54px;
        border-radius: 14px;
        background: linear-gradient(135deg, #004225 0%, #002411 100%);
        color: #d4af37;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        box-shadow: 0 4px 10px rgba(0, 66, 37, 0.2);
    }
    .btn-brand-primary {
        background-color: #004225 !important;
        border: none !important;
        color: #ffffff !important;
        font-weight: 600;
        transition: all 0.2s ease;
    }
    .btn-brand-primary:hover {
        background-color: #002b18 !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 66, 37, 0.25);
    }
    .subservice-pill {
        background: #f1f5f9;
        color: #334155;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 8px 12px;
        font-size: 13px;
        font-weight: 500;
        transition: all 0.2s ease;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .subservice-pill:hover {
        background: #004225;
        color: #ffffff;
        border-color: #004225;
    }
    .subservice-pill:hover .text-muted {
        color: #d4af37 !important;
    }
    .other-services-wrapper {
        display: none;
        animation: fadeIn 0.4s ease-in-out forwards;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-0">
    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <span class="badge bg-success-subtle text-success px-3 py-1.5 rounded-pill font-monospace small fw-bold text-uppercase mb-2" style="color: #004225 !important; background-color: #e6f4ea !important;">
                <i class="bi bi-shield-check me-1"></i> Verified Consular &amp; Document Services
            </span>
            <h1 class="h3 fw-bold text-dark mb-1">Official Services Catalog</h1>
            <p class="text-secondary small mb-0">Select a primary consular service or sub-service to initiate your background auto-saved application.</p>
        </div>
        <div>
            <span class="badge bg-white border text-dark p-2 px-3 rounded-pill shadow-sm small font-monospace">
                <i class="bi bi-currency-exchange text-success me-1"></i> Billing Currency: <strong><?php echo e($currencyCode ?? 'NGN'); ?></strong>
            </span>
        </div>
    </div>

    <!-- Primary Services Section -->
    <div class="mb-5">
        <h2 class="h5 fw-bold text-dark mb-3 d-flex align-items-center gap-2">
            <i class="bi bi-star-fill text-warning"></i> Primary Services
        </h2>

        <?php if($primaryServices->count() > 0): ?>
            <div class="row g-4">
                <?php $__currentLoopData = $primaryServices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="service-card p-4 h-100 d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="service-icon-box">
                                    <i class="bi <?php echo e($service->icon ?? 'bi-box-seam'); ?>"></i>
                                </div>
                                <span class="badge bg-light text-dark border px-2.5 py-1 rounded-pill small fw-semibold">
                                    <?php echo e($service->processing_days ? $service->processing_days . ' Days' : 'Fast-track'); ?>

                                </span>
                            </div>

                            <h3 class="h5 fw-bold text-dark mb-2"><?php echo e($service->name); ?></h3>
                            <p class="text-secondary small mb-3 flex-grow-1" style="line-height: 1.5;">
                                <?php echo e($service->short_description ?? $service->description); ?>

                            </p>

                            <?php if($service->subServices->count() > 0): ?>
                                <div class="bg-light p-2.5 rounded-3 mb-3 border">
                                    <span class="d-block text-uppercase text-muted fw-bold mb-2" style="font-size: 10px; letter-spacing: 0.5px;">Available Sub-Services (<?php echo e($service->subServices->count()); ?>):</span>
                                    <div class="d-flex flex-column gap-1.5">
                                        <?php $__currentLoopData = $service->subServices->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <a href="<?php echo e(route('client.book', $sub->id)); ?>" class="subservice-pill">
                                                <span><?php echo e($sub->name); ?></span>
                                                <span class="text-muted small fw-bold"><?php echo e($currencySymbol ?? '₦'); ?><?php echo e(number_format($sub->price, 2)); ?></span>
                                            </a>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="pt-3 border-top d-flex justify-content-between align-items-center mt-auto">
                                <div>
                                    <span class="text-muted d-block" style="font-size: 11px;">Starting From</span>
                                    <span class="fw-bold text-dark fs-5"><?php echo e($currencySymbol ?? '₦'); ?><?php echo e(number_format($service->price, 2)); ?></span>
                                </div>

                                <?php if($service->subServices->count() > 0): ?>
                                    <button class="btn btn-brand-primary btn-sm px-3 py-2 rounded-3 d-flex align-items-center gap-1.5" data-bs-toggle="modal" data-bs-target="#subServiceModal<?php echo e($service->id); ?>">
                                        <span>Start Application</span>
                                        <i class="bi bi-chevron-right small"></i>
                                    </button>
                                <?php else: ?>
                                    <a href="<?php echo e(route('client.book', $service->id)); ?>" class="btn btn-brand-primary btn-sm px-3 py-2 rounded-3 d-flex align-items-center gap-1.5">
                                        <span>Start Application</span>
                                        <i class="bi bi-arrow-right small"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Modal for Sub-services -->
                    <?php if($service->subServices->count() > 0): ?>
                        <div class="modal fade" id="subServiceModal<?php echo e($service->id); ?>" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow rounded-4">
                                    <div class="modal-header border-bottom py-3">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="service-icon-box" style="width: 40px; height: 40px; font-size: 18px;">
                                                <i class="bi <?php echo e($service->icon ?? 'bi-box-seam'); ?>"></i>
                                            </div>
                                            <div>
                                                <h5 class="modal-title fw-bold text-dark fs-6"><?php echo e($service->name); ?></h5>
                                                <p class="text-muted small mb-0">Select your specific sub-service requirement</p>
                                            </div>
                                        </div>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body p-4">
                                        <div class="d-flex flex-column gap-2.5">
                                            <?php $__currentLoopData = $service->subServices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <a href="<?php echo e(route('client.book', $sub->id)); ?>" class="p-3 border rounded-3 text-decoration-none text-dark d-flex align-items-center justify-content-between hover-bg-light transition-all">
                                                    <div>
                                                        <h6 class="fw-bold mb-1 text-dark"><?php echo e($sub->name); ?></h6>
                                                        <p class="text-secondary small mb-0" style="font-size: 12px;"><?php echo e($sub->short_description ?? 'Official processing & verification'); ?></p>
                                                    </div>
                                                    <div class="text-end ms-3">
                                                        <span class="fw-bold text-success d-block" style="color: #004225 !important;"><?php echo e($currencySymbol ?? '₦'); ?><?php echo e(number_format($sub->price, 2)); ?></span>
                                                        <span class="btn btn-outline-success btn-sm py-0.5 px-2 mt-1 rounded-pill" style="font-size: 11px; border-color: #004225; color: #004225;">Select <i class="bi bi-arrow-right"></i></span>
                                                    </div>
                                                </a>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php else: ?>
            <div class="text-center py-5 bg-white rounded-4 border">
                <i class="bi bi-box-seam fs-1 text-muted"></i>
                <p class="text-secondary mt-2 small">No primary services found.</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- View Other Services Dynamic Expandable Section -->
    <?php if($otherServices->count() > 0): ?>
        <div class="text-center my-4">
            <button class="btn btn-outline-dark btn-lg px-4 py-2.5 rounded-pill shadow-sm fw-semibold d-inline-flex align-items-center gap-2" id="toggleOtherServicesBtn">
                <i class="bi bi-grid-3x3-gap"></i>
                <span>View Other Services (<?php echo e($otherServices->count()); ?>)</span>
                <i class="bi bi-chevron-down" id="toggleOtherServicesIcon"></i>
            </button>
        </div>

        <div class="other-services-wrapper my-4" id="otherServicesContainer">
            <div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
                <h3 class="h5 fw-bold text-dark mb-4 border-bottom pb-2">
                    <i class="bi bi-grid text-success" style="color: #004225 !important;"></i> Additional &amp; Specialized Services
                </h3>
                <div class="row g-4">
                    <?php $__currentLoopData = $otherServices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="service-card p-4 h-100 d-flex flex-column border">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div class="service-icon-box bg-secondary text-white" style="width: 44px; height: 44px; font-size: 20px;">
                                        <i class="bi <?php echo e($service->icon ?? 'bi-file-earmark-text'); ?>"></i>
                                    </div>
                                    <span class="fw-bold text-dark fs-6"><?php echo e($currencySymbol ?? '₦'); ?><?php echo e(number_format($service->price, 2)); ?></span>
                                </div>
                                <h4 class="h6 fw-bold text-dark mb-2"><?php echo e($service->name); ?></h4>
                                <p class="text-secondary small mb-3 flex-grow-1"><?php echo e($service->short_description ?? $service->description); ?></p>
                                <div class="pt-3 border-top mt-auto text-end">
                                    <a href="<?php echo e(route('client.book', $service->id)); ?>" class="btn btn-outline-success btn-sm rounded-3 px-3" style="border-color: #004225; color: #004225;">
                                        Start Application <i class="bi bi-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggleBtn = document.getElementById('toggleOtherServicesBtn');
        const container = document.getElementById('otherServicesContainer');
        const icon = document.getElementById('toggleOtherServicesIcon');

        if (toggleBtn && container) {
            toggleBtn.addEventListener('click', function () {
                const isHidden = container.style.display === 'none' || container.style.display === '';
                if (isHidden) {
                    container.style.display = 'block';
                    toggleBtn.classList.remove('btn-outline-dark');
                    toggleBtn.classList.add('btn-dark');
                    icon.classList.remove('bi-chevron-down');
                    icon.classList.add('bi-chevron-up');
                } else {
                    container.style.display = 'none';
                    toggleBtn.classList.remove('btn-dark');
                    toggleBtn.classList.add('btn-outline-dark');
                    icon.classList.remove('bi-chevron-up');
                    icon.classList.add('bi-chevron-down');
                }
            });
        }
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\DOOTOR ENTERPRISES\resources\views/client/services.blade.php ENDPATH**/ ?>