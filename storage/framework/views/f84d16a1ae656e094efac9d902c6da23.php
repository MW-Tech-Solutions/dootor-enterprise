<?php $__env->startSection('title', 'Login - ' . ($settings->platform_name ?? 'DOOTOR ENTERPRISES')); ?>

<?php $__env->startSection('styles'); ?>
<style>
    .btn-brand-primary {
        background-color: #004225 !important;
        border: none !important;
        color: #ffffff !important;
        font-weight: 600;
        transition: all 0.25s ease;
    }
    .btn-brand-primary:hover {
        background-color: #002411 !important;
        box-shadow: 0 4px 12px rgba(0, 66, 37, 0.2);
        transform: translateY(-1px);
    }
    .text-brand-success {
        color: #004225 !important;
        font-weight: 600;
    }
    .text-brand-success:hover {
        color: #d4af37 !important;
        text-decoration: underline !important;
    }
    .form-control:focus {
        border-color: #004225 !important;
        box-shadow: 0 0 0 0.25rem rgba(0, 66, 37, 0.15) !important;
    }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('body'); ?>
<div class="container-fluid p-0 min-vh-100 d-flex flex-column flex-lg-row">
    <!-- Left Pane: Branding & Service Features (hidden on mobile, shown on lg) -->
    <div class="col-lg-5 d-none d-lg-flex flex-column justify-content-between p-5 text-white position-relative" style="background: linear-gradient(135deg, #001a0c 0%, #003b1c 100%); min-height: 100vh;">
        <!-- Glowing background decoration -->
        <div class="position-absolute top-0 start-0 w-100 h-100" style="background: radial-gradient(circle at 10% 10%, rgba(212, 175, 55, 0.15), transparent 60%); pointer-events: none;"></div>
        
        <div>
            <a href="/" class="d-inline-flex align-items-center gap-2 text-decoration-none text-white mb-5">
                <?php if($settings && $settings->logo_url): ?>
                    <img src="<?php echo e(asset($settings->logo_url)); ?>" alt="Logo" class="rounded" style="height: 48px; width: 48px; object-fit: contain;">
                <?php else: ?>
                    <svg width="40" height="40" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg" class="me-1">
                        <!-- Stylized D (Gold) -->
                        <path d="M20 20C40 20 52 32 52 50C52 68 40 80 20 80C14 80 14 74 14 74V26C14 26 14 20 20 20Z" fill="url(#goldLogoLogin)" />
                        <path d="M28 32C38 32 44 40 44 50C44 60 38 68 28 68V32Z" fill="#FFFFFF" />
                        <!-- Stylized E (Dark Green) -->
                        <path d="M50 24H80V35H64V44H76V53H64V63H80V74H50V24Z" fill="#004225" />
                        <defs>
                            <linearGradient id="goldLogoLogin" x1="14" y1="20" x2="52" y2="80" gradientUnits="userSpaceOnUse">
                                <stop offset="0%" stop-color="#FFE57F" />
                                <stop offset="50%" stop-color="#D4AF37" />
                                <stop offset="100%" stop-color="#AA820A" />
                            </linearGradient>
                        </defs>
                    </svg>
                <?php endif; ?>
                <span class="fw-bold fs-4 tracking-tight text-white"><?php echo e($settings->platform_name ?? 'DOOTOR ENTERPRISES'); ?></span>
            </a>
            
            <h2 class="display-6 fw-bold text-white mb-3" style="line-height: 1.2;">NIN, BVN, Passport &amp; Other Migration Facilities Documents Support</h2>
            <p class="text-white-50 mb-5 fs-6">Connecting clients with verified expert agents for expedited official document processing, authentication, and vetting support.</p>
            
            <div class="d-flex flex-column gap-3">
                <div class="d-flex align-items-start gap-3">
                    <div class="bg-warning text-dark rounded-circle p-1 d-flex align-items-center justify-content-center" style="width: 24px; height: 24px; background-color: #d4af37 !important; flex-shrink: 0;">
                        <i class="bi bi-check-lg fw-bold" style="font-size: 14px;"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 text-white fw-semibold small">100% Vetted Expert Consultants</h5>
                        <p class="text-white-50 small mb-0">Direct access to experienced agents specialized in identity and immigration tasks.</p>
                    </div>
                </div>
                
                <div class="d-flex align-items-start gap-3">
                    <div class="bg-warning text-dark rounded-circle p-1 d-flex align-items-center justify-content-center" style="width: 24px; height: 24px; background-color: #d4af37 !important; flex-shrink: 0;">
                        <i class="bi bi-check-lg fw-bold" style="font-size: 14px;"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 text-white fw-semibold small">Secure Document Verification</h5>
                        <p class="text-white-50 small mb-0">Encrypted portal ensuring your passport, BVN, and identity details are private.</p>
                    </div>
                </div>

                <div class="d-flex align-items-start gap-3">
                    <div class="bg-warning text-dark rounded-circle p-1 d-flex align-items-center justify-content-center" style="width: 24px; height: 24px; background-color: #d4af37 !important; flex-shrink: 0;">
                        <i class="bi bi-check-lg fw-bold" style="font-size: 14px;"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 text-white fw-semibold small">Real-Time Application Tracking</h5>
                        <p class="text-white-50 small mb-0">Follow every phase of your order status from vetting to final dispatch.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="border-top border-secondary border-opacity-20 pt-4 mt-5">
            <span class="d-block small text-white-50" style="font-size: 11px; line-height: 1.4;">WE ARE NOT GOVERNMENT OFFICIALS, AFFILIATES, OR AN ANNEX OF THE NIGERIAN HIGH COMMISSION.</span>
        </div>
    </div>

    <!-- Right Pane: Login Form Container -->
    <div class="col-lg-7 d-flex align-items-center justify-content-center bg-light py-5 px-3 px-sm-5" style="flex-grow: 1; min-height: 100vh;">
        <div class="card border-0 shadow-sm p-4 p-sm-5 rounded-4 bg-white" style="max-width: 460px; width: 100%;">
            <!-- Mobile Brand Logo header (hidden on desktop) -->
            <div class="text-center mb-4 d-block d-lg-none">
                <a href="/" class="d-inline-flex align-items-center gap-2 text-decoration-none text-dark mb-2">
                    <?php if($settings && $settings->logo_url): ?>
                        <img src="<?php echo e(asset($settings->logo_url)); ?>" alt="Logo" class="rounded" style="height: 38px; width: 38px; object-fit: contain;">
                    <?php else: ?>
                        <svg width="28" height="28" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg" class="me-1">
                            <path d="M20 20C40 20 52 32 52 50C52 68 40 80 20 80C14 80 14 74 14 74V26C14 26 14 20 20 20Z" fill="url(#goldLogoLoginMobile)" />
                            <path d="M28 32C38 32 44 40 44 50C44 60 38 68 28 68V32Z" fill="#FFFFFF" />
                            <path d="M50 24H80V35H64V44H76V53H64V63H80V74H50V24Z" fill="#004225" />
                            <defs>
                                <linearGradient id="goldLogoLoginMobile" x1="14" y1="20" x2="52" y2="80" gradientUnits="userSpaceOnUse">
                                    <stop offset="0%" stop-color="#FFE57F" />
                                    <stop offset="50%" stop-color="#D4AF37" />
                                    <stop offset="100%" stop-color="#AA820A" />
                                </linearGradient>
                            </defs>
                        </svg>
                    <?php endif; ?>
                    <span class="fw-bold text-dark fs-5"><?php echo e($settings->platform_name ?? 'DOOTOR ENTERPRISES'); ?></span>
                </a>
            </div>

            <div class="mb-4">
                <h1 class="h3 fw-bold text-dark mb-1">Welcome Back</h1>
                <p class="text-secondary small">Access your account to manage your document services</p>
            </div>

            <form action="<?php echo e(route('login')); ?>" method="POST">
                <?php echo csrf_field(); ?>

                <!-- Email -->
                <div class="mb-3">
                    <label for="email" class="form-label small fw-medium">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted" style="border-color: #dee2e6;"><i class="bi bi-envelope"></i></span>
                        <input type="email" name="email" id="email" class="form-control bg-light border-start-0 rounded-end-3 <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('email')); ?>" placeholder="abiodun@dootor.com" style="border-color: #dee2e6;" required autofocus>
                    </div>
                    <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="text-danger small mt-1" style="font-size: 11.5px;"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <!-- Password -->
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label for="password" class="form-label small fw-medium mb-0">Password</label>
                        <a href="<?php echo e(route('password.request')); ?>" class="text-decoration-none small text-secondary fw-semibold" style="font-size: 12.5px;">Forgot Password?</a>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted" style="border-color: #dee2e6;"><i class="bi bi-lock"></i></span>
                        <input type="password" name="password" id="password" class="form-control bg-light border-start-0 rounded-end-3" placeholder="••••••••" style="border-color: #dee2e6;" required>
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label small text-secondary fw-medium" for="remember">
                        Keep me logged in
                    </label>
                </div>

                <!-- Submit -->
                <button type="submit" class="btn btn-brand-primary w-100 py-2.5 rounded-3 mb-3 d-flex align-items-center justify-content-center gap-2">
                    <span>Sign In</span>
                    <i class="bi bi-arrow-right"></i>
                </button>
            </form>

            <div class="text-center text-secondary small mt-3 pt-3 border-top border-light">
                Don't have an account? <br class="d-none d-sm-block">
                <a href="<?php echo e(route('register.client')); ?>" class="text-decoration-none text-brand-success fw-semibold">Register Account</a>
                
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\DOOTOR ENTERPRISES\resources\views\auth\login.blade.php ENDPATH**/ ?>