<?php $__env->startSection('title', 'Services & Sub-Services - ' . ($settings->platform_name ?? 'Dooter Enterprises')); ?>

<?php $__env->startSection('styles'); ?>
<style>
    .service-parent-card {
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        background: #ffffff;
        transition: all 0.25s ease;
    }
    .service-parent-card:hover {
        border-color: #cbd5e1;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
    }
    .subservice-item {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px 16px;
        transition: all 0.2s ease;
    }
    .subservice-item:hover {
        background: #ffffff;
        border-color: #004225;
        box-shadow: 0 4px 12px rgba(0, 66, 37, 0.08);
    }
    .nav-pills-custom .nav-link {
        color: #64748b;
        font-weight: 600;
        border-radius: 9999px;
        padding: 8px 20px;
        font-size: 14px;
        transition: all 0.2s ease;
    }
    .nav-pills-custom .nav-link.active {
        background-color: #004225 !important;
        color: #ffffff !important;
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
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-0">
    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <h1 class="h3 fw-bold text-dark mb-0">Platform Services &amp; Sub-Services</h1>
                <span class="badge bg-dark rounded-pill px-2.5 py-1 small"><?php echo e($services->count()); ?> Total</span>
            </div>
            <p class="text-secondary small mb-0">Manage primary consular services, attach sub-services, and set mother relationships across the platform.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button class="btn btn-dark btn-sm rounded-pill px-3 py-2 d-flex align-items-center gap-1.5 shadow-sm" type="button" data-bs-toggle="modal" data-bs-target="#createServiceModal">
                <i class="bi bi-plus-circle-fill"></i>
                <span>Add New Service</span>
            </button>
        </div>
    </div>

    <!-- Summary Statistics -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm p-3 rounded-4 bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase d-block mb-1" style="font-size: 11px;">Primary Services</span>
                        <h3 class="fw-bold text-dark mb-0"><?php echo e($primaryServices->count()); ?></h3>
                    </div>
                    <div class="rounded-3 p-2 bg-success-subtle text-success d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; font-size: 20px;">
                        <i class="bi bi-star-fill"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm p-3 rounded-4 bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase d-block mb-1" style="font-size: 11px;">Sub-Services</span>
                        <h3 class="fw-bold text-dark mb-0"><?php echo e($subServices->count()); ?></h3>
                    </div>
                    <div class="rounded-3 p-2 bg-warning-subtle text-warning d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; font-size: 20px;">
                        <i class="bi bi-diagram-3-fill"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm p-3 rounded-4 bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase d-block mb-1" style="font-size: 11px;">Active Services</span>
                        <h3 class="fw-bold text-success mb-0"><?php echo e($services->where('status', 'Active')->count()); ?></h3>
                    </div>
                    <div class="rounded-3 p-2 bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; font-size: 20px;">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm p-3 rounded-4 bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase d-block mb-1" style="font-size: 11px;">Inactive / Draft</span>
                        <h3 class="fw-bold text-secondary mb-0"><?php echo e($services->where('status', 'Inactive')->count()); ?></h3>
                    </div>
                    <div class="rounded-3 p-2 bg-secondary-subtle text-secondary d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; font-size: 20px;">
                        <i class="bi bi-eye-slash-fill"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="card border-0 shadow-sm p-3 rounded-4 bg-white mb-4">
        <ul class="nav nav-pills nav-pills-custom gap-2" id="servicesTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="hierarchical-tab" data-bs-toggle="pill" data-bs-target="#hierarchical-view" type="button" role="tab">
                    <i class="bi bi-diagram-3 me-1"></i> Hierarchical Overview (<?php echo e($primaryServices->count()); ?> Mothers)
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="primary-tab" data-bs-toggle="pill" data-bs-target="#primary-view" type="button" role="tab">
                    <i class="bi bi-star me-1"></i> Primary Services (<?php echo e($primaryServices->count()); ?>)
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="sub-tab" data-bs-toggle="pill" data-bs-target="#sub-view" type="button" role="tab">
                    <i class="bi bi-arrow-return-right me-1"></i> Sub-Services (<?php echo e($subServices->count()); ?>)
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="all-tab" data-bs-toggle="pill" data-bs-target="#all-view" type="button" role="tab">
                    <i class="bi bi-grid me-1"></i> All Catalog (<?php echo e($services->count()); ?>)
                </button>
            </li>
        </ul>
    </div>

    <!-- Tab Content -->
    <div class="tab-content" id="servicesTabContent">
        <!-- 1. Hierarchical Overview Tab -->
        <div class="tab-pane fade show active" id="hierarchical-view" role="tabpanel">
            <?php if($primaryServices->count() > 0): ?>
                <div class="d-flex flex-column gap-4">
                    <?php $__currentLoopData = $primaryServices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $primary): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="service-parent-card p-4">
                            <!-- Primary Service Header Row -->
                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 pb-3 border-bottom">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="rounded-3 p-2 bg-success text-white d-flex align-items-center justify-content-center shadow-sm" style="width: 48px; height: 48px; font-size: 22px; background: #004225 !important;">
                                        <i class="bi bi-star-fill text-warning"></i>
                                    </div>
                                    <div>
                                        <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                                            <h2 class="h5 fw-bold text-dark mb-0"><?php echo e($primary->name); ?></h2>
                                            <?php if($primary->status === 'Active'): ?>
                                                <span class="badge bg-success-subtle text-success rounded-pill px-2.5 py-1">Active</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger-subtle text-danger rounded-pill px-2.5 py-1">Inactive</span>
                                            <?php endif; ?>
                                            <span class="badge bg-warning text-dark rounded-pill px-2.5 py-1"><i class="bi bi-shield-check me-1"></i>Primary (Mother)</span>
                                            <span class="badge bg-light text-muted border rounded-pill px-2.5 py-1"><?php echo e($primary->subServices->count()); ?> Sub-Services</span>
                                            <span class="badge bg-primary-subtle text-primary border rounded-pill px-2.5 py-1"><i class="bi bi-sort-numeric-down me-1"></i>Homepage Priority: #<?php echo e($primary->priority ?? 0); ?></span>
                                        </div>
                                        <p class="text-secondary small mb-0"><?php echo e($primary->short_description ?? Str::limit($primary->description, 120)); ?></p>
                                    </div>
                                </div>
                                <div class="text-md-end d-flex flex-md-column align-items-end justify-content-between gap-1 w-100 w-md-auto">
                                    <span class="fw-bold text-dark fs-5"><?php echo e($currencySymbol ?? '$'); ?><?php echo e(number_format($primary->price, 2)); ?></span>
                                    <span class="text-muted small"><?php echo e($primary->processing_days ? $primary->processing_days . ' processing days' : 'Standard timeline'); ?></span>
                                </div>
                            </div>

                            <!-- Primary Action Buttons Row -->
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 py-3 border-bottom bg-light bg-opacity-50 px-3 rounded-3 my-3">
                                <div class="d-flex flex-wrap gap-1.5">
                                    <!-- Builder Tools -->
                                    <a href="<?php echo e(route('admin.service.form-builder', $primary->id)); ?>" class="btn btn-outline-success btn-sm rounded-pill">
                                        <i class="bi bi-ui-checks me-1"></i> Form Builder (<?php echo e($primary->fields->count()); ?>)
                                    </a>
                                    <a href="<?php echo e(route('admin.service.workflow', $primary->id)); ?>" class="btn btn-outline-primary btn-sm rounded-pill">
                                        <i class="bi bi-diagram-3 me-1"></i> Workflows (<?php echo e($primary->workflowStages->count()); ?>)
                                    </a>
                                </div>
                                <div class="d-flex flex-wrap gap-1.5 align-items-center">
                                    <!-- BUTTON: Make Sub-Service (Core User Request) -->
                                    <button class="btn btn-outline-warning text-dark btn-sm rounded-pill px-3 fw-semibold" type="button" data-bs-toggle="modal" data-bs-target="#makeSubServiceModal-<?php echo e($primary->id); ?>">
                                        <i class="bi bi-diagram-2 me-1"></i> Make Sub-Service
                                    </button>

                                    <!-- Edit Trigger -->
                                    <button class="btn btn-outline-dark btn-sm rounded-pill px-3" type="button" data-bs-toggle="modal" data-bs-target="#editServiceModal-<?php echo e($primary->id); ?>">
                                        <i class="bi bi-pencil me-1"></i> Edit
                                    </button>

                                    <!-- Delete Trigger -->
                                    <form action="<?php echo e(route('admin.service.delete', $primary->id)); ?>" method="POST" class="d-inline m-0" onsubmit="return confirm('Permanently delete <?php echo e($primary->name); ?>? Sub-services under this mother will be detached.');">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </div>

                            <!-- Nested Sub-Services Section -->
                            <div class="mt-3">
                                <div class="d-flex justify-content-between align-items-center mb-2.5">
                                    <h3 class="h6 fw-bold text-dark mb-0 d-flex align-items-center gap-1.5">
                                        <i class="bi bi-arrow-return-right text-success"></i> Sub-Services under <?php echo e($primary->name); ?> (<?php echo e($primary->subServices->count()); ?>)
                                    </h3>
                                    <button class="btn btn-outline-success btn-sm rounded-pill py-0.5 px-2.5" style="font-size: 12px;" type="button" data-bs-toggle="modal" data-bs-target="#createServiceModal" onclick="document.getElementById('create-parent_id').value='<?php echo e($primary->id); ?>';">
                                        <i class="bi bi-plus me-1"></i> Add Sub-Service to <?php echo e($primary->name); ?>

                                    </button>
                                </div>

                                <?php if($primary->subServices->count() > 0): ?>
                                    <div class="row g-2.5">
                                        <?php $__currentLoopData = $primary->subServices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="col-12 col-lg-6">
                                                <div class="subservice-item d-flex flex-column justify-content-between h-100">
                                                    <div>
                                                        <div class="d-flex justify-content-between align-items-start mb-1">
                                                            <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                                                <span class="badge bg-white border text-dark rounded-pill px-2 py-0.5 small" style="font-size: 11px;">#<?php echo e($sub->id); ?></span>
                                                                <h4 class="h6 fw-bold mb-0 text-dark"><?php echo e($sub->name); ?></h4>
                                                                <?php if($sub->status === 'Active'): ?>
                                                                    <span class="badge bg-success-subtle text-success rounded-pill px-2 py-0.5" style="font-size: 10px;">Active</span>
                                                                <?php else: ?>
                                                                    <span class="badge bg-danger-subtle text-danger rounded-pill px-2 py-0.5" style="font-size: 10px;">Inactive</span>
                                                                <?php endif; ?>
                                                            </div>
                                                            <span class="fw-bold text-dark small"><?php echo e($currencySymbol ?? '$'); ?><?php echo e(number_format($sub->price, 2)); ?></span>
                                                        </div>
                                                        <p class="text-secondary small mb-2" style="font-size: 12px;"><?php echo e($sub->short_description ?? Str::limit($sub->description, 90)); ?></p>
                                                    </div>

                                                    <div class="d-flex justify-content-between align-items-center pt-2 border-top mt-2">
                                                        <div class="d-flex gap-1">
                                                            <a href="<?php echo e(route('admin.service.form-builder', $sub->id)); ?>" class="btn btn-link btn-sm p-0 text-success text-decoration-none small" style="font-size: 11px;">
                                                                <i class="bi bi-ui-checks me-0.5"></i> Form
                                                            </a>
                                                            <span class="text-muted">·</span>
                                                            <a href="<?php echo e(route('admin.service.workflow', $sub->id)); ?>" class="btn btn-link btn-sm p-0 text-primary text-decoration-none small" style="font-size: 11px;">
                                                                <i class="bi bi-diagram-3 me-0.5"></i> Workflow
                                                            </a>
                                                        </div>
                                                        <div class="d-flex align-items-center gap-1">
                                                            <!-- Change Mother Service Button -->
                                                            <button class="btn btn-outline-primary btn-sm py-0.5 px-2 rounded-pill" style="font-size: 11px;" type="button" data-bs-toggle="modal" data-bs-target="#makeSubServiceModal-<?php echo e($sub->id); ?>" title="Change Mother Service">
                                                                <i class="bi bi-arrow-left-right me-0.5"></i> Mother
                                                            </button>

                                                            <!-- Convert to Primary Service Form -->
                                                            <form action="<?php echo e(route('admin.service.make-primary', $sub->id)); ?>" method="POST" class="d-inline m-0" onsubmit="return confirm('Make <?php echo e($sub->name); ?> an independent Primary Service?');">
                                                                <?php echo csrf_field(); ?>
                                                                <button type="submit" class="btn btn-outline-warning text-dark btn-sm py-0.5 px-2 rounded-pill" style="font-size: 11px;" title="Convert to Primary Service">
                                                                    <i class="bi bi-star me-0.5"></i> Primary
                                                                </button>
                                                            </form>

                                                            <!-- Edit Modal Trigger -->
                                                            <button class="btn btn-outline-dark btn-sm py-0.5 px-2 rounded-pill" style="font-size: 11px;" type="button" data-bs-toggle="modal" data-bs-target="#editServiceModal-<?php echo e($sub->id); ?>">
                                                                Edit
                                                            </button>

                                                            <!-- Delete -->
                                                            <form action="<?php echo e(route('admin.service.delete', $sub->id)); ?>" method="POST" class="d-inline m-0" onsubmit="return confirm('Delete this sub-service?');">
                                                                <?php echo csrf_field(); ?>
                                                                <?php echo method_field('DELETE'); ?>
                                                                <button type="submit" class="btn btn-outline-danger btn-sm py-0.5 px-1.5 rounded-pill" style="font-size: 11px;"><i class="bi bi-trash"></i></button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                <?php else: ?>
                                    <div class="p-3 bg-light rounded-3 text-center border border-dashed">
                                        <p class="text-secondary small mb-1">No sub-services attached to <?php echo e($primary->name); ?> yet.</p>
                                        <button class="btn btn-sm btn-outline-success rounded-pill px-3" type="button" data-bs-toggle="modal" data-bs-target="#createServiceModal" onclick="document.getElementById('create-parent_id').value='<?php echo e($primary->id); ?>';">
                                            <i class="bi bi-plus-circle me-1"></i> Add First Sub-Service
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php else: ?>
                <div class="card border-0 shadow-sm p-5 rounded-4 bg-white text-center">
                    <i class="bi bi-box-seam fs-1 text-muted"></i>
                    <h2 class="h5 fw-bold text-dark mt-3 mb-1">No Primary Services Defined</h2>
                    <p class="text-secondary small mb-3">Get started by creating your first primary service.</p>
                    <div>
                        <button class="btn btn-dark rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#createServiceModal">Add Primary Service</button>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- 2. Primary Services Tab -->
        <div class="tab-pane fade" id="primary-view" role="tabpanel">
            <div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="h5 fw-bold text-dark mb-0">All Mother Primary Services (<?php echo e($primaryServices->count()); ?>)</h2>
                    <button class="btn btn-dark btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#createServiceModal">
                        <i class="bi bi-plus-circle me-1"></i> Add Primary Service
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Primary Service Name</th>
                                <th>Sub-Services Count</th>
                                <th>Default Price</th>
                                <th>Processing Time</th>
                                <th>Status</th>
                                <th class="text-end">Actions &amp; Relationship</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $primaryServices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi bi-star-fill text-warning"></i>
                                            <div>
                                                <strong class="text-dark d-block"><?php echo e($p->name); ?></strong>
                                                <span class="text-muted small">ID: #<?php echo e($p->id); ?></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border rounded-pill px-2.5 py-1">
                                            <?php echo e($p->subServices->count()); ?> Sub-Services
                                        </span>
                                        <span class="badge bg-primary-subtle text-primary border rounded-pill px-2 py-0.5 ms-1" style="font-size: 11px;">
                                            #<?php echo e($p->priority ?? 0); ?>

                                        </span>
                                    </td>
                                    <td class="fw-bold"><?php echo e($currencySymbol ?? '$'); ?><?php echo e(number_format($p->price, 2)); ?></td>
                                    <td><?php echo e($p->processing_days ? $p->processing_days . ' days' : 'N/A'); ?></td>
                                    <td>
                                        <?php if($p->status === 'Active'): ?>
                                            <span class="badge bg-success-subtle text-success rounded-pill px-2.5 py-1">Active</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger-subtle text-danger rounded-pill px-2.5 py-1">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <div class="d-inline-flex gap-1">
                                            <!-- Make Sub Service -->
                                            <button class="btn btn-outline-warning text-dark btn-sm rounded-pill px-2.5" data-bs-toggle="modal" data-bs-target="#makeSubServiceModal-<?php echo e($p->id); ?>" title="Make Sub-Service">
                                                <i class="bi bi-diagram-2 me-1"></i> Make Sub-Service
                                            </button>
                                            <a href="<?php echo e(route('admin.service.form-builder', $p->id)); ?>" class="btn btn-outline-success btn-sm rounded-pill" title="Form Builder">
                                                <i class="bi bi-ui-checks"></i>
                                            </a>
                                            <a href="<?php echo e(route('admin.service.workflow', $p->id)); ?>" class="btn btn-outline-primary btn-sm rounded-pill" title="Workflows">
                                                <i class="bi bi-diagram-3"></i>
                                            </a>
                                            <button class="btn btn-outline-dark btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#editServiceModal-<?php echo e($p->id); ?>">
                                                Edit
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">No primary services found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- 3. Sub-Services Tab -->
        <div class="tab-pane fade" id="sub-view" role="tabpanel">
            <div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="h5 fw-bold text-dark mb-0">All Configured Sub-Services (<?php echo e($subServices->count()); ?>)</h2>
                        <p class="text-secondary small mb-0">Sub-services inherit workflow structures or have independent processing paths under their mother primary service.</p>
                    </div>
                    <button class="btn btn-dark btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#createServiceModal">
                        <i class="bi bi-plus-circle me-1"></i> Add Sub-Service
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Sub-Service Name</th>
                                <th>Mother Primary Service</th>
                                <th>Default Price</th>
                                <th>Processing Time</th>
                                <th>Status</th>
                                <th class="text-end">Actions &amp; Relationship</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $subServices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi bi-arrow-return-right text-success"></i>
                                            <div>
                                                <strong class="text-dark d-block"><?php echo e($s->name); ?></strong>
                                                <span class="text-muted small">ID: #<?php echo e($s->id); ?></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if($s->parent): ?>
                                            <span class="badge-mother-service">
                                                <i class="bi bi-star-fill text-warning me-1"></i> <?php echo e($s->parent->name); ?>

                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary text-white rounded-pill px-2.5 py-1">Unassigned</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="fw-bold"><?php echo e($currencySymbol ?? '$'); ?><?php echo e(number_format($s->price, 2)); ?></td>
                                    <td><?php echo e($s->processing_days ? $s->processing_days . ' days' : 'N/A'); ?></td>
                                    <td>
                                        <?php if($s->status === 'Active'): ?>
                                            <span class="badge bg-success-subtle text-success rounded-pill px-2.5 py-1">Active</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger-subtle text-danger rounded-pill px-2.5 py-1">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <div class="d-inline-flex gap-1">
                                            <!-- Change Mother Service Button -->
                                            <button class="btn btn-outline-primary btn-sm rounded-pill px-2.5" data-bs-toggle="modal" data-bs-target="#makeSubServiceModal-<?php echo e($s->id); ?>" title="Change Mother Service">
                                                <i class="bi bi-arrow-left-right me-1"></i> Change Mother
                                            </button>

                                            <!-- Convert to Primary Service Form -->
                                            <form action="<?php echo e(route('admin.service.make-primary', $s->id)); ?>" method="POST" class="d-inline m-0" onsubmit="return confirm('Convert <?php echo e($s->name); ?> into an independent Primary Service?');">
                                                <?php echo csrf_field(); ?>
                                                <button type="submit" class="btn btn-outline-warning text-dark btn-sm rounded-pill px-2.5" title="Make Primary Service">
                                                    <i class="bi bi-star me-1"></i> Make Primary
                                                </button>
                                            </form>

                                            <a href="<?php echo e(route('admin.service.form-builder', $s->id)); ?>" class="btn btn-outline-success btn-sm rounded-pill" title="Form Builder">
                                                <i class="bi bi-ui-checks"></i>
                                            </a>
                                            <a href="<?php echo e(route('admin.service.workflow', $s->id)); ?>" class="btn btn-outline-primary btn-sm rounded-pill" title="Workflows">
                                                <i class="bi bi-diagram-3"></i>
                                            </a>
                                            <button class="btn btn-outline-dark btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#editServiceModal-<?php echo e($s->id); ?>">
                                                Edit
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">No sub-services configured yet. Click "Make Sub-Service" on any service to assign it to a mother service.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- 4. All Catalog Tab -->
        <div class="tab-pane fade" id="all-view" role="tabpanel">
            <div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
                <div class="row g-4">
                    <?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="card border border-light h-100 p-3.5 rounded-3 bg-light d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="d-flex align-items-center gap-1 flex-wrap">
                                        <?php if($service->status === 'Active'): ?>
                                            <span class="badge bg-success-subtle text-success rounded-pill px-2.5 py-1">Active</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger-subtle text-danger rounded-pill px-2.5 py-1">Inactive</span>
                                        <?php endif; ?>

                                        <?php if(!$service->parent_id): ?>
                                            <span class="badge bg-warning text-dark rounded-pill px-2.5 py-1"><i class="bi bi-star-fill me-1"></i>Primary</span>
                                        <?php else: ?>
                                            <span class="badge-mother-service"><i class="bi bi-arrow-return-right me-1"></i><?php echo e($service->parent->name ?? 'Sub-service'); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <span class="fw-bold text-dark"><?php echo e($currencySymbol ?? '$'); ?><?php echo e(number_format($service->price, 2)); ?></span>
                                </div>
                                
                                <h3 class="h6 fw-bold mb-1"><?php echo e($service->name); ?></h3>
                                <p class="text-secondary small mb-3 flex-grow-1"><?php echo e($service->short_description ?? Str::limit($service->description, 100)); ?></p>
                                
                                <div class="d-flex flex-wrap gap-1 mb-3">
                                    <!-- BUTTON: Make Sub-Service / Change Mother -->
                                    <button class="btn btn-outline-warning text-dark btn-sm rounded-pill" type="button" data-bs-toggle="modal" data-bs-target="#makeSubServiceModal-<?php echo e($service->id); ?>">
                                        <i class="bi bi-diagram-2 me-1"></i> <?php echo e($service->parent_id ? 'Change Mother' : 'Make Sub-Service'); ?>

                                    </button>

                                    <?php if($service->parent_id): ?>
                                        <form action="<?php echo e(route('admin.service.make-primary', $service->id)); ?>" method="POST" class="d-inline m-0" onsubmit="return confirm('Convert to Primary Service?');">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" class="btn btn-outline-success btn-sm rounded-pill"><i class="bi bi-star me-1"></i> Make Primary</button>
                                        </form>
                                    <?php endif; ?>

                                    <a href="<?php echo e(route('admin.service.form-builder', $service->id)); ?>" class="btn btn-outline-success btn-sm rounded-pill">
                                        <i class="bi bi-ui-checks me-1"></i> Form Builder
                                    </a>
                                    <a href="<?php echo e(route('admin.service.workflow', $service->id)); ?>" class="btn btn-outline-primary btn-sm rounded-pill">
                                        <i class="bi bi-diagram-3 me-1"></i> Workflows
                                    </a>
                                </div>
                                
                                <hr class="my-2 border-light">
                                
                                <div class="d-flex justify-content-between align-items-center mt-auto">
                                    <span class="text-muted small">ID: #<?php echo e($service->id); ?></span>
                                    <div class="d-flex gap-1">
                                        <button class="btn btn-outline-dark btn-sm rounded-pill px-3" type="button" data-bs-toggle="modal" data-bs-target="#editServiceModal-<?php echo e($service->id); ?>">Edit</button>
                                        <form action="<?php echo e(route('admin.service.delete', $service->id)); ?>" method="POST" class="m-0" onsubmit="return confirm('Delete this global platform service permanently?');">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- ALL MODALS: Make Sub-Service, Edit, Create -->
<!-- ========================================== -->

<?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <!-- Modal: Make Sub-Service (Asks: Which primary service is the mother of the sub service?) -->
    <div class="modal fade" id="makeSubServiceModal-<?php echo e($service->id); ?>" tabindex="-1" aria-labelledby="makeSubServiceModalLabel-<?php echo e($service->id); ?>" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4">
                <div class="modal-header border-bottom py-3 bg-light">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle p-2 bg-warning-subtle text-warning d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                            <i class="bi bi-diagram-3-fill"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-dark fs-6" id="makeSubServiceModalLabel-<?php echo e($service->id); ?>">
                                <?php echo e($service->parent_id ? 'Change Mother Service' : 'Make Sub-Service'); ?>

                            </h5>
                            <p class="text-muted small mb-0"><?php echo e($service->name); ?> (ID: #<?php echo e($service->id); ?>)</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?php echo e(route('admin.service.make-sub-service', $service->id)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="modal-body p-4">
                        <div class="alert alert-warning border-warning border-opacity-25 rounded-3 small mb-3">
                            <i class="bi bi-exclamation-triangle-fill text-warning me-1"></i>
                            You are configuring <strong><?php echo e($service->name); ?></strong> as a sub-service.
                        </div>

                        <!-- THE REQUIRED QUESTION -->
                        <div class="mb-3">
                            <label for="parent_id_<?php echo e($service->id); ?>" class="form-label fw-bold text-dark fs-6 mb-2">
                                <i class="bi bi-diagram-3-fill text-warning me-1"></i> Which primary service is the mother of the sub service?
                            </label>
                            <select name="parent_id" id="parent_id_<?php echo e($service->id); ?>" class="form-select form-select-lg rounded-3 border-2" required>
                                <option value="" disabled <?php echo e(!$service->parent_id ? 'selected' : ''); ?>>-- Select Primary Mother Service --</option>
                                <?php $__currentLoopData = $allPrimaryServices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mother): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if($mother->id !== $service->id): ?>
                                        <option value="<?php echo e($mother->id); ?>" <?php echo e($service->parent_id == $mother->id ? 'selected' : ''); ?>>
                                            ★ <?php echo e($mother->name); ?> (ID: #<?php echo e($mother->id); ?>)
                                        </option>
                                    <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <small class="text-muted mt-2 d-block">
                                <i class="bi bi-info-circle me-1"></i> This sub-service will be listed directly under the chosen primary mother service on the client and vendor portals.
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer border-top py-2.5">
                        <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-dark rounded-pill px-4">
                            <i class="bi bi-check-circle me-1"></i> Save as Sub-Service
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Global Service Modal -->
    <div class="modal fade" id="editServiceModal-<?php echo e($service->id); ?>" tabindex="-1" aria-labelledby="editServiceModalLabel-<?php echo e($service->id); ?>" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow rounded-4">
                <div class="modal-header border-bottom py-3">
                    <h5 class="modal-title fw-bold" id="editServiceModalLabel-<?php echo e($service->id); ?>">Edit Platform Service: <?php echo e($service->name); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?php echo e(route('admin.service.update', $service->id)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PATCH'); ?>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-12 col-md-8">
                                <label for="edit-name-<?php echo e($service->id); ?>" class="form-label small fw-medium">Service Name</label>
                                <input type="text" name="name" id="edit-name-<?php echo e($service->id); ?>" class="form-control rounded-3" value="<?php echo e(old('name', $service->name)); ?>" required>
                            </div>
                            <div class="col-12 col-md-4">
                                <label for="edit-status-<?php echo e($service->id); ?>" class="form-label small fw-medium">Platform Status</label>
                                <select name="status" id="edit-status-<?php echo e($service->id); ?>" class="form-select rounded-3">
                                    <option value="Active" <?php echo e($service->status === 'Active' ? 'selected' : ''); ?>>Active</option>
                                    <option value="Inactive" <?php echo e($service->status === 'Inactive' ? 'selected' : ''); ?>>Inactive</option>
                                </select>
                            </div>

                            <!-- Mother Service Dropdown in Edit Modal -->
                            <div class="col-12">
                                <label for="edit-parent-<?php echo e($service->id); ?>" class="form-label small fw-bold text-dark">
                                    <i class="bi bi-diagram-3 me-1 text-warning"></i> Mother Primary Service (Leave empty if this is an independent Primary Service)
                                </label>
                                <select name="parent_id" id="edit-parent-<?php echo e($service->id); ?>" class="form-select rounded-3">
                                    <option value="">-- None (Independent Primary Service) --</option>
                                    <?php $__currentLoopData = $allPrimaryServices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php if($p->id !== $service->id): ?>
                                            <option value="<?php echo e($p->id); ?>" <?php echo e($service->parent_id == $p->id ? 'selected' : ''); ?>>
                                                ★ Mother: <?php echo e($p->name); ?>

                                            </option>
                                        <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>

                            <div class="col-12 col-md-4">
                                <label for="edit-price-<?php echo e($service->id); ?>" class="form-label small fw-medium">Base Default Price (<?php echo e($currencyCode ?? 'USD'); ?>)</label>
                                <input type="number" name="price" id="edit-price-<?php echo e($service->id); ?>" step="0.01" min="0" class="form-control rounded-3" value="<?php echo e(old('price', $service->price)); ?>" required>
                            </div>
                            <div class="col-12 col-md-4">
                                <label for="edit-priority-<?php echo e($service->id); ?>" class="form-label small fw-bold text-primary">
                                    <i class="bi bi-sort-numeric-down me-1"></i> Homepage Priority
                                </label>
                                <input type="number" name="priority" id="edit-priority-<?php echo e($service->id); ?>" min="0" class="form-control rounded-3" value="<?php echo e(old('priority', $service->priority ?? 0)); ?>" placeholder="e.g. 1 (Top)">
                                <small class="text-muted" style="font-size: 11px;">1 = highest first, 0 = normal</small>
                            </div>
                            <div class="col-12 col-md-4">
                                <label for="edit-days-<?php echo e($service->id); ?>" class="form-label small fw-medium">Estimated Processing Days</label>
                                <input type="number" name="processing_days" id="edit-days-<?php echo e($service->id); ?>" min="1" class="form-control rounded-3" value="<?php echo e(old('processing_days', $service->processing_days ?? 3)); ?>">
                            </div>

                            <div class="col-12">
                                <label for="edit-short-<?php echo e($service->id); ?>" class="form-label small fw-medium">Short Headline / Summary</label>
                                <input type="text" name="short_description" id="edit-short-<?php echo e($service->id); ?>" class="form-control rounded-3" value="<?php echo e(old('short_description', $service->short_description)); ?>" placeholder="Brief 1-sentence highlight">
                            </div>

                            <div class="col-12">
                                <label for="edit-description-<?php echo e($service->id); ?>" class="form-label small fw-medium">Full Description</label>
                                <textarea name="description" id="edit-description-<?php echo e($service->id); ?>" class="form-control rounded-3" rows="3" required><?php echo e(old('description', $service->description)); ?></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top py-2.5">
                        <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-dark rounded-pill px-4">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<!-- Create Global Service Modal -->
<div class="modal fade" id="createServiceModal" tabindex="-1" aria-labelledby="createServiceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-bottom py-3">
                <h5 class="modal-title fw-bold" id="createServiceModalLabel">Add Platform Service / Sub-Service</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo e(route('admin.services.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12 col-md-8">
                            <label for="create-name" class="form-label small fw-medium">Service Name</label>
                            <input type="text" name="name" id="create-name" class="form-control rounded-3" placeholder="e.g. Birth Certificate Attestation" required>
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="create-status" class="form-label small fw-medium">Platform Status</label>
                            <select name="status" id="create-status" class="form-select rounded-3">
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>

                        <!-- Which primary service is the mother of the sub service? -->
                        <div class="col-12">
                            <label for="create-parent_id" class="form-label small fw-bold text-dark">
                                <i class="bi bi-diagram-3-fill text-warning me-1"></i> Which primary service is the mother of the sub service?
                            </label>
                            <select name="parent_id" id="create-parent_id" class="form-select rounded-3">
                                <option value="">-- None (Create as Standalone Primary Service) --</option>
                                <?php $__currentLoopData = $allPrimaryServices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($p->id); ?>">★ Mother: <?php echo e($p->name); ?> (ID: #<?php echo e($p->id); ?>)</option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <small class="text-muted d-block mt-1">If selected, this becomes a sub-service grouped under the chosen primary mother service.</small>
                        </div>

                        <div class="col-12 col-md-4">
                            <label for="create-price" class="form-label small fw-medium">Base Default Price (<?php echo e($currencyCode ?? 'USD'); ?>)</label>
                            <input type="number" name="price" id="create-price" step="0.01" min="0" class="form-control rounded-3" value="0.00" required>
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="create-priority" class="form-label small fw-bold text-primary">
                                <i class="bi bi-sort-numeric-down me-1"></i> Homepage Priority
                            </label>
                            <input type="number" name="priority" id="create-priority" min="0" class="form-control rounded-3" value="0" placeholder="e.g. 1 (Top)">
                            <small class="text-muted" style="font-size: 11px;">1 = highest first, 0 = normal</small>
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="create-days" class="form-label small fw-medium">Estimated Processing Days</label>
                            <input type="number" name="processing_days" id="create-days" min="1" class="form-control rounded-3" value="3">
                        </div>

                        <div class="col-12">
                            <label for="create-short" class="form-label small fw-medium">Short Headline / Summary</label>
                            <input type="text" name="short_description" id="create-short" class="form-control rounded-3" placeholder="Brief 1-sentence highlight">
                        </div>

                        <div class="col-12">
                            <label for="create-description" class="form-label small fw-medium">Full Description</label>
                            <textarea name="description" id="create-description" class="form-control rounded-3" rows="3" placeholder="Describe the document processing details..." required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top py-2.5">
                    <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-dark rounded-pill px-4">Create Service</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\DOOTOR ENTERPRISES\resources\views\admin\services.blade.php ENDPATH**/ ?>