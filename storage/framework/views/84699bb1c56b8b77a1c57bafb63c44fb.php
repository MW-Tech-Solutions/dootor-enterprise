<?php $__env->startSection('title', $storefront->storefront_name . ' - ' . ($settings->platform_name ?? 'DOOTOR ENTERPRISES')); ?>

<?php $__env->startSection('styles'); ?>
<?php
    // Storefront dynamic styling overrides
    $theme = $storefront->theme ?? 'light';
    $palette = $storefront->color_palette ?? 'default';
    $bgImg = $storefront->background_image_url ?? null;
    $gradient = $storefront->gradient ?? null;
?>
<style>
    /* Theme color variables overrides */
    .storefront-banner {
        <?php if($bgImg): ?>
            background-image: url('<?php echo e($bgImg); ?>');
            background-size: cover;
            background-position: center;
        <?php elseif($gradient): ?>
            background-image: <?php echo $gradient; ?>;
        <?php else: ?>
            background-image: linear-gradient(135deg, #64b3f4, #c2e59c);
        <?php endif; ?>
        min-height: 280px;
        position: relative;
    }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('body'); ?>
<!-- Hero banner -->
<div class="storefront-banner d-flex align-items-end pb-4">
    <div class="position-absolute inset-0 bg-dark opacity-20"></div>
    <div class="container relative-content z-2">
        <div class="d-flex flex-column flex-md-row align-items-center align-items-md-end gap-4 text-white text-center text-md-start">
            <?php if($vendor->avatar_url): ?>
                <img src="<?php echo e(app_file_url($vendor->avatar_url)); ?>" alt="avatar" class="rounded-circle border border-4 border-white shadow-lg" style="width: 120px; height: 120px; object-fit: cover; margin-bottom: -40px;">
            <?php else: ?>
                <div class="rounded-circle bg-white text-dark d-flex align-items-center justify-content-center fw-bold shadow-lg border border-4 border-white" style="width: 120px; height: 120px; font-size: 40px; margin-bottom: -40px;">
                    <?php echo e(strtoupper(substr($vendor->first_name ?? 'V', 0, 1))); ?>

                </div>
            <?php endif; ?>
            
            <div class="mb-2">
                <span class="badge bg-success rounded-pill px-3 py-1 mb-2"><i class="bi bi-patch-check-fill me-1"></i> Verified Agent</span>
                <h1 class="h2 fw-bold text-shadow mb-1"><?php echo e($storefront->storefront_name); ?></h1>
                <p class="mb-0 text-white-50 small"><i class="bi bi-geo-alt me-1"></i> <?php echo e($vendor->state ?? 'Lagos'); ?>, <?php echo e($vendor->country ?? 'Nigeria'); ?></p>
            </div>
        </div>
    </div>
</div>

<div class="container py-5 mt-4">
    <div class="row g-4">
        <!-- About Vendor Sidebar -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm p-4 rounded-4 bg-white mb-4">
                <h2 class="h5 fw-bold mb-3">About Us</h2>
                <p class="text-secondary small mb-4">
                    <?php echo e($storefront->storefront_about ?? 'Welcome to my official service storefront. We offer quick, secure, and authenticated document processing support for personal and corporate services.'); ?>

                </p>
                
                <h2 class="h5 fw-bold mb-3">Contact Information</h2>
                <div class="d-flex flex-column gap-2 text-secondary small">
                    <span class="d-flex align-items-center gap-2"><i class="bi bi-telephone text-dark"></i> <?php echo e($vendor->phone ?? 'Not Provided'); ?></span>
                    <span class="d-flex align-items-center gap-2"><i class="bi bi-envelope text-dark"></i> <?php echo e($vendor->email); ?></span>
                </div>
                
                <hr class="my-4 border-light">
                <a href="/" class="btn btn-outline-dark rounded-pill w-100"><i class="bi bi-arrow-left me-2"></i> Back to Platform</a>
            </div>
        </div>
        
        <!-- Services Catalog -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
                <h2 class="h5 fw-bold mb-4">Offered Services</h2>
                
                <?php if(count($services) > 0): ?>
                    <div class="row g-3">
                        <?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="col-md-6">
                                <div class="card border-0 bg-light p-3 rounded-3 h-100 d-flex flex-column">
                                    <h3 class="h6 fw-bold mb-1"><?php echo e($service->name); ?></h3>
                                    <p class="text-secondary small flex-grow-1 mb-3"><?php echo e($service->description); ?></p>
                                    
                                    <div class="d-flex justify-content-between align-items-center mt-auto">
                                        <span class="fw-bold fs-5 text-dark"><?php echo e($settings->default_currency ?? 'USD'); ?> <?php echo e(number_format($service->price, 2)); ?></span>
                                        <?php if(Auth::check() && Auth::user()->role === 'client'): ?>
                                            <a href="<?php echo e(route('client.book', $service->id)); ?>" class="btn btn-dark btn-sm rounded-pill px-3">Book Service</a>
                                        <?php else: ?>
                                            <a href="<?php echo e(route('login')); ?>" class="btn btn-outline-dark btn-sm rounded-pill px-3">Login to Book</a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="bi bi-box fs-1 text-muted"></i>
                        <p class="text-secondary mt-2 small">No active services listed on this storefront yet.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\DOOTOR ENTERPRISES\resources\views\storefront.blade.php ENDPATH**/ ?>