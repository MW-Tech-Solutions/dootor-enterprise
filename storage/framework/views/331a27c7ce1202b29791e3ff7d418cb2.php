<?php $__env->startSection('title', 'Master Document Requirement Types'); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <h4 class="fw-bold text-dark mb-1">Master Document Requirement Types</h4>
        <p class="text-secondary small mb-0">Manage global document templates and assign them dynamically to your admin-added services.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?php echo e(route('admin.service-documents')); ?>" class="btn btn-outline-dark rounded-pill px-4 btn-sm">
            <i class="bi bi-card-checklist me-1"></i> View Service Checklists
        </a>
        <button type="button" class="btn text-white rounded-pill px-4 btn-sm" style="background-color: #004225;" data-bs-toggle="modal" data-bs-target="#addDocumentTypeModal">
            <i class="bi bi-plus-circle me-1"></i> Add Document Type
        </button>
    </div>
</div>

<?php if(session('success')): ?>
<div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
    <?php echo e(session('success')); ?>

    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
    <div class="card-body p-0">
        <?php if($documentTypes->count() > 0): ?>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="bg-light">
                    <tr class="text-secondary small">
                        <th class="ps-4">Document Type Name</th>
                        <th>Category</th>
                        <th>Assigned Admin Services</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $documentTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $isDocInService = function($service, $typeName) {
                            $raw = $service->required_documents;
                            if (is_string($raw)) $raw = json_decode($raw, true) ?: [];
                            $assigned = is_array($raw) ? array_map('trim', $raw) : [];

                            if (in_array($typeName, $assigned)) return true;
                            $cleanType = strtolower(preg_replace('/[^a-z0-9]/i', '', $typeName));
                            foreach ($assigned as $item) {
                                $cleanItem = strtolower(preg_replace('/[^a-z0-9]/i', '', $item));
                                if ($cleanType === $cleanItem) return true;
                                if (strlen($cleanItem) > 3 && (str_contains($cleanType, $cleanItem) || str_contains($cleanItem, $cleanType))) {
                                    return true;
                                }
                            }
                            return false;
                        };

                        // Dynamically filter which admin-added services currently have this document in required_documents
                        $assignedServices = $allServices->filter(function($svc) use ($type, $isDocInService) {
                            return $isDocInService($svc, $type->name);
                        });
                    ?>
                    <tr>
                        <td class="ps-4">
                            <span class="fw-bold text-dark d-block"><?php echo e($type->name); ?></span>
                            <small class="text-muted d-block" style="font-size: 11.5px;"><?php echo e($type->description ?? 'No description'); ?></small>
                        </td>
                        <td>
                            <span class="badge bg-secondary bg-opacity-10 text-dark rounded-pill px-3 py-1">
                                <?php echo e($type->category); ?>

                            </span>
                        </td>
                        <td>
                            <button class="btn btn-light btn-sm rounded-pill px-3 border small" data-bs-toggle="modal" data-bs-target="#assignModal<?php echo e($type->id); ?>">
                                <i class="bi bi-box-seam me-1 text-primary"></i>
                                <span class="fw-bold"><?php echo e($assignedServices->count()); ?> Services</span>
                            </button>
                        </td>
                        <td>
                            <?php if($type->is_active): ?>
                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2.5 py-1">Active</span>
                            <?php else: ?>
                                <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-2.5 py-1">Inactive</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-flex justify-content-end gap-2">
                                <button class="btn btn-outline-dark btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#assignModal<?php echo e($type->id); ?>">
                                    <i class="bi bi-check2-square me-1"></i> Assign
                                </button>
                                <button class="btn btn-outline-secondary btn-sm rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 34px; height: 34px;" data-bs-toggle="modal" data-bs-target="#editModal<?php echo e($type->id); ?>">
                                    <i class="bi bi-pencil-fill"></i>
                                </button>
                                <form action="<?php echo e(route('admin.document-types.delete', $type->id)); ?>" method="POST" onsubmit="return confirm('Are you sure you want to delete this document type?')">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-outline-danger btn-sm rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 34px; height: 34px;">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                </form>
                            </div>

                            <!-- Assign Services Modal -->
                            <div class="modal fade text-start" id="assignModal<?php echo e($type->id); ?>" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content border-0 rounded-4 shadow">
                                        <div class="modal-header border-0 pb-0">
                                            <div>
                                                <h5 class="modal-title fw-bold text-dark">Assign "<?php echo e($type->name); ?>" to Services</h5>
                                                <span class="small text-muted">Check all admin-added services that require applicants to upload this document</span>
                                            </div>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="<?php echo e(route('admin.document-types.assign-services', $type->id)); ?>" method="POST">
                                            <?php echo csrf_field(); ?>
                                            <div class="modal-body p-4">
                                                <?php if($allServices->count() > 0): ?>
                                                    <div class="row g-3">
                                                        <?php $__currentLoopData = $allServices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <?php
                                                            $hasDoc = $isDocInService($service, $type->name);
                                                        ?>
                                                        <div class="col-md-6">
                                                            <div class="p-3 border rounded-3 bg-white h-100">
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox" name="service_ids[]" value="<?php echo e($service->id); ?>" id="svcCheck<?php echo e($type->id); ?>_<?php echo e($service->id); ?>" <?php echo e($hasDoc ? 'checked' : ''); ?>>
                                                                    <label class="form-check-label fw-bold text-dark small ms-1" for="svcCheck<?php echo e($type->id); ?>_<?php echo e($service->id); ?>">
                                                                        <?php echo e($service->name); ?>

                                                                    </label>
                                                                </div>
                                                                <small class="text-muted d-block ms-4" style="font-size: 11.5px;">$<?php echo e(number_format($service->price, 2)); ?> • <?php echo e($service->category ?? 'General'); ?></small>
                                                            </div>
                                                        </div>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </div>
                                                <?php else: ?>
                                                    <div class="text-center py-4">
                                                        <span class="small text-muted">No admin services created yet in `/admin/services`.</span>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="modal-footer border-0 pt-0">
                                                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn text-white rounded-pill px-4" style="background-color: #004225;">Save Service Assignments</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Edit Modal -->
                            <div class="modal fade text-start" id="editModal<?php echo e($type->id); ?>" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 rounded-4 shadow">
                                        <div class="modal-header border-0 pb-0">
                                            <h5 class="modal-title fw-bold text-dark">Edit Document Type</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="<?php echo e(route('admin.document-types.update', $type->id)); ?>" method="POST">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('PATCH'); ?>
                                            <div class="modal-body p-4">
                                                <div class="mb-3">
                                                    <label class="form-label small fw-semibold text-dark">Document Type Name</label>
                                                    <input type="text" name="name" class="form-control rounded-3" value="<?php echo e($type->name); ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label small fw-semibold text-dark">Category</label>
                                                    <select name="category" class="form-select rounded-3" required>
                                                        <option value="Identity" <?php echo e($type->category === 'Identity' ? 'selected' : ''); ?>>Identity</option>
                                                        <option value="Financial" <?php echo e($type->category === 'Financial' ? 'selected' : ''); ?>>Financial</option>
                                                        <option value="Legal" <?php echo e($type->category === 'Legal' ? 'selected' : ''); ?>>Legal</option>
                                                        <option value="Travel" <?php echo e($type->category === 'Travel' ? 'selected' : ''); ?>>Travel</option>
                                                        <option value="Corporate" <?php echo e($type->category === 'Corporate' ? 'selected' : ''); ?>>Corporate</option>
                                                        <option value="General" <?php echo e($type->category === 'General' ? 'selected' : ''); ?>>General</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label small fw-semibold text-dark">Description</label>
                                                    <textarea name="description" class="form-control rounded-3" rows="3"><?php echo e($type->description); ?></textarea>
                                                </div>
                                                <div class="form-check form-switch mb-2">
                                                    <input class="form-check-input" type="checkbox" name="is_active" id="activeCheck<?php echo e($type->id); ?>" value="1" <?php echo e($type->is_active ? 'checked' : ''); ?>>
                                                    <label class="form-check-label small fw-semibold" for="activeCheck<?php echo e($type->id); ?>">Active for Service Assignment</label>
                                                </div>
                                            </div>
                                            <div class="modal-footer border-0 pt-0">
                                                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn text-white rounded-pill px-4" style="background-color: #004225;">Save Changes</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="text-center py-5">
            <i class="bi bi-file-earmark-code display-4 text-muted"></i>
            <h5 class="fw-bold text-dark mt-3">No Document Types Configured</h5>
            <p class="text-secondary small">Add master document requirement templates to assign them to services.</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Add Document Type Modal -->
<div class="modal fade" id="addDocumentTypeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-dark">Add New Master Document Type</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?php echo e(route('admin.document-types.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-dark">Document Type Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control rounded-3" placeholder="e.g. Sworn High Court Affidavit" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-dark">Category <span class="text-danger">*</span></label>
                        <select name="category" class="form-select rounded-3" required>
                            <option value="Identity">Identity</option>
                            <option value="Financial">Financial</option>
                            <option value="Legal">Legal</option>
                            <option value="Travel">Travel</option>
                            <option value="Corporate">Corporate</option>
                            <option value="General" selected>General</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-dark">Description</label>
                        <textarea name="description" class="form-control rounded-3" rows="3" placeholder="Explain requirements for applicants uploading this document..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn text-white rounded-pill px-4" style="background-color: #004225;">Add Document Type</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\DOOTOR ENTERPRISES\resources\views\admin\document-types.blade.php ENDPATH**/ ?>