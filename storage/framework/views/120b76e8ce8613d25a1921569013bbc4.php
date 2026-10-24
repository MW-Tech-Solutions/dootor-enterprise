<?php $__env->startSection('body'); ?>
<?php
    $user = Auth::user();
    $role = $user->role ?? 'client';
    $status = $user->status ?? 'Approved';
    $isPendingVendor = ($role === 'vendor' && ($status === 'Pending' || $status === 'Rejected'));

    // Dynamic Navigation links matching Next.js
    $navItems = [];
    $settingsPath = '';

    if ($isPendingVendor) {
        $navItems = [
            ['href' => route('vendor.kyc'), 'icon' => 'bi-shield-check', 'label' => 'Complete KYC']
        ];
    } else {
        if ($role === 'admin' || $user->isAdmin() || $user->roles()->exists()) {
            $allNavItems = [
                ['href' => route('admin.dashboard'), 'icon' => 'bi-grid', 'label' => 'Dashboard', 'permission' => null],
                ['href' => route('admin.payment-verification'), 'icon' => 'bi-credit-card-2-front', 'label' => 'Payment Verification', 'permission' => 'applications.view'],
                ['href' => route('admin.work-queue'), 'icon' => 'bi-clock-history', 'label' => 'Staff Work Queue', 'permission' => 'applications.view'],
                ['href' => route('admin.subscriptions'), 'icon' => 'bi-file-earmark-check', 'label' => 'Service Applications', 'permission' => 'applications.view'],
                ['href' => route('admin.expirations'), 'icon' => 'bi-hourglass-split', 'label' => 'App Expirations', 'permission' => 'applications.view'],
                ['href' => route('admin.users'), 'icon' => 'bi-people', 'label' => 'User Management', 'permission' => 'users.view'],
                ['href' => route('admin.roles'), 'icon' => 'bi-shield-lock', 'label' => 'Roles & RBAC', 'permission' => 'roles.view'],
                ['href' => route('admin.services'), 'icon' => 'bi-box-seam', 'label' => 'Services Catalog', 'permission' => 'services.view'],
                ['href' => route('admin.service-documents'), 'icon' => 'bi-card-checklist', 'label' => 'Service Checklists', 'permission' => 'documents.view'],
                ['href' => route('admin.document-types'), 'icon' => 'bi-file-earmark-plus', 'label' => 'Document Templates', 'permission' => 'documents.view'],
                ['href' => route('admin.email-templates'), 'icon' => 'bi-envelope-paper', 'label' => 'Email Templates', 'permission' => 'email.view'],
                ['href' => Route::has('admin.email-broadcast') ? route('admin.email-broadcast') : url('/admin/email-broadcast'), 'icon' => 'bi-send-check', 'label' => 'Send Broadcast Email', 'permission' => 'email.send'],
                ['href' => route('admin.audit-logs'), 'icon' => 'bi-journal-code', 'label' => 'Audit Logs', 'permission' => 'audit.view'],
                ['href' => route('admin.reports'), 'icon' => 'bi-graph-up', 'label' => 'Reports & Analytics', 'permission' => 'reports.view'],
                ['href' => route('support.index'), 'icon' => 'bi-headset', 'label' => 'Support Desk', 'permission' => null],
                ['href' => route('admin.profile'), 'icon' => 'bi-person-badge', 'label' => 'My Profile', 'permission' => null],
                ['href' => route('admin.faqs'), 'icon' => 'bi-question-circle', 'label' => 'FAQs Management', 'permission' => null],
                ['href' => route('admin.settings'), 'icon' => 'bi-sliders', 'label' => 'Settings', 'permission' => 'settings.view'],
            ];

            $navItems = array_values(array_filter($allNavItems, function ($item) use ($user) {
                return empty($item['permission']) || $user->hasPermission($item['permission']);
            }));
            $settingsPath = $user->hasPermission('settings.view') ? route('admin.settings') : '';
        } elseif ($role === 'vendor') {
            $navItems = [
                ['href' => route('vendor.dashboard'), 'icon' => 'bi-grid', 'label' => 'Dashboard'],
                ['href' => route('vendor.clients'), 'icon' => 'bi-people', 'label' => 'Clients'],
                ['href' => route('vendor.requests'), 'icon' => 'bi-clipboard-data', 'label' => 'Client Requests'],
                ['href' => route('vendor.my-services'), 'icon' => 'bi-box-seam', 'label' => 'My Services'],
                ['href' => route('vendor.services'), 'icon' => 'bi-bag-plus', 'label' => 'Add Services'],
                ['href' => route('vendor.settings'), 'icon' => 'bi-sliders', 'label' => 'Settings'],
            ];
            $settingsPath = route('vendor.settings');
        } elseif ($role === 'client') {
            $navItems = [
                ['href' => route('client.dashboard'), 'icon' => 'bi-grid', 'label' => 'Dashboard'],
                ['href' => route('client.requests'), 'icon' => 'bi-clipboard-data', 'label' => 'My Requests'],
                ['href' => route('client.services'), 'icon' => 'bi-search', 'label' => 'Browse Services'],
                ['href' => route('support.index'), 'icon' => 'bi-headset', 'label' => 'Support Tickets'],
                ['href' => route('client.settings'), 'icon' => 'bi-sliders', 'label' => 'Settings'],
            ];
            $settingsPath = route('client.settings');
        }
    }
?>

<div class="d-flex h-100 w-100 overflow-hidden" style="position: fixed; inset: 0;">
    <!-- Sidebar Navigation (Desktop) -->
    <aside class="bg-white border-end d-none d-md-flex flex-column flex-shrink-0 p-3 h-100" style="width: 280px; z-index: 1040; overflow-y: auto;">
        <div class="mb-4 px-3">
            <a href="/" class="d-flex align-items-center gap-2 text-decoration-none text-dark">
                <?php if($settings && $settings->logo_url): ?>
                    <img src="<?php echo e(app_file_url($settings->logo_url)); ?>" alt="Logo" class="rounded" style="height: 32px; width: 32px; object-fit: contain;">
                <?php else: ?>
                    <svg width="30" height="30" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg" class="me-1">
                        <!-- Stylized D (Gold) -->
                        <path d="M20 20C40 20 52 32 52 50C52 68 40 80 20 80C14 80 14 74 14 74V26C14 26 14 20 20 20Z" fill="url(#goldLogoDash)" />
                        <path d="M28 32C38 32 44 40 44 50C44 60 38 68 28 68V32Z" fill="#FFFFFF" />
                        <!-- Stylized E (Dark Green) -->
                        <path d="M50 24H80V35H64V44H76V53H64V63H80V74H50V24Z" fill="#004225" />
                        <defs>
                            <linearGradient id="goldLogoDash" x1="14" y1="20" x2="52" y2="80" gradientUnits="userSpaceOnUse">
                                <stop offset="0%" stop-color="#FFE57F" />
                                <stop offset="50%" stop-color="#D4AF37" />
                                <stop offset="100%" stop-color="#AA820A" />
                            </linearGradient>
                        </defs>
                    </svg>
                <?php endif; ?>
                <span class="fw-bold text-dark tracking-tight" style="font-size: 14.5px;"><?php echo e($settings->platform_name ?? 'DOOTOR ENTERPRISES'); ?></span>
            </a>
        </div>
        
        <nav class="nav flex-column gap-1">
            <?php $__currentLoopData = $navItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e($item['href']); ?>" class="sidebar-link <?php echo e(request()->url() == $item['href'] ? 'active' : ''); ?>">
                    <i class="bi <?php echo e($item['icon']); ?> fs-5"></i>
                    <span><?php echo e($item['label']); ?></span>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </nav>

        <div class="mt-auto pt-3 border-top border-light">
            <div class="p-2 d-flex align-items-center gap-2 sidebar-profile mb-2">
                <?php if($user->avatar_url): ?>
                    <img src="<?php echo e(app_file_url($user->avatar_url)); ?>" alt="avatar" class="rounded-circle" style="width: 32px; height: 32px; object-fit: cover;">
                <?php else: ?>
                    <div class="rounded-circle text-dark d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; font-size: 13px; background-color: #d4af37 !important;">
                        <?php echo e(strtoupper(substr($user->first_name ?? 'U', 0, 1))); ?><?php echo e(strtoupper(substr($user->last_name ?? 'M', 0, 1))); ?>

                    </div>
                <?php endif; ?>
                <div class="overflow-hidden">
                    <span class="d-block text-dark fw-semibold small text-truncate" style="font-size: 12px;"><?php echo e($user->first_name); ?> <?php echo e($user->last_name); ?></span>
                    <span class="d-block text-secondary small text-truncate" style="font-size: 10.5px;"><?php echo e(ucfirst($role)); ?></span>
                </div>
            </div>
            <form action="<?php echo e(route('logout')); ?>" method="POST" class="m-0">
                <?php echo csrf_field(); ?>
                <button class="btn btn-link sidebar-link w-100 text-start border-0 bg-transparent text-danger p-2" type="submit" style="color: #ef4444 !important;">
                    <i class="bi bi-box-arrow-right text-danger me-2" style="color: #ef4444 !important;"></i>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Content Area (Independently Scrollable) -->
    <div id="dashboardMainScroll" class="flex-grow-1 bg-light d-flex flex-column h-100 overflow-y-auto" style="min-width: 0; background-color: #f8fafc !important;">
        <!-- Header -->
        <header class="navbar navbar-expand bg-white border-bottom px-3 px-md-4 py-2 sticky-top" style="z-index: 1030;">
            <div class="container-fluid p-0 d-flex align-items-center justify-content-between">
                <?php if($role === 'client'): ?>
                    <!-- Client Mobile Brand Header (Exact match to screenshot) -->
                    <div class="d-flex align-items-center gap-2 d-md-none">
                        <a href="<?php echo e(route('client.dashboard')); ?>" class="d-flex align-items-center gap-2 text-decoration-none text-dark">
                            <?php if($settings && $settings->logo_url): ?>
                                <img src="<?php echo e(app_file_url($settings->logo_url)); ?>" alt="Logo" class="rounded" style="height: 30px; width: 30px; object-fit: contain;">
                            <?php else: ?>
                                <svg width="28" height="28" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg" class="me-1">
                                    <!-- Stylized D (Gold) -->
                                    <path d="M20 20C40 20 52 32 52 50C52 68 40 80 20 80C14 80 14 74 14 74V26C14 26 14 20 20 20Z" fill="url(#goldLogoNavMobile)" />
                                    <path d="M28 32C38 32 44 40 44 50C44 60 38 68 28 68V32Z" fill="#FFFFFF" />
                                    <!-- Stylized E (Dark Green) -->
                                    <path d="M50 24H80V35H64V44H76V53H64V63H80V74H50V24Z" fill="#004225" />
                                    <defs>
                                        <linearGradient id="goldLogoNavMobile" x1="14" y1="20" x2="52" y2="80" gradientUnits="userSpaceOnUse">
                                            <stop offset="0%" stop-color="#FFE57F" />
                                            <stop offset="50%" stop-color="#D4AF37" />
                                            <stop offset="100%" stop-color="#AA820A" />
                                        </linearGradient>
                                    </defs>
                                </svg>
                            <?php endif; ?>
                            <span class="fw-bolder text-dark tracking-tight text-uppercase" style="font-size: 14.5px; font-weight: 800; letter-spacing: -0.01em;"><?php echo e($settings->platform_name ?? 'DOOTOR ENTERPRISES'); ?></span>
                        </a>
                    </div>
                    <!-- Desktop Spacer -->
                    <div class="d-none d-md-block"></div>
                <?php else: ?>
                    <!-- Mobile Toggle button for Admin/Vendor -->
                    <button class="btn btn-outline-secondary d-md-none me-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar" aria-controls="mobileSidebar">
                        <i class="bi bi-list fs-4"></i>
                    </button>
                    <div class="d-none d-md-block"></div>
                <?php endif; ?>

                <div class="d-flex align-items-center gap-2 gap-sm-3">
                    <button class="header-bell-btn position-relative border-0" type="button" title="Notifications" style="width: 36px !important; height: 36px !important; border-radius: 50% !important; background: #ffffff !important; border: 1px solid #e2e8f0 !important; display: inline-flex !important; align-items: center !important; justify-content: center !important; outline: none !important; box-shadow: none !important; padding: 0 !important; color: #1e293b !important; cursor: pointer !important;">
                        <i class="bi bi-bell" style="font-size: 15px; color: #334155;"></i>
                        <span class="position-absolute rounded-circle" style="top: 7px; right: 7px; width: 7.5px; height: 7.5px; background-color: #f59e0b; border: 1.5px solid #ffffff;"></span>
                    </button>
                    
                    <div class="dropdown">
                        <button class="btn btn-link p-0 d-flex align-items-center gap-1.5 gap-sm-2 text-decoration-none text-dark dropdown-toggle border-0 shadow-none" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="border: none !important; outline: none !important; background: transparent !important; text-decoration: none !important; box-shadow: none !important;">
                            <?php if($user->avatar_url): ?>
                                <img src="<?php echo e(app_file_url($user->avatar_url)); ?>" alt="avatar" class="rounded-circle" style="width: 34px; height: 34px; object-fit: cover;">
                            <?php else: ?>
                                <div class="rounded-circle text-dark d-flex align-items-center justify-content-center fw-bold shadow-xs" style="width: 34px; height: 34px; font-size: 12.5px; background-color: #d4af37 !important; color: #000000 !important; flex-shrink: 0;">
                                    <?php echo e(strtoupper(substr($user->first_name ?? 'U', 0, 1))); ?><?php echo e(strtoupper(substr($user->last_name ?? 'M', 0, 1))); ?>

                                </div>
                            <?php endif; ?>
                            <span class="fw-semibold text-dark text-truncate d-none d-sm-inline-block" style="font-size: 13.5px; max-width: 140px;"><?php echo e($user->first_name ?? ''); ?> <?php echo e($user->last_name ?? ''); ?></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2 rounded-3">
                            <?php if($role === 'admin'): ?>
                                <li><a class="dropdown-item" href="<?php echo e(route('admin.profile')); ?>"><i class="bi bi-person-badge me-2"></i> Admin Profile</a></li>
                            <?php endif; ?>
                            <?php if(!$isPendingVendor && $settingsPath): ?>
                                <li><a class="dropdown-item" href="<?php echo e($settingsPath); ?>"><i class="bi bi-gear me-2"></i> Settings</a></li>
                            <?php endif; ?>
                            <li><a class="dropdown-item" href="<?php echo e(route('support.index')); ?>"><i class="bi bi-question-circle me-2"></i> Support Desk</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="<?php echo e(route('logout')); ?>" method="POST" class="m-0">
                                    <?php echo csrf_field(); ?>
                                    <button class="dropdown-item text-danger" type="submit"><i class="bi bi-box-arrow-right me-2"></i> Logout</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Dashboard View Content -->
        <main class="container-fluid px-3 px-md-4 py-3 py-md-4 d-flex flex-column <?php echo e($role === 'client' ? 'pb-5 mb-5 pb-md-4 mb-md-0' : ''); ?>" style="min-height: calc(100vh - 60px); padding-bottom: 80px !important;">
            <div class="flex-grow-1">
                <?php echo $__env->yieldContent('content'); ?>
            </div>
            <footer class="mt-4 pt-3 border-top text-muted small d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2 opacity-75">
                <div>&copy; <?php echo e(date('Y')); ?> <?php echo e($settings->platform_name ?? 'DOOTOR ENTERPRISES'); ?>. All rights reserved.</div>
                <div>Developed by <a href="https://kisprojectslab.com" target="_blank" rel="noopener noreferrer" class="text-dark fw-semibold text-decoration-none border-bottom">KendatTech</a></div>
            </footer>
        </main>
    </div>
</div>

<!-- Mobile Bottom Navigation Bar (Client Portal - Strictly Mobile Only < 768px) -->
<?php if($role === 'client'): ?>
<style>
    @media (min-width: 768px) {
        .mobile-bottom-nav,
        div.mobile-bottom-nav,
        nav.mobile-bottom-nav {
            display: none !important;
            visibility: hidden !important;
            opacity: 0 !important;
            pointer-events: none !important;
            height: 0 !important;
            width: 0 !important;
            overflow: hidden !important;
        }
    }
</style>
<div class="mobile-bottom-nav d-md-none">
    <div class="mobile-bottom-nav-inner">
        <!-- Dashboard Tab -->
        <a href="<?php echo e(route('client.dashboard')); ?>" class="mobile-nav-item <?php echo e(request()->routeIs('client.dashboard') ? 'active' : ''); ?>">
            <div class="mobile-nav-icon-wrap">
                <?php if(request()->routeIs('client.dashboard')): ?>
                    <div class="active-grid-icon">
                        <svg width="17" height="17" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="2.5" y="2.5" width="6" height="6" rx="1.5" fill="#ffffff" />
                            <rect x="11.5" y="2.5" width="6" height="6" rx="1.5" fill="#ffffff" />
                            <rect x="2.5" y="11.5" width="6" height="6" rx="1.5" fill="#ffffff" />
                            <rect x="11.5" y="11.5" width="6" height="6" rx="1.5" fill="#ffffff" />
                        </svg>
                    </div>
                <?php else: ?>
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="2.5" y="2.5" width="6" height="6" rx="1.5" fill="#64748b" />
                        <rect x="11.5" y="2.5" width="6" height="6" rx="1.5" fill="#64748b" />
                        <rect x="2.5" y="11.5" width="6" height="6" rx="1.5" fill="#64748b" />
                        <rect x="11.5" y="11.5" width="6" height="6" rx="1.5" fill="#64748b" />
                    </svg>
                <?php endif; ?>
            </div>
            <span class="mobile-nav-label">Dashboard</span>
            <?php if(request()->routeIs('client.dashboard')): ?>
                <div class="mobile-nav-indicator"></div>
            <?php endif; ?>
        </a>

        <!-- My Requests Tab -->
        <a href="<?php echo e(route('client.requests')); ?>" class="mobile-nav-item <?php echo e(request()->routeIs('client.requests*') || request()->routeIs('client.request.*') ? 'active' : ''); ?>">
            <div class="mobile-nav-icon-wrap">
                <?php if(request()->routeIs('client.requests*') || request()->routeIs('client.request.*')): ?>
                    <div class="active-grid-icon">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="9" y1="17" x2="9" y2="13"></line>
                            <line x1="12" y1="17" x2="12" y2="10"></line>
                            <line x1="15" y1="17" x2="15" y2="15"></line>
                        </svg>
                    </div>
                <?php else: ?>
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="9" y1="17" x2="9" y2="13"></line>
                        <line x1="12" y1="17" x2="12" y2="10"></line>
                        <line x1="15" y1="17" x2="15" y2="15"></line>
                    </svg>
                <?php endif; ?>
            </div>
            <span class="mobile-nav-label">My Requests</span>
            <?php if(request()->routeIs('client.requests*') || request()->routeIs('client.request.*')): ?>
                <div class="mobile-nav-indicator"></div>
            <?php endif; ?>
        </a>

        <!-- Browse Services Tab -->
        <a href="<?php echo e(route('client.services')); ?>" class="mobile-nav-item <?php echo e(request()->routeIs('client.services*') || request()->routeIs('client.book*') || request()->routeIs('client.application*') ? 'active' : ''); ?>">
            <div class="mobile-nav-icon-wrap">
                <?php if(request()->routeIs('client.services*') || request()->routeIs('client.book*') || request()->routeIs('client.application*')): ?>
                    <div class="active-grid-icon">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                    </div>
                <?php else: ?>
                    <svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                <?php endif; ?>
            </div>
            <span class="mobile-nav-label">Browse Services</span>
            <?php if(request()->routeIs('client.services*') || request()->routeIs('client.book*') || request()->routeIs('client.application*')): ?>
                <div class="mobile-nav-indicator"></div>
            <?php endif; ?>
        </a>

        <!-- Support Tickets Tab -->
        <a href="<?php echo e(route('support.index')); ?>" class="mobile-nav-item <?php echo e(request()->routeIs('support*') ? 'active' : ''); ?>">
            <div class="mobile-nav-icon-wrap">
                <?php if(request()->routeIs('support*')): ?>
                    <div class="active-grid-icon">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                        </svg>
                    </div>
                <?php else: ?>
                    <svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                    </svg>
                <?php endif; ?>
            </div>
            <span class="mobile-nav-label">Support Tickets</span>
            <?php if(request()->routeIs('support*')): ?>
                <div class="mobile-nav-indicator"></div>
            <?php endif; ?>
        </a>

        <!-- Settings Tab -->
        <a href="<?php echo e(route('client.settings')); ?>" class="mobile-nav-item <?php echo e(request()->routeIs('client.settings*') ? 'active' : ''); ?>">
            <div class="mobile-nav-icon-wrap">
                <?php if(request()->routeIs('client.settings*')): ?>
                    <div class="active-grid-icon">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="4" y1="6" x2="20" y2="6"></line>
                            <circle cx="9" cy="6" r="2.5"></circle>
                            <line x1="4" y1="12" x2="20" y2="12"></line>
                            <circle cx="15" cy="12" r="2.5"></circle>
                            <line x1="4" y1="18" x2="20" y2="18"></line>
                            <circle cx="8" cy="18" r="2.5"></circle>
                        </svg>
                    </div>
                <?php else: ?>
                    <svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="4" y1="6" x2="20" y2="6"></line>
                        <circle cx="9" cy="6" r="2.5"></circle>
                        <line x1="4" y1="12" x2="20" y2="12"></line>
                        <circle cx="15" cy="12" r="2.5"></circle>
                        <line x1="4" y1="18" x2="20" y2="18"></line>
                        <circle cx="8" cy="18" r="2.5"></circle>
                    </svg>
                <?php endif; ?>
            </div>
            <span class="mobile-nav-label">Settings</span>
            <?php if(request()->routeIs('client.settings*')): ?>
                <div class="mobile-nav-indicator"></div>
            <?php endif; ?>
        </a>
    </div>
</div>
<?php endif; ?>

<!-- Mobile Sidebar Drawer (Offcanvas) -->
<div class="offcanvas offcanvas-start bg-light border-0" tabindex="-1" id="mobileSidebar" aria-labelledby="mobileSidebarLabel" style="width: 280px; z-index: 1060 !important;">
    <div class="offcanvas-header border-bottom border-light">
        <h5 class="offcanvas-title d-flex align-items-center gap-2 text-dark" id="mobileSidebarLabel">
            <?php if($settings && $settings->logo_url): ?>
                <img src="<?php echo e(app_file_url($settings->logo_url)); ?>" alt="Logo" class="rounded" style="height: 28px; width: 28px; object-fit: contain;">
            <?php else: ?>
                <svg width="26" height="26" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg" class="me-1">
                    <!-- Stylized D (Gold) -->
                    <path d="M20 20C40 20 52 32 52 50C52 68 40 80 20 80C14 80 14 74 14 74V26C14 26 14 20 20 20Z" fill="url(#goldLogoDashMobile)" />
                    <path d="M28 32C38 32 44 40 44 50C44 60 38 68 28 68V32Z" fill="#FFFFFF" />
                    <!-- Stylized E (Dark Green) -->
                    <path d="M50 24H80V35H64V44H76V53H64V63H80V74H50V24Z" fill="#004225" />
                    <defs>
                        <linearGradient id="goldLogoDashMobile" x1="14" y1="20" x2="52" y2="80" gradientUnits="userSpaceOnUse">
                            <stop offset="0%" stop-color="#FFE57F" />
                            <stop offset="50%" stop-color="#D4AF37" />
                            <stop offset="100%" stop-color="#AA820A" />
                        </linearGradient>
                    </defs>
                </svg>
            <?php endif; ?>
            <span class="fw-bold"><?php echo e($settings->platform_name ?? 'DOOTOR ENTERPRISES'); ?></span>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body d-flex flex-column py-3 px-2">
        <nav class="nav flex-column gap-1">
            <?php $__currentLoopData = $navItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e($item['href']); ?>" class="sidebar-link <?php echo e(request()->url() == $item['href'] ? 'active' : ''); ?>">
                    <i class="bi <?php echo e($item['icon']); ?> fs-5"></i>
                    <span><?php echo e($item['label']); ?></span>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </nav>
        
        <div class="mt-auto pt-3 border-top border-light px-2">
            <div class="p-2 d-flex align-items-center gap-2 sidebar-profile mb-2">
                <?php if($user->avatar_url): ?>
                    <img src="<?php echo e(app_file_url($user->avatar_url)); ?>" alt="avatar" class="rounded-circle" style="width: 32px; height: 32px; object-fit: cover;">
                <?php else: ?>
                    <div class="rounded-circle text-dark d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; font-size: 13px; background-color: #d4af37 !important;">
                        <?php echo e(strtoupper(substr($user->first_name ?? 'U', 0, 1))); ?><?php echo e(strtoupper(substr($user->last_name ?? 'M', 0, 1))); ?>

                    </div>
                <?php endif; ?>
                <div class="overflow-hidden">
                    <span class="d-block text-dark fw-semibold small text-truncate" style="font-size: 12px;"><?php echo e($user->first_name); ?> <?php echo e($user->last_name); ?></span>
                    <span class="d-block text-secondary small text-truncate" style="font-size: 10.5px;"><?php echo e(ucfirst($role)); ?></span>
                </div>
            </div>
            <form action="<?php echo e(route('logout')); ?>" method="POST" class="m-0">
                <?php echo csrf_field(); ?>
                <button class="btn btn-link sidebar-link w-100 text-start border-0 bg-transparent text-danger p-2" type="submit" style="color: #ef4444 !important;">
                    <i class="bi bi-box-arrow-right text-danger me-2" style="color: #ef4444 !important;"></i>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\DOOTOR ENTERPRISES\resources\views\layouts\dashboard.blade.php ENDPATH**/ ?>