<?php $__env->startSection('title', 'Services & Sub-Services Catalog - ' . ($settings->platform_name ?? 'DOOTOR ENTERPRISES')); ?>

<?php $__env->startSection('styles'); ?>
<style>
    .service-card {
        border: 1px solid #f1f5f9;
        border-radius: 20px !important;
        background: #ffffff;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.03);
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        position: relative;
        overflow: hidden;
    }
    .service-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 14px 28px -6px rgba(0, 66, 37, 0.12), 0 4px 8px -4px rgba(0, 0, 0, 0.04);
        border-color: #004225;
    }
    .service-icon-box {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        background: #004225;
        color: #d4af37;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        flex-shrink: 0;
        box-shadow: 0 4px 10px rgba(0, 66, 37, 0.15);
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
    .badge-mother-service {
        background-color: #e6f4ea;
        color: #004225;
        font-weight: 600;
        border: 1px solid #b7e1cd;
        border-radius: 9999px;
        padding: 4px 10px;
        font-size: 11px;
    }
    .filter-btn {
        background: #ffffff;
        color: #334155;
        border: 1px solid #e2e8f0 !important;
        font-weight: 500;
        transition: all 0.2s ease;
        white-space: nowrap;
    }
    .filter-btn:hover {
        border-color: #004225 !important;
        color: #004225;
    }
    .filter-btn.active {
        background-color: #004225 !important;
        color: #ffffff !important;
        border-color: #004225 !important;
        font-weight: 600;
    }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-0">
    <!-- Header Section (Exact Match to Image 2) -->
    <div class="mb-3">
        <div class="d-flex align-items-center justify-content-between mb-2 flex-wrap gap-2">
            <span class="badge" style="background-color: #e6f4ea; color: #004225; border-radius: 50px; font-weight: 700; font-size: 10px; padding: 6px 12px;">
                <i class="bi bi-shield-check me-1"></i> VERIFIED CONSULAR &amp; DOCUMENT SERVICES
            </span>
            <span class="badge" style="background-color: #ecfdf5; color: #004225; border-radius: 50px; font-weight: 600; font-size: 10px; padding: 6px 12px;">
                <i class="bi bi-link-45deg me-1"></i> Billing Currency: <?php echo e($currencyCode ?? 'USD'); ?>

            </span>
        </div>
        <h1 class="h3 fw-bold text-dark mb-1" style="font-weight: 800; font-size: 1.55rem; letter-spacing: -0.02em;">Official Services &amp; Sub-Services Catalog</h1>
        <p class="text-secondary small mb-3" style="color: #64748b; font-size: 13px; line-height: 1.45;">Browse verified primary consular services and specialized sub-services to book your application.</p>
    </div>

    <!-- Search Bar (Full Width, Matching Image 2) -->
    <div class="position-relative mb-3">
        <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-secondary" style="font-size: 14px;"></i>
        <input type="text" id="serviceSearchInput" class="form-control rounded-pill ps-5 py-2.5 bg-white border" placeholder="Search services or sub-services..." style="border-color: #e2e8f0; font-size: 13.5px; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
    </div>

    <!-- Filter Buttons (Matching Image 2) -->
    <div class="d-flex align-items-center gap-2 mb-4 overflow-x-auto pb-1" style="scrollbar-width: none;">
        <button type="button" class="btn btn-sm rounded-pill px-3 py-1.5 filter-btn" data-filter="all" style="font-size: 12px;">
            <i class="bi bi-grid me-1"></i> All Services
        </button>
        <button type="button" class="btn btn-sm rounded-pill px-3 py-1.5 filter-btn active" data-filter="primary" style="font-size: 12px;">
            <i class="bi bi-star-fill text-warning me-1"></i> Primary Services (<?php echo e($primaryServices->count()); ?>)
        </button>
        <button type="button" class="btn btn-sm rounded-pill px-3 py-1.5 filter-btn" data-filter="subservice" style="font-size: 12px;">
            <i class="bi bi-diagram-3 me-1"></i> Sub-Services (<?php echo e($subServices->count()); ?>)
        </button>
    </div>

    <!-- Primary Services Section -->
    <div class="mb-5 section-group" id="primaryServicesSection">
        <?php if($primaryServices->count() > 0): ?>
            <div class="row g-3 g-md-4" id="primaryServicesGrid">
                <?php $__currentLoopData = $primaryServices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        // Pick icon matching Image 2
                        $nameLower = strtolower($service->name);
                        $serviceIcon = match(true) {
                            str_contains($nameLower, 'authorization') || str_contains($nameLower, 'letter') || str_contains($nameLower, 'attorney') => 'bi-file-earmark-text',
                            str_contains($nameLower, 'emergency') || str_contains($nameLower, 'travel') || str_contains($nameLower, 'certificate') => 'bi-airplane',
                            str_contains($nameLower, 'nin') || str_contains($nameLower, 'identification') => 'bi-card-heading',
                            str_contains($nameLower, 'passport') => 'bi-book',
                            str_contains($nameLower, 'waiver') || str_contains($nameLower, 'appointment') => 'bi-calendar-check',
                            str_contains($nameLower, 'collection') || str_contains($nameLower, 'same day') => 'bi-clock-history',
                            default => ($service->icon ?? 'bi-box-seam')
                        };
                    ?>
                    <div class="col-12 col-md-6 col-lg-4 service-card-wrapper" data-name="<?php echo e(strtolower($service->name . ' ' . $service->description)); ?>" data-type="primary">
                        <div class="service-card p-3.5 p-md-4 h-100 d-flex flex-column">
                            <div class="d-flex align-items-start gap-3 mb-3">
                                <div class="service-icon-box">
                                    <i class="bi <?php echo e($serviceIcon); ?>"></i>
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <div class="d-flex align-items-center gap-1.5 mb-1.5 flex-wrap">
                                        <span class="badge rounded-pill px-2.5 py-1" style="background-color: #ecfdf5; color: #004225; font-size: 10px; font-weight: 600;">
                                            <i class="bi bi-clock me-1"></i> <?php echo e($service->processing_days ? $service->processing_days . ' Days' : '5 Days'); ?>

                                        </span>
                                        <span class="badge rounded-pill px-2.5 py-1" style="background-color: #fef3c7; color: #92400e; font-size: 10px; font-weight: 600;">
                                            Primary Mother
                                        </span>
                                    </div>
                                    <h3 class="h6 fw-bold text-dark mb-0.5" style="font-weight: 800; font-size: 15px;"><?php echo e($service->name); ?></h3>
                                    <p class="text-secondary small mb-0" style="font-size: 12px; color: #64748b !important;">
                                        <?php echo e($service->short_description ?? Str::limit($service->description, 80)); ?>

                                    </p>
                                </div>
                            </div>

                            <?php if($service->subServices->count() > 0): ?>
                                <div class="p-2.5 rounded-3 mb-3" style="background-color: #f0fdf4; border: 1px solid #dcfce7; border-radius: 12px !important;">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-uppercase fw-bold text-secondary d-flex align-items-center gap-1.5" style="font-size: 10.5px; letter-spacing: 0.03em;">
                                            <i class="bi bi-people text-success"></i> SUB-SERVICES (<?php echo e($service->subServices->count()); ?>)
                                        </span>
                                        <a href="javascript:void(0)" class="text-success small fw-bold text-decoration-none d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#subServiceModal<?php echo e($service->id); ?>" style="font-size: 11px; color: #004225 !important;">
                                            <span>View all</span> <i class="bi bi-arrow-right"></i>
                                        </a>
                                    </div>
                                    <div class="d-flex flex-column gap-1.5">
                                        <?php $__currentLoopData = $service->subServices->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="d-flex justify-content-between align-items-center py-1 border-bottom border-light-subtle" style="font-size: 12px;">
                                                <span class="text-secondary text-truncate me-2"><?php echo e($sub->name); ?></span>
                                                <span class="fw-bold text-dark flex-shrink-0"><?php echo e($currencySymbol ?? '$'); ?><?php echo e(number_format($sub->price, 2)); ?></span>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center pt-2.5 border-top mt-auto" style="border-color: #f1f5f9 !important;">
                                    <div>
                                        <span class="text-secondary d-block" style="font-size: 10px;">Starting From</span>
                                        <span class="fw-bold text-dark fs-5" style="font-weight: 800;"><?php echo e($currencySymbol ?? '$'); ?><?php echo e(number_format($service->price, 2)); ?></span>
                                    </div>
                                    <button class="btn btn-brand-primary rounded-pill px-3.5 py-2 fw-bold d-flex align-items-center gap-1.5" style="font-size: 12.5px;" data-bs-toggle="modal" data-bs-target="#subServiceModal<?php echo e($service->id); ?>">
                                        <span>Choose Option</span>
                                        <i class="bi bi-arrow-right"></i>
                                    </button>
                                </div>
                            <?php else: ?>
                                <div class="pt-2.5 border-top mt-auto" style="border-color: #f1f5f9 !important;">
                                    <div class="mb-2">
                                        <span class="text-secondary d-block" style="font-size: 10px;">Standard Rate</span>
                                        <span class="fw-bold text-dark fs-5" style="font-weight: 800;"><?php echo e($currencySymbol ?? '$'); ?><?php echo e(number_format($service->price, 2)); ?></span>
                                    </div>
                                    <a href="<?php echo e(route('client.book', $service->id)); ?>" class="btn btn-brand-primary w-100 rounded-pill py-2.5 fw-bold d-flex align-items-center justify-content-center gap-1.5" style="font-size: 13px;">
                                        <span>Start Application</span>
                                        <i class="bi bi-arrow-right"></i>
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Modal for Sub-services -->
                    <?php if($service->subServices->count() > 0): ?>
                        <div class="modal fade" id="subServiceModal<?php echo e($service->id); ?>" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content border-0 shadow rounded-4">
                                    <div class="modal-header border-bottom py-3">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="service-icon-box" style="width: 42px; height: 42px; font-size: 18px;">
                                                <i class="bi <?php echo e($service->icon ?? 'bi-box-seam'); ?>"></i>
                                            </div>
                                            <div>
                                                <h5 class="modal-title fw-bold text-dark fs-6"><?php echo e($service->name); ?></h5>
                                                <p class="text-muted small mb-0">Select your specific sub-service requirement or apply for the base service</p>
                                            </div>
                                        </div>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body p-4">
                                        <!-- Base primary option -->
                                        <div class="p-3 bg-light border rounded-3 mb-3 d-flex align-items-center justify-content-between">
                                            <div>
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="badge bg-warning text-dark rounded-pill px-2 py-0.5" style="font-size: 10px;">Primary Package</span>
                                                    <h6 class="fw-bold mb-0 text-dark"><?php echo e($service->name); ?> (Standard Base)</h6>
                                                </div>
                                                <p class="text-secondary small mb-0 mt-1" style="font-size: 12px;"><?php echo e($service->short_description ?? 'Complete primary service processing'); ?></p>
                                            </div>
                                            <div class="text-end ms-3">
                                                <span class="fw-bold text-dark d-block fs-6"><?php echo e($currencySymbol ?? '$'); ?><?php echo e(number_format($service->price, 2)); ?></span>
                                                <a href="<?php echo e(route('client.book', $service->id)); ?>" class="btn btn-outline-dark btn-sm rounded-pill px-3 py-1 mt-1" style="font-size: 12px;">
                                                    Select Base <i class="bi bi-arrow-right"></i>
                                                </a>
                                            </div>
                                        </div>

                                        <h6 class="text-uppercase text-muted fw-bold mb-2 small" style="font-size: 11px; letter-spacing: 0.5px;">
                                            <i class="bi bi-diagram-3-fill text-success me-1"></i> Available Sub-Services (<?php echo e($service->subServices->count()); ?>)
                                        </h6>

                                        <div class="d-flex flex-column gap-2.5">
                                            <?php $__currentLoopData = $service->subServices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <a href="<?php echo e(route('client.book', $sub->id)); ?>" class="p-3 border rounded-3 text-decoration-none text-dark d-flex align-items-center justify-content-between hover-bg-light transition-all" style="background: #ffffff;">
                                                    <div>
                                                        <div class="d-flex align-items-center gap-2">
                                                            <i class="bi bi-arrow-return-right text-success"></i>
                                                            <h6 class="fw-bold mb-0 text-dark"><?php echo e($sub->name); ?></h6>
                                                        </div>
                                                        <p class="text-secondary small mb-0 mt-1 ms-4" style="font-size: 12px;"><?php echo e($sub->short_description ?? 'Official processing & verification'); ?></p>
                                                    </div>
                                                    <div class="text-end ms-3">
                                                        <span class="fw-bold text-success d-block" style="color: #004225 !important;"><?php echo e($currencySymbol ?? '$'); ?><?php echo e(number_format($sub->price, 2)); ?></span>
                                                        <span class="btn btn-outline-success btn-sm py-0.5 px-2.5 mt-1 rounded-pill" style="font-size: 11px; border-color: #004225; color: #004225;">Select <i class="bi bi-arrow-right"></i></span>
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
                <p class="text-secondary mt-2 small">No primary services available at the moment.</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- All Sub-Services Direct Grid Section -->
    <?php if($subServices->count() > 0): ?>
        <div class="mb-5 section-group" id="subServicesSection">
            <div class="d-flex justify-content-between align-items-center mb-3 border-top pt-4">
                <div>
                    <h2 class="h5 fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-diagram-3-fill text-success"></i> All Sub-Services by Mother Service (<?php echo e($subServices->count()); ?>)
                    </h2>
                    <p class="text-secondary small mb-0">Directly select and initiate processing for any specialized sub-service.</p>
                </div>
            </div>

            <div class="row g-3" id="subServicesGrid">
                <?php $__currentLoopData = $subServices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-12 col-md-6 col-lg-4 service-card-wrapper" data-name="<?php echo e(strtolower($sub->name . ' ' . ($sub->parent->name ?? '') . ' ' . $sub->description)); ?>" data-type="subservice">
                        <div class="card border h-100 p-3.5 rounded-3 bg-white shadow-sm d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <?php if($sub->parent): ?>
                                        <span class="badge-mother-service">
                                            <i class="bi bi-star-fill text-warning me-1"></i> Mother: <?php echo e($sub->parent->name); ?>

                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2 py-0.5 small">Sub-Service</span>
                                    <?php endif; ?>
                                    <span class="badge bg-light text-dark border px-2 py-0.5 rounded-pill small">
                                        <?php echo e($sub->processing_days ? $sub->processing_days . ' Days' : 'Fast-track'); ?>

                                    </span>
                                </div>

                                <h3 class="h6 fw-bold text-dark mb-1"><?php echo e($sub->name); ?></h3>
                                <p class="text-secondary small mb-3" style="font-size: 12px; line-height: 1.5;">
                                    <?php echo e($sub->short_description ?? Str::limit($sub->description, 100)); ?>

                                </p>
                            </div>

                            <div class="pt-2.5 border-top d-flex justify-content-between align-items-center mt-auto">
                                <div>
                                    <span class="text-muted d-block" style="font-size: 10px;">Service Fee</span>
                                    <span class="fw-bold text-dark fs-6"><?php echo e($currencySymbol ?? '$'); ?><?php echo e(number_format($sub->price, 2)); ?></span>
                                </div>
                                <a href="<?php echo e(route('client.book', $sub->id)); ?>" class="btn btn-outline-success btn-sm rounded-pill px-3 py-1" style="border-color: #004225; color: #004225;">
                                    Book Now <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const filterBtns = document.querySelectorAll('.filter-btn');
        const searchInput = document.getElementById('serviceSearchInput');
        const cards = document.querySelectorAll('.service-card-wrapper');
        const primarySection = document.getElementById('primaryServicesSection');
        const subSection = document.getElementById('subServicesSection');

        let currentFilter = 'all';
        let currentSearch = '';

        function applyFilters() {
            cards.forEach(card => {
                const cardType = card.getAttribute('data-type');
                const cardName = card.getAttribute('data-name') || '';

                const matchesFilter = (currentFilter === 'all') || (currentFilter === cardType);
                const matchesSearch = !currentSearch || cardName.includes(currentSearch);

                if (matchesFilter && matchesSearch) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });

            if (currentFilter === 'subservice') {
                if (primarySection) primarySection.style.display = 'none';
                if (subSection) subSection.style.display = '';
            } else if (currentFilter === 'primary') {
                if (primarySection) primarySection.style.display = '';
                if (subSection) subSection.style.display = 'none';
            } else {
                if (primarySection) primarySection.style.display = '';
                if (subSection) subSection.style.display = '';
            }
        }

        filterBtns.forEach(btn => {
            btn.addEventListener('click', function () {
                filterBtns.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                currentFilter = this.getAttribute('data-filter');
                applyFilters();
            });
        });

        if (searchInput) {
            searchInput.addEventListener('input', function () {
                currentSearch = this.value.toLowerCase().trim();
                applyFilters();
            });
        }
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\DOOTOR ENTERPRISES\resources\views\client\services.blade.php ENDPATH**/ ?>