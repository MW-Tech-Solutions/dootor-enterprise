<?php $__env->startSection('title', 'Profile Settings - ' . ($settings->platform_name ?? 'Dooter Enterprises')); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-4">
    <h1 class="h3 fw-bold text-dark mb-1">Profile Settings</h1>
    <p class="text-secondary small">Manage your personal info and user profile settings</p>
</div>

<div class="card border-0 shadow-sm p-3.5 p-sm-4 rounded-4 bg-white" style="max-width: 600px; border: 1px solid #eef2f6 !important; border-radius: 20px !important; padding: 22px 20px !important;">
    <form action="<?php echo e(route('client.settings.update')); ?>" method="POST" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>

        <!-- Profile photo -->
        <div class="mb-4 d-flex align-items-center gap-3">
            <?php if($user->avatar_url): ?>
                <img src="<?php echo e(app_file_url($user->avatar_url)); ?>" alt="avatar" class="rounded-circle border border-3 border-light shadow-sm" style="width: 80px; height: 80px; object-fit: cover;">
            <?php else: ?>
                <div class="rounded-circle bg-dark text-white d-flex align-items-center justify-content-center fw-bold border border-3 border-light shadow-sm" style="width: 80px; height: 80px; font-size: 28px;">
                    <?php echo e(strtoupper(substr($user->first_name ?? 'U', 0, 1))); ?>

                </div>
            <?php endif; ?>
            <div>
                <label for="avatar" class="form-label small fw-medium mb-1">Profile Picture</label>
                <input type="file" name="avatar" id="avatar" class="form-control form-control-sm border-0 bg-light rounded-3">
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-sm-6">
                <label for="first_name" class="form-label small fw-medium">First Name</label>
                <input type="text" name="first_name" id="first_name" class="form-control rounded-3 <?php $__errorArgs = ['first_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('first_name', $user->first_name)); ?>" required>
                <?php $__errorArgs = ['first_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="col-sm-6">
                <label for="last_name" class="form-label small fw-medium">Last Name</label>
                <input type="text" name="last_name" id="last_name" class="form-control rounded-3 <?php $__errorArgs = ['last_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('last_name', $user->last_name)); ?>" required>
                <?php $__errorArgs = ['last_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label small fw-medium">Email Address (Read Only)</label>
            <input type="email" class="form-control rounded-3 bg-light text-muted" value="<?php echo e($user->email); ?>" readonly>
        </div>

        <div class="mb-3">
            <label for="phone" class="form-label small fw-medium">Phone Number</label>
            <input type="text" name="phone" id="phone" class="form-control rounded-3 <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('phone', $user->phone)); ?>">
            <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <div class="invalid-feedback"><?php echo e($message); ?></div>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-sm-6">
                <label for="country" class="form-label small fw-medium">Country (African Countries Only)</label>
                <select name="country" id="country" class="form-select african-country-select rounded-3 <?php $__errorArgs = ['country'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" data-selected="<?php echo e(old('country', $user->country ?? 'Nigeria')); ?>" data-division-target="state" data-label-target="state_label" required>
                    <?php
                        $afCountries = $africanCountries ?? \App\Services\AfricanLocationService::allCountries();
                        $selectedCountry = old('country', $user->country ?? 'Nigeria');
                    ?>
                    <?php $__currentLoopData = $afCountries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($c['name']); ?>" <?php echo e($selectedCountry == $c['name'] ? 'selected' : ''); ?>><?php echo e($c['name']); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php $__errorArgs = ['country'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="col-sm-6">
                <label for="state" id="state_label" class="form-label small fw-medium african-division-label">State / Region</label>
                <select name="state" id="state" class="form-select african-division-select rounded-3 <?php $__errorArgs = ['state'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" data-selected="<?php echo e(old('state', $user->state)); ?>" required>
                    <option value="">Loading...</option>
                </select>
                <?php $__errorArgs = ['state'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
        </div>

        <button type="submit" class="btn btn-dark rounded-pill px-4 py-2 small">Save Profile Changes</button>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="<?php echo e(asset('js/location-loader.js')); ?>"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\DOOTOR ENTERPRISES\resources\views\client\settings.blade.php ENDPATH**/ ?>