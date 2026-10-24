<?php $__env->startSection('title', 'Services Catalog - ' . ($settings->platform_name ?? 'DOOTOR ENTERPRISES')); ?>

<?php $__env->startSection('styles'); ?>
<?php echo \Illuminate\View\Factory::parentPlaceholder('styles'); ?>
<style>
    /* Service Catalog Cards Refined Styling - Matching Homepage 1:1 */
    .service-catalog-card {
        border: 1px solid #eef2f6 !important;
        border-radius: 20px !important;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1) !important;
        background: #ffffff !important;
        box-shadow: 0 4px 18px -2px rgba(0, 0, 0, 0.04) !important;
    }
    .service-catalog-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 30px -10px rgba(0, 66, 37, 0.12) !important;
        border-color: rgba(0, 66, 37, 0.25) !important;
    }
    .subservice-chip-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px 14px;
    }
    .subservice-chip {
        background: #ffffff;
        color: #1e293b;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 5px 9px;
        font-size: 11.5px;
        font-weight: 500;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
        display: inline-flex;
        align-items: center;
        gap: 5px;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        text-decoration: none;
    }
    .subservice-chip:hover {
        border-color: #004225 !important;
        color: #004225 !important;
        background-color: #f0fdf4 !important;
        transform: translateY(-1px);
        box-shadow: 0 3px 8px rgba(0, 66, 37, 0.1);
    }
    .subservice-price-pill {
        background: #ecfdf5;
        color: #004225;
        font-weight: 700;
        font-size: 11px;
        padding: 1.5px 6.5px;
        border-radius: 6px;
        margin-left: 2px;
        border: 1px solid rgba(0, 66, 37, 0.12);
        transition: all 0.2s ease;
    }
    .subservice-chip:hover .subservice-price-pill {
        background: #004225;
        color: #ffffff;
        border-color: #004225;
    }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<?php
    if (!function_exists('getServiceCategory')) {
        function getServiceCategory($name) {
            $name = strtolower($name);
            if (str_contains($name, 'nin') || str_contains($name, 'bvn') || str_contains($name, 'licence') || str_contains($name, 'lga') || str_contains($name, 'indigen')) {
                return 'identity';
            }
            if (str_contains($name, 'passport') || str_contains($name, 'travel') || str_contains($name, 'visa') || str_contains($name, 'etc')) {
                return 'travel';
            }
            return 'legal';
        }
    }
?>

<div class="container-fluid px-0">
    <!-- Header Badges -->
    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
        <span class="badge" style="background-color: #e6f4ea; color: #004225; border-radius: 50px; font-weight: 700; font-size: 11px; padding: 6px 14px; border: 1px solid rgba(0, 66, 37, 0.12);">
            <i class="bi bi-shield-check me-1"></i> VERIFIED CONSULAR &amp; DOCUMENT SERVICES
        </span>
        <span class="badge" style="background-color: #ecfdf5; color: #004225; border-radius: 50px; font-weight: 600; font-size: 11px; padding: 6px 14px; border: 1px solid rgba(0, 66, 37, 0.12);">
            <i class="bi bi-credit-card me-1"></i> Billing Currency: <?php echo e($currencyCode ?? 'NGN'); ?>

        </span>
    </div>

    <!-- Catalog Section Title (Exact Match to Homepage) -->
    <div class="text-center max-w-lg mx-auto mb-4 pt-1">
        <span class="text-uppercase small fw-bold tracking-wide" style="color: #004225; letter-spacing: 1.2px;">Catalog</span>
        <h1 class="display-6 fw-bold mt-1 text-dark" style="letter-spacing: -0.02em;">Services We Assist With</h1>
        <p style="color: #334155; font-size: 14.5px;">Explore our catalog of official documentation and certification services with transparent pricing to initiate your request.</p>
    </div>

    <!-- Search and Filter Controls (Exact Match to Homepage) -->
    <div class="row mb-5 justify-content-center">
        <div class="col-md-7 col-lg-6 mb-3 mb-md-0">
            <div class="input-group shadow-sm rounded-pill overflow-hidden border">
                <span class="input-group-text bg-white border-0 ps-3.5"><i class="bi bi-search text-muted"></i></span>
                <input type="text" id="serviceSearch" class="form-control border-0 ps-2 py-2.5 small" placeholder="Search for services or sub-services (e.g. NIN, Visa, Birth)..." style="font-size: 14px;">
            </div>
        </div>
        <div class="col-12 mt-3 d-flex flex-wrap justify-content-center gap-2" id="serviceCategories">
            <button type="button" class="btn btn-dark rounded-pill px-4 btn-sm fw-medium active" data-category="all">All Services</button>
            <button type="button" class="btn btn-outline-dark rounded-pill px-4 btn-sm fw-medium" data-category="identity">Identity &amp; Verifications</button>
            <button type="button" class="btn btn-outline-dark rounded-pill px-4 btn-sm fw-medium" data-category="travel">Travel &amp; Visas</button>
            <button type="button" class="btn btn-outline-dark rounded-pill px-4 btn-sm fw-medium" data-category="legal">Legal &amp; Publications</button>
        </div>
    </div>

    <!-- Services Grid (Exact 4-column responsive grid matching homepage) -->
    <div class="row g-4" id="servicesGrid">
        <?php if($primaryServices->count() > 0): ?>
            <?php $__currentLoopData = $primaryServices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $subServiceNames = $service->subServices->pluck('name')->implode(' ');
                    $searchableName = strtolower($service->name . ' ' . $subServiceNames . ' ' . $service->description);

                    $nameLower = strtolower($service->name);
                    $serviceIcon = match(true) {
                        str_contains($nameLower, 'authorization') || str_contains($nameLower, 'letter') || str_contains($nameLower, 'attorney') => 'bi-file-earmark-text',
                        str_contains($nameLower, 'emergency') || str_contains($nameLower, 'travel') || str_contains($nameLower, 'certificate') => 'bi-airplane',
                        str_contains($nameLower, 'nin') || str_contains($nameLower, 'identification') => 'bi-fingerprint',
                        str_contains($nameLower, 'bvn') => 'bi-bank',
                        str_contains($nameLower, 'passport') => 'bi-passport',
                        str_contains($nameLower, 'visa') => 'bi-ticket-perforated',
                        str_contains($nameLower, 'waiver') || str_contains($nameLower, 'appointment') => 'bi-calendar-check',
                        str_contains($nameLower, 'collection') || str_contains($nameLower, 'same day') => 'bi-clock-history',
                        str_contains($nameLower, 'police') => 'bi-shield-check',
                        str_contains($nameLower, 'newspaper') => 'bi-newspaper',
                        str_contains($nameLower, 'affidavit') => 'bi-journal-check',
                        str_contains($nameLower, 'indigen') || str_contains($nameLower, 'lga') => 'bi-house-check',
                        str_contains($nameLower, 'birth') || str_contains($nameLower, 'npc') => 'bi-file-earmark-medical',
                        default => ($service->icon ?: 'bi-file-earmark-text')
                    };
                ?>
                <div class="col-12 col-sm-6 col-lg-4 col-xl-3 service-card-container" data-category="<?php echo e(getServiceCategory($service->name)); ?>" data-name="<?php echo e($searchableName); ?>">
                    <div class="card service-catalog-card h-100 p-4 d-flex flex-column position-relative overflow-hidden">
                        <!-- Gold Checkmark Badge -->
                        <div class="position-absolute top-0 end-0 mt-3 me-3" title="Verified Consular Service">
                            <i class="bi bi-patch-check-fill fs-5" style="color: #d4af37;"></i>
                        </div>
                        
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="rounded-3 text-white d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width: 44px; height: 44px; background: linear-gradient(135deg, #004225 0%, #0a5c36 100%);">
                                <i class="bi <?php echo e($serviceIcon); ?> fs-5"></i>
                            </div>
                            <div>
                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2.5 py-1 extra-small fw-bold" style="font-size: 10.5px; border: 1px solid rgba(0,66,37,0.15);">Primary Service</span>
                                <h3 class="h5 fw-bold mb-0 text-dark mt-1" style="letter-spacing: -0.3px;"><?php echo e($service->name); ?></h3>
                            </div>
                        </div>

                        <p class="small text-secondary mb-3" style="line-height: 1.6; font-size: 13px;"><?php echo e($service->description); ?></p>

                        <!-- Sub-Services View Section with Direct Request & Pricing -->
                        <?php if($service->subServices && $service->subServices->count() > 0): ?>
                            <div class="subservice-chip-box mb-3 flex-grow-1">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="extra-small fw-bold text-uppercase d-flex align-items-center gap-1.5" style="font-size: 11px; letter-spacing: 0.5px; color: #004225;">
                                        <i class="bi bi-diagram-3-fill text-success"></i> Sub-Services (<?php echo e($service->subServices->count()); ?>)
                                    </span>
                                </div>
                                <div class="d-flex flex-wrap gap-1.5">
                                    <?php $__currentLoopData = $service->subServices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <a href="<?php echo e(route('client.book', $sub->id)); ?>" class="subservice-chip" title="Request <?php echo e($sub->name); ?> - <?php echo e($currencySymbol); ?><?php echo e(number_format($sub->price, 2)); ?>">
                                            <i class="bi bi-check2 text-success fw-bold"></i>
                                            <span><?php echo e($sub->name); ?></span>
                                            <span class="subservice-price-pill"><?php echo e($currencySymbol); ?><?php echo e(number_format($sub->price, 2)); ?></span>
                                        </a>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="flex-grow-1"></div>
                        <?php endif; ?>

                        <!-- Card Footer with Pre-Enrollment Fee & Action Button -->
                        <div class="pt-3 border-top d-flex align-items-center justify-content-between gap-2 mt-auto">
                            <div>
                                <span class="text-muted extra-small d-block" style="font-size: 11px; font-weight: 500;">Official Pre-Enrollment Fee</span>
                                <strong class="text-dark fs-5 fw-bold" style="letter-spacing: -0.5px;"><?php echo e($currencySymbol ?? '$'); ?><?php echo e(number_format($service->price, 2)); ?></strong>
                            </div>
                            <a href="<?php echo e(route('client.book', $service->id)); ?>" class="btn btn-dark btn-sm rounded-pill px-3.5 py-2 font-monospace small d-inline-flex align-items-center gap-1.5 fw-semibold shadow-sm text-decoration-none" style="background-color: #004225; border-color: #004225;">
                                <span>Apply Now</span> <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php else: ?>
            <div class="col-12 text-center py-5 bg-white rounded-4 border">
                <i class="bi bi-box-seam fs-1 text-muted"></i>
                <p class="text-secondary mt-2 small">No services currently available for request.</p>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('serviceSearch');
        const filterButtons = document.querySelectorAll('#serviceCategories button');
        const cardContainers = document.querySelectorAll('.service-card-container');

        let currentCategory = 'all';
        let currentSearch = '';

        function filterServices() {
            let visibleCount = 0;
            cardContainers.forEach(container => {
                const name = container.getAttribute('data-name') || '';
                const category = container.getAttribute('data-category') || '';

                const matchesSearch = !currentSearch || name.includes(currentSearch);
                const matchesCategory = currentCategory === 'all' || category === currentCategory;

                if (matchesSearch && matchesCategory) {
                    container.style.display = 'block';
                    visibleCount++;
                } else {
                    container.style.display = 'none';
                }
            });

            // Handle "No services found" message
            let noResultsMsg = document.getElementById('noServicesFound');
            if (visibleCount === 0) {
                if (!noResultsMsg) {
                    noResultsMsg = document.createElement('div');
                    noResultsMsg.id = 'noServicesFound';
                    noResultsMsg.className = 'col-12 text-center py-5 text-secondary';
                    noResultsMsg.innerHTML = '<i class="bi bi-search fs-1 d-block mb-2"></i><p class="small">No services match your criteria. Please try another search.</p>';
                    document.getElementById('servicesGrid').appendChild(noResultsMsg);
                }
            } else if (noResultsMsg) {
                noResultsMsg.remove();
            }
        }

        if (searchInput) {
            searchInput.addEventListener('input', function (e) {
                currentSearch = e.target.value.toLowerCase().trim();
                filterServices();
            });
        }

        filterButtons.forEach(btn => {
            btn.addEventListener('click', function (e) {
                filterButtons.forEach(b => {
                    b.classList.remove('btn-dark', 'active');
                    b.classList.add('btn-outline-dark');
                });

                this.classList.remove('btn-outline-dark');
                this.classList.add('btn-dark', 'active');

                currentCategory = this.getAttribute('data-category');
                filterServices();
            });
        });
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\DOOTOR ENTERPRISES\resources\views\client\services.blade.php ENDPATH**/ ?>