<?php $__env->startSection('title', 'Service Form Builder - ' . $service->name); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="<?php echo e(route('admin.services')); ?>" class="text-decoration-none text-success">Services</a></li>
                <li class="breadcrumb-item active" aria-current="page">Form Builder</li>
            </ol>
        </nav>
        <h1 class="h3 fw-bold text-dark mb-0">Form Builder: <?php echo e($service->name); ?></h1>
        <p class="text-secondary small mb-0">Configure custom dynamic input fields, uploads, and questions required for online applications.</p>
    </div>
    <a href="<?php echo e(route('admin.services')); ?>" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back to Services
    </a>
</div>

<?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i><?php echo e(session('success')); ?>

        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<form action="<?php echo e(route('admin.service.form-builder.save', $service)); ?>" method="POST">
    <?php echo csrf_field(); ?>

    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
            <h5 class="card-title fw-bold mb-0 text-dark fs-6">Configured Fields (<?php echo e($fields->count()); ?>)</h5>
            <button type="button" class="btn btn-success btn-sm" id="addFieldBtn" style="background-color: #004225; border: none;">
                <i class="bi bi-plus-lg me-1"></i> Add Custom Field
            </button>
        </div>
        <div class="card-body p-0">
            <div id="fieldsContainer">
                <?php $__empty_1 = true; $__currentLoopData = $fields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="field-row p-3 border-bottom position-relative bg-light bg-opacity-50">
                        <input type="hidden" name="fields[<?php echo e($index); ?>][id]" value="<?php echo e($field->id); ?>">
                        
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Field Label *</label>
                                <input type="text" name="fields[<?php echo e($index); ?>][field_label]" class="form-control form-control-sm bg-white" value="<?php echo e($field->field_label); ?>" required placeholder="e.g. Passport Number">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Field Type *</label>
                                <select name="fields[<?php echo e($index); ?>][field_type]" class="form-select form-select-sm bg-white field-type-select">
                                    <option value="text" <?php echo e($field->field_type == 'text' ? 'selected' : ''); ?>>Text Input</option>
                                    <option value="textarea" <?php echo e($field->field_type == 'textarea' ? 'selected' : ''); ?>>Text Area</option>
                                    <option value="number" <?php echo e($field->field_type == 'number' ? 'selected' : ''); ?>>Number</option>
                                    <option value="email" <?php echo e($field->field_type == 'email' ? 'selected' : ''); ?>>Email</option>
                                    <option value="phone" <?php echo e($field->field_type == 'phone' ? 'selected' : ''); ?>>Phone</option>
                                    <option value="date" <?php echo e($field->field_type == 'date' ? 'selected' : ''); ?>>Date</option>
                                    <option value="dropdown" <?php echo e($field->field_type == 'dropdown' ? 'selected' : ''); ?>>Dropdown Select</option>
                                    <option value="radio" <?php echo e($field->field_type == 'radio' ? 'selected' : ''); ?>>Radio Buttons</option>
                                    <option value="checkbox" <?php echo e($field->field_type == 'checkbox' ? 'selected' : ''); ?>>Checkbox</option>
                                    <option value="yes_no" <?php echo e($field->field_type == 'yes_no' ? 'selected' : ''); ?>>Yes / No Choice</option>
                                    <option value="file" <?php echo e($field->field_type == 'file' ? 'selected' : ''); ?>>File Upload</option>
                                    <option value="passport" <?php echo e($field->field_type == 'passport' ? 'selected' : ''); ?>>Passport Photograph Upload</option>
                                    <option value="address" <?php echo e($field->field_type == 'address' ? 'selected' : ''); ?>>Full Address</option>
                                    <option value="country" <?php echo e($field->field_type == 'country' ? 'selected' : ''); ?>>African Country Dropdown</option>
                                    <option value="state" <?php echo e($field->field_type == 'state' ? 'selected' : ''); ?>>State / Province</option>
                                    <option value="lga" <?php echo e($field->field_type == 'lga' ? 'selected' : ''); ?>>LGA / District</option>
                                    <option value="instructions" <?php echo e($field->field_type == 'instructions' ? 'selected' : ''); ?>>Informational Banner</option>
                                    <option value="declaration" <?php echo e($field->field_type == 'declaration' ? 'selected' : ''); ?>>Declaration Checkbox</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Placeholder</label>
                                <input type="text" name="fields[<?php echo e($index); ?>][placeholder]" class="form-control form-control-sm bg-white" value="<?php echo e($field->placeholder); ?>" placeholder="Instruction placeholder">
                            </div>
                            <div class="col-md-2 d-flex align-items-end gap-2">
                                <div class="form-check me-2">
                                    <input class="form-check-input" type="checkbox" name="fields[<?php echo e($index); ?>][is_required]" value="1" id="req_<?php echo e($index); ?>" <?php echo e($field->is_required ? 'checked' : ''); ?>>
                                    <label class="form-check-label small fw-semibold" for="req_<?php echo e($index); ?>">Required</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="fields[<?php echo e($index); ?>][is_enabled]" value="1" id="enb_<?php echo e($index); ?>" <?php echo e($field->is_enabled ? 'checked' : ''); ?>>
                                    <label class="form-check-label small fw-semibold" for="enb_<?php echo e($index); ?>">Active</label>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mt-1">
                            <div class="col-md-6">
                                <label class="form-label small text-secondary">Help Text / Description</label>
                                <input type="text" name="fields[<?php echo e($index); ?>][help_text]" class="form-control form-control-sm bg-white" value="<?php echo e($field->help_text); ?>" placeholder="Subtext instructions for user">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small text-secondary">Options (For Dropdown/Radio/Checkbox - one per line)</label>
                                <textarea name="fields[<?php echo e($index); ?>][options]" class="form-control form-control-sm bg-white" rows="1" placeholder="Option 1&#10;Option 2"><?php echo e(is_array($field->options) ? implode("\n", $field->options) : ''); ?></textarea>
                            </div>
                            <div class="col-md-2 d-flex align-items-end justify-content-end">
                                <input type="hidden" name="fields[<?php echo e($index); ?>][sort_order]" value="<?php echo e($field->sort_order ?? ($index + 1)); ?>">
                                <button type="button" class="btn btn-outline-danger btn-sm remove-field-btn">
                                    <i class="bi bi-trash"></i> Remove
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="p-5 text-center text-muted" id="noFieldsNotice">
                        <i class="bi bi-ui-checks-grid fs-1 d-block mb-2 text-secondary"></i>
                        <p class="mb-2">No custom form fields configured for this service yet.</p>
                        <p class="small text-secondary mb-0">Click "Add Custom Field" above to build your service questionnaire.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="card-footer bg-white py-3 text-end">
            <button type="submit" class="btn btn-success px-4" style="background-color: #004225; border: none;">
                <i class="bi bi-check-circle me-1"></i> Save Form Configuration
            </button>
        </div>
    </div>
</form>

<template id="fieldTemplate">
    <div class="field-row p-3 border-bottom position-relative bg-light bg-opacity-50">
        <input type="hidden" name="fields[__INDEX__][id]" value="">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label small fw-bold">Field Label *</label>
                <input type="text" name="fields[__INDEX__][field_label]" class="form-control form-control-sm bg-white" required placeholder="e.g. Full Maiden Name">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold">Field Type *</label>
                <select name="fields[__INDEX__][field_type]" class="form-select form-select-sm bg-white">
                    <option value="text">Text Input</option>
                    <option value="textarea">Text Area</option>
                    <option value="number">Number</option>
                    <option value="email">Email</option>
                    <option value="phone">Phone</option>
                    <option value="date">Date</option>
                    <option value="dropdown">Dropdown Select</option>
                    <option value="radio">Radio Buttons</option>
                    <option value="checkbox">Checkbox</option>
                    <option value="yes_no">Yes / No Choice</option>
                    <option value="file">File Upload</option>
                    <option value="passport">Passport Photograph Upload</option>
                    <option value="address">Full Address</option>
                    <option value="country">African Country Dropdown</option>
                    <option value="state">State / Province</option>
                    <option value="lga">LGA / District</option>
                    <option value="instructions">Informational Banner</option>
                    <option value="declaration">Declaration Checkbox</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold">Placeholder</label>
                <input type="text" name="fields[__INDEX__][placeholder]" class="form-control form-control-sm bg-white" placeholder="Instruction placeholder">
            </div>
            <div class="col-md-2 d-flex align-items-end gap-2">
                <div class="form-check me-2">
                    <input class="form-check-input" type="checkbox" name="fields[__INDEX__][is_required]" value="1" id="req___INDEX__" checked>
                    <label class="form-check-label small fw-semibold" for="req___INDEX__">Required</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="fields[__INDEX__][is_enabled]" value="1" id="enb___INDEX__" checked>
                    <label class="form-check-label small fw-semibold" for="enb___INDEX__">Active</label>
                </div>
            </div>
        </div>

        <div class="row g-3 mt-1">
            <div class="col-md-6">
                <label class="form-label small text-secondary">Help Text / Description</label>
                <input type="text" name="fields[__INDEX__][help_text]" class="form-control form-control-sm bg-white" placeholder="Subtext instructions for user">
            </div>
            <div class="col-md-4">
                <label class="form-label small text-secondary">Options (For Dropdown/Radio - one per line)</label>
                <textarea name="fields[__INDEX__][options]" class="form-control form-control-sm bg-white" rows="1" placeholder="Option 1&#10;Option 2"></textarea>
            </div>
            <div class="col-md-2 d-flex align-items-end justify-content-end">
                <input type="hidden" name="fields[__INDEX__][sort_order]" value="__SORT__">
                <button type="button" class="btn btn-outline-danger btn-sm remove-field-btn">
                    <i class="bi bi-trash"></i> Remove
                </button>
            </div>
        </div>
    </div>
</template>

<script>
document.addEventListener("DOMContentLoaded", function() {
    let indexCount = <?php echo e($fields->count()); ?>;
    const addBtn = document.getElementById("addFieldBtn");
    const container = document.getElementById("fieldsContainer");
    const templateHtml = document.getElementById("fieldTemplate").innerHTML;
    const notice = document.getElementById("noFieldsNotice");

    addBtn.addEventListener("click", function() {
        if (notice) notice.style.display = "none";
        indexCount++;
        let html = templateHtml.replace(/__INDEX__/g, indexCount).replace(/__SORT__/g, indexCount);
        container.insertAdjacentHTML("beforeend", html);
    });

    container.addEventListener("click", function(e) {
        if (e.target.closest(".remove-field-btn")) {
            const row = e.target.closest(".field-row");
            row.remove();
        }
    });
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\DOOTOR ENTERPRISES\resources\views\admin\form-builder.blade.php ENDPATH**/ ?>