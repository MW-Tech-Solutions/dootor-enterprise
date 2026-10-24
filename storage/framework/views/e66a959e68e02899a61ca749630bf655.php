<?php $__env->startSection('title', 'FAQs Management - ' . ($settings->platform_name ?? 'DOOTOR ENTERPRISES')); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <h1 class="h3 fw-bold text-dark mb-0">FAQs Dynamic Management</h1>
                <span class="badge bg-dark rounded-pill px-2.5 py-1 small"><?php echo e($faqs->count()); ?> FAQs</span>
            </div>
            <p class="text-secondary small mb-0">Create, edit, reorder, and manage frequently asked questions displayed dynamically on the public homepage.</p>
        </div>
        <div>
            <button class="btn btn-dark btn-sm rounded-pill px-3 py-2 d-flex align-items-center gap-1.5 shadow-sm" type="button" data-bs-toggle="modal" data-bs-target="#createFaqModal">
                <i class="bi bi-plus-circle-fill"></i>
                <span>Add New FAQ</span>
            </button>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row g-3 mb-4">
        <div class="col-sm-4">
            <div class="card border-0 shadow-sm p-3 rounded-4 bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase d-block mb-1" style="font-size: 11px;">Total Questions</span>
                        <h3 class="fw-bold text-dark mb-0"><?php echo e($faqs->count()); ?></h3>
                    </div>
                    <div class="rounded-3 p-2 bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; font-size: 20px;">
                        <i class="bi bi-question-circle-fill"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card border-0 shadow-sm p-3 rounded-4 bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase d-block mb-1" style="font-size: 11px;">Active / Published</span>
                        <h3 class="fw-bold text-success mb-0"><?php echo e($faqs->where('status', 'Active')->count()); ?></h3>
                    </div>
                    <div class="rounded-3 p-2 bg-success-subtle text-success d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; font-size: 20px;">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card border-0 shadow-sm p-3 rounded-4 bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase d-block mb-1" style="font-size: 11px;">Hidden / Draft</span>
                        <h3 class="fw-bold text-secondary mb-0"><?php echo e($faqs->where('status', 'Inactive')->count()); ?></h3>
                    </div>
                    <div class="rounded-3 p-2 bg-secondary-subtle text-secondary d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; font-size: 20px;">
                        <i class="bi bi-eye-slash-fill"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FAQ Table Card -->
    <div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
        <?php if($faqs->count() > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 70px;">Order</th>
                            <th>Question &amp; Answer</th>
                            <th style="width: 140px;">Category</th>
                            <th style="width: 100px;">Status</th>
                            <th style="width: 130px;" class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $faqs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $faq): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td>
                                    <span class="badge bg-light text-dark border rounded-pill px-2.5 py-1 font-monospace">
                                        #<?php echo e($faq->sort_order); ?>

                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex flex-column gap-1">
                                        <strong class="text-dark fs-6"><?php echo e($faq->question); ?></strong>
                                        <p class="text-secondary small mb-0" style="line-height: 1.5;">
                                            <?php echo e(Str::limit($faq->answer, 140)); ?>

                                        </p>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-info-subtle text-info-emphasis rounded-pill px-2.5 py-1 small">
                                        <?php echo e($faq->category ?? 'General'); ?>

                                    </span>
                                </td>
                                <td>
                                    <?php if($faq->status === 'Active'): ?>
                                        <span class="badge bg-success-subtle text-success rounded-pill px-2.5 py-1">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2.5 py-1">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1">
                                        <button class="btn btn-outline-dark btn-sm rounded-pill px-3" type="button" data-bs-toggle="modal" data-bs-target="#editFaqModal-<?php echo e($faq->id); ?>">
                                            <i class="bi bi-pencil me-1"></i> Edit
                                        </button>
                                        <form action="<?php echo e(route('admin.faqs.delete', $faq->id)); ?>" method="POST" class="d-inline m-0" onsubmit="return confirm('Delete this FAQ entry permanently?');">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            <!-- Edit Modal for each FAQ -->
                            <div class="modal fade" id="editFaqModal-<?php echo e($faq->id); ?>" tabindex="-1" aria-labelledby="editFaqModalLabel-<?php echo e($faq->id); ?>" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content border-0 shadow rounded-4">
                                        <div class="modal-header border-bottom py-3">
                                            <h5 class="modal-title fw-bold text-dark fs-6" id="editFaqModalLabel-<?php echo e($faq->id); ?>">Edit FAQ Question</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="<?php echo e(route('admin.faqs.update', $faq->id)); ?>" method="POST">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('PATCH'); ?>
                                            <div class="modal-body p-4">
                                                <div class="row g-3">
                                                    <div class="col-12">
                                                        <label for="edit_question_<?php echo e($faq->id); ?>" class="form-label small fw-semibold">Question</label>
                                                        <input type="text" name="question" id="edit_question_<?php echo e($faq->id); ?>" class="form-control rounded-3" value="<?php echo e(old('question', $faq->question)); ?>" required>
                                                    </div>
                                                    <div class="col-12">
                                                        <label for="edit_answer_<?php echo e($faq->id); ?>" class="form-label small fw-semibold">Answer Content</label>
                                                        <textarea name="answer" id="edit_answer_<?php echo e($faq->id); ?>" class="form-control rounded-3" rows="4" required><?php echo e(old('answer', $faq->answer)); ?></textarea>
                                                    </div>
                                                    <div class="col-md-5">
                                                        <label for="edit_category_<?php echo e($faq->id); ?>" class="form-label small fw-semibold">Category</label>
                                                        <input type="text" name="category" id="edit_category_<?php echo e($faq->id); ?>" class="form-control rounded-3" value="<?php echo e(old('category', $faq->category)); ?>" placeholder="e.g. General, Compliance, Security">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="edit_order_<?php echo e($faq->id); ?>" class="form-label small fw-semibold">Display Sort Order</label>
                                                        <input type="number" name="sort_order" id="edit_order_<?php echo e($faq->id); ?>" class="form-control rounded-3" value="<?php echo e(old('sort_order', $faq->sort_order)); ?>">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label for="edit_status_<?php echo e($faq->id); ?>" class="form-label small fw-semibold">Status</label>
                                                        <select name="status" id="edit_status_<?php echo e($faq->id); ?>" class="form-select rounded-3">
                                                            <option value="Active" <?php echo e($faq->status === 'Active' ? 'selected' : ''); ?>>Active</option>
                                                            <option value="Inactive" <?php echo e($faq->status === 'Inactive' ? 'selected' : ''); ?>>Inactive</option>
                                                        </select>
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
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="bi bi-question-circle fs-1 text-muted"></i>
                <h5 class="fw-bold text-dark mt-2 mb-1">No FAQs Added Yet</h5>
                <p class="text-secondary small mb-3">Create your first FAQ question to display on the public homepage.</p>
                <button class="btn btn-dark rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#createFaqModal">Add New FAQ</button>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Create FAQ Modal -->
<div class="modal fade" id="createFaqModal" tabindex="-1" aria-labelledby="createFaqModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-bottom py-3">
                <h5 class="modal-title fw-bold text-dark fs-6" id="createFaqModalLabel">Add Frequently Asked Question</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo e(route('admin.faqs.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="create_question" class="form-label small fw-semibold">Question</label>
                            <input type="text" name="question" id="create_question" class="form-control rounded-3" placeholder="e.g. How does document vetting work?" required>
                        </div>
                        <div class="col-12">
                            <label for="create_answer" class="form-label small fw-semibold">Answer Content</label>
                            <textarea name="answer" id="create_answer" class="form-control rounded-3" rows="4" placeholder="Detailed clear answer for applicants..." required></textarea>
                        </div>
                        <div class="col-md-5">
                            <label for="create_category" class="form-label small fw-semibold">Category</label>
                            <input type="text" name="category" id="create_category" class="form-control rounded-3" value="General" placeholder="e.g. General, Compliance, Security">
                        </div>
                        <div class="col-md-4">
                            <label for="create_order" class="form-label small fw-semibold">Display Sort Order</label>
                            <input type="number" name="sort_order" id="create_order" class="form-control rounded-3" value="<?php echo e(($faqs->max('sort_order') ?? 0) + 1); ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="create_status" class="form-label small fw-semibold">Status</label>
                            <select name="status" id="create_status" class="form-select rounded-3">
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top py-2.5">
                    <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-dark rounded-pill px-4">Create FAQ</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\DOOTOR ENTERPRISES\resources\views\admin\faqs.blade.php ENDPATH**/ ?>