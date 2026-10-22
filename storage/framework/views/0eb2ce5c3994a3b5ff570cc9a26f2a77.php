<?php $__env->startSection('title', 'Service Document Checklists - Service Assignment'); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <h4 class="fw-bold text-dark mb-1">Service Document Checklists</h4>
        <p class="text-secondary small mb-0">Select an active service and assign the required upload documents using checkboxes.</p>
    </div>
    <a href="<?php echo e(route('admin.document-types')); ?>" class="btn btn-outline-dark rounded-pill px-4 btn-sm">
        <i class="bi bi-folder-plus me-1"></i> Manage Master Document Types
    </a>
</div>

<?php if(session('success')): ?>
<div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
    <?php echo e(session('success')); ?>

    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php
    $rawAssigned = optional($selectedService)->required_documents;
    if (is_string($rawAssigned)) {
        $rawAssigned = json_decode($rawAssigned, true) ?: [];
    }

    $parsedChecklist = [];
    if (is_array($rawAssigned)) {
        foreach ($rawAssigned as $item) {
            if (is_array($item)) {
                $dName = $item['name'] ?? ($item['document_name'] ?? '');
                $isComp = isset($item['is_compulsory']) ? (bool)$item['is_compulsory'] : true;
            } else {
                $dName = (string)$item;
                $isComp = true;
            }
            if (!empty(trim($dName))) {
                $parsedChecklist[trim(strtolower($dName))] = [
                    'name' => trim($dName),
                    'is_compulsory' => $isComp,
                ];
            }
        }
    }

    $isDocAssigned = function($typeName) use ($parsedChecklist) {
        $cleanType = trim(strtolower($typeName));
        if (isset($parsedChecklist[$cleanType])) return true;
        $cleanNoAlpha = preg_replace('/[^a-z0-9]/i', '', $cleanType);
        foreach ($parsedChecklist as $key => $data) {
            $cleanKey = preg_replace('/[^a-z0-9]/i', '', $key);
            if ($cleanNoAlpha === $cleanKey) return true;
        }
        return false;
    };

    $isDocCompulsory = function($typeName) use ($parsedChecklist) {
        $cleanType = trim(strtolower($typeName));
        if (isset($parsedChecklist[$cleanType])) return $parsedChecklist[$cleanType]['is_compulsory'];
        $cleanNoAlpha = preg_replace('/[^a-z0-9]/i', '', $cleanType);
        foreach ($parsedChecklist as $key => $data) {
            $cleanKey = preg_replace('/[^a-z0-9]/i', '', $key);
            if ($cleanNoAlpha === $cleanKey) return $data['is_compulsory'];
        }
        return true;
    };

    $tickedCount = 0;
    if ($selectedService) {
        foreach ($documentTypes as $cat => $typesGroup) {
            foreach ($typesGroup as $dt) {
                if ($isDocAssigned($dt->name)) {
                    $tickedCount++;
                }
            }
        }
    }
?>

<div class="row g-4">
    <!-- Service Selector Column -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
            <h5 class="fw-bold text-dark mb-3">1. Select Target Service</h5>
            <form action="<?php echo e(route('admin.service-documents')); ?>" method="GET">
                <div class="mb-3">
                    <label class="form-label small text-muted">Active Platform Services (<?php echo e($services->count()); ?> Total)</label>
                    <select name="service_id" class="form-select form-select-lg rounded-3" onchange="this.form.submit()">
                        <?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $svc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($svc->id); ?>" <?php echo e(optional($selectedService)->id === $svc->id ? 'selected' : ''); ?>>
                                <?php echo e($svc->name); ?> ($<?php echo e(number_format($svc->price, 2)); ?>)
                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </form>

            <?php if($selectedService): ?>
            <div class="p-3 bg-light rounded-3 mt-3">
                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1 mb-2"><?php echo e($selectedService->category ?? 'General'); ?></span>
                <h6 class="fw-bold text-dark mb-1"><?php echo e($selectedService->name); ?></h6>
                <p class="small text-secondary mb-2"><?php echo e($selectedService->description); ?></p>
                
                <div class="small fw-semibold text-dark mt-2 border-top pt-2">
                    Current Assigned Checklists:
                    <span class="badge bg-dark text-white rounded-pill ms-1">
                        <?php echo e($tickedCount); ?> Ticked Documents
                    </span>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Document Checkboxes Column -->
    <div class="col-lg-8">
        <?php if($selectedService): ?>
        <div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                <div>
                    <h5 class="fw-bold text-dark mb-0">2. Assign Document Requirements</h5>
                    <span class="small text-muted">Check required documents for <?php echo e($selectedService->name); ?> and select if compulsory or optional</span>
                </div>
                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" onclick="toggleAllCheckboxes(true)">Select All</button>
            </div>

            <form action="<?php echo e(route('admin.service-documents.update', $selectedService->id)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PATCH'); ?>

                <div class="d-flex flex-column gap-4 my-3">
                    <?php $__currentLoopData = $documentTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category => $types): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div>
                        <h6 class="fw-bold text-uppercase text-secondary small tracking-wider mb-3 px-2 py-1 bg-light rounded-3 border-start border-3 border-success">
                            <i class="bi bi-folder2-open me-2"></i> <?php echo e($category); ?> Documents
                        </h6>

                        <div class="row g-3">
                            <?php $__currentLoopData = $types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $docType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $isChecked = $isDocAssigned($docType->name);
                                $isComp = $isDocCompulsory($docType->name);
                            ?>
                            <div class="col-md-6">
                                <div class="p-3 border rounded-3 h-100 bg-white hover-shadow transition" style="border-color: <?php echo e($isChecked ? '#004225' : '#e2e8f0'); ?>; background-color: <?php echo e($isChecked ? 'rgba(0, 66, 37, 0.03)' : '#ffffff'); ?>;">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="form-check">
                                            <input class="form-check-input doc-checkbox" type="checkbox" name="required_documents[]" value="<?php echo e($docType->name); ?>" id="docCheck<?php echo e($docType->id); ?>" <?php echo e($isChecked ? 'checked' : ''); ?>>
                                            <label class="form-check-label fw-bold text-dark small ms-1" for="docCheck<?php echo e($docType->id); ?>">
                                                <?php echo e($docType->name); ?>

                                            </label>
                                        </div>
                                        <div class="form-check form-switch ms-2 mb-0" title="Toggle Compulsory vs Optional">
                                            <input class="form-check-input" type="checkbox" name="compulsory_docs[]" value="<?php echo e($docType->name); ?>" id="compSwitch<?php echo e($docType->id); ?>" <?php echo e(($isChecked && $isComp) || (!$isChecked) ? 'checked' : ''); ?>>
                                            <label class="form-check-label extra-small text-muted fw-semibold" for="compSwitch<?php echo e($docType->id); ?>" style="font-size: 11px;">
                                                Compulsory
                                            </label>
                                        </div>
                                    </div>
                                    <?php if($docType->description): ?>
                                    <p class="small text-muted mb-0 mt-1 ms-4" style="font-size: 11.5px;"><?php echo e($docType->description); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                <div class="border-top pt-3 mt-4 d-flex justify-content-between align-items-center">
                    <span class="small text-muted"><i class="bi bi-info-circle me-1"></i> Changes will immediately take effect on client booking pages.</span>
                    <button type="submit" class="btn text-white rounded-pill px-4 py-2 fw-bold" style="background-color: #004225;">
                        <i class="bi bi-check2-circle me-1"></i> Save Required Documents Checklist
                    </button>
                </div>
            </form>
        </div>
        <?php else: ?>
        <div class="card border-0 shadow-sm p-5 rounded-4 text-center bg-white">
            <i class="bi bi-arrow-left-circle display-4 text-muted mb-2"></i>
            <h5 class="fw-bold text-dark">No Active Service Selected</h5>
            <p class="small text-secondary mb-0">Please select a service from the left dropdown panel to manage document requirements.</p>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
    function toggleAllCheckboxes(state) {
        document.querySelectorAll('.doc-checkbox').forEach(cb => {
            cb.checked = state;
        });
    }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\DOOTOR ENTERPRISES\resources\views\admin\service-documents.blade.php ENDPATH**/ ?>