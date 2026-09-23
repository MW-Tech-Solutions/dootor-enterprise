@extends('layouts.app')

@section('styles')
<style>
    .sidebar-link {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 11px 16px;
        border-radius: 10px;
        color: #475569 !important;
        text-decoration: none;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        font-weight: 500;
        margin-bottom: 3px;
        white-space: nowrap;
    }

    .sidebar-link span {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .sidebar-link i {
        color: #64748b !important;
        transition: color 0.2s ease;
    }

    .sidebar-link:hover {
        background-color: #f1f5f9 !important;
        color: #004225 !important;
    }

    .sidebar-link:hover i {
        color: #004225 !important;
    }

    .sidebar-link.active {
        background-color: #004225 !important;
        color: #ffffff !important;
        font-weight: 600;
        box-shadow: 0 4px 12px rgba(0, 66, 37, 0.25) !important;
    }

    .sidebar-link.active span {
        color: #ffffff !important;
    }

    .sidebar-link.active i {
        color: #d4af37 !important;
    }
    
    .sidebar-profile {
        background: #ffffff;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }
</style>
@endsection

@section('body')
@php
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
        if ($role === 'admin') {
            $allNavItems = [
                ['href' => route('admin.dashboard'), 'icon' => 'bi-grid', 'label' => 'Dashboard', 'permission' => null],
                ['href' => route('admin.payment-verification'), 'icon' => 'bi-credit-card-2-front', 'label' => 'Payment Verification', 'permission' => 'applications.view'],
                ['href' => route('admin.work-queue'), 'icon' => 'bi-clock-history', 'label' => 'Staff Work Queue', 'permission' => 'applications.view'],
                ['href' => route('admin.subscriptions'), 'icon' => 'bi-file-earmark-check', 'label' => 'Service Applications', 'permission' => 'applications.view'],
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
@endphp

@section('body')
<div class="d-flex h-100 w-100 overflow-hidden" style="position: fixed; inset: 0;">
    <!-- Sidebar Navigation (Desktop) -->
    <aside class="bg-white border-end d-none d-md-flex flex-column flex-shrink-0 p-3 h-100" style="width: 280px; z-index: 1040; overflow-y: auto;">
        <div class="mb-4 px-3">
            <a href="/" class="d-flex align-items-center gap-2 text-decoration-none text-dark">
                @if($settings && $settings->logo_url)
                    <img src="{{ app_file_url($settings->logo_url) }}" alt="Logo" class="rounded" style="height: 32px; width: 32px; object-fit: contain;">
                @else
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
                @endif
                <span class="fw-bold text-dark tracking-tight" style="font-size: 14.5px;">{{ $settings->platform_name ?? 'DOOTOR ENTERPRISES' }}</span>
            </a>
        </div>
        
        <nav class="nav flex-column gap-1">
            @foreach($navItems as $item)
                <a href="{{ $item['href'] }}" class="sidebar-link {{ request()->url() == $item['href'] ? 'active' : '' }}">
                    <i class="bi {{ $item['icon'] }} fs-5"></i>
                    <span>{{ $item['label'] }}</span>
                </a>
            @endforeach
        </nav>

        <div class="mt-auto pt-3 border-top border-light">
            <div class="p-2 d-flex align-items-center gap-2 sidebar-profile mb-2">
                @if($user->avatar_url)
                    <img src="{{ app_file_url($user->avatar_url) }}" alt="avatar" class="rounded-circle" style="width: 32px; height: 32px; object-fit: cover;">
                @else
                    <div class="rounded-circle text-dark d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; font-size: 13px; background-color: #d4af37 !important;">
                        {{ strtoupper(substr($user->first_name ?? 'U', 0, 1)) }}{{ strtoupper(substr($user->last_name ?? 'M', 0, 1)) }}
                    </div>
                @endif
                <div class="overflow-hidden">
                    <span class="d-block text-dark fw-semibold small text-truncate" style="font-size: 12px;">{{ $user->first_name }} {{ $user->last_name }}</span>
                    <span class="d-block text-secondary small text-truncate" style="font-size: 10.5px;">{{ ucfirst($role) }}</span>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST" class="m-0">
                @csrf
                <button class="btn btn-link sidebar-link w-100 text-start border-0 bg-transparent text-danger p-2" type="submit" style="color: #ef4444 !important;">
                    <i class="bi bi-box-arrow-right text-danger me-2" style="color: #ef4444 !important;"></i>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Content Area (Independently Scrollable) -->
    <div class="flex-grow-1 bg-light d-flex flex-column h-100 overflow-y-auto" style="min-width: 0;">
        <!-- Header -->
        <header class="navbar navbar-expand bg-white border-bottom px-4 py-2 sticky-top">
            <div class="container-fluid p-0">
                <!-- Mobile Toggle button -->
                <button class="btn btn-outline-secondary d-md-none me-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar" aria-controls="mobileSidebar">
                    <i class="bi bi-list fs-4"></i>
                </button>

                <div class="ms-auto d-flex align-items-center gap-3">
                    <button class="btn btn-outline-secondary btn-sm position-relative rounded-circle p-2" style="width: 38px; height: 38px;">
                        <i class="bi bi-bell"></i>
                    </button>
                    
                    <div class="dropdown">
                        <button class="btn btn-link p-0 d-flex align-items-center gap-2 text-decoration-none text-dark dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            @if($user->avatar_url)
                                <img src="{{ app_file_url($user->avatar_url) }}" alt="avatar" class="rounded-circle" style="width: 32px; height: 32px; object-fit: cover;">
                            @else
                                <div class="rounded-circle bg-gradient text-white d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; font-size: 14px;">
                                    {{ strtoupper(substr($user->first_name ?? 'U', 0, 1)) }}{{ strtoupper(substr($user->last_name ?? 'M', 0, 1)) }}
                                </div>
                            @endif
                            <span class="d-none d-sm-inline">{{ $user->first_name ?? '' }} {{ $user->last_name ?? '' }}</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                            @if($role === 'admin')
                                <li><a class="dropdown-item" href="{{ route('admin.profile') }}"><i class="bi bi-person-badge me-2"></i> Admin Profile</a></li>
                            @endif
                            @if(!$isPendingVendor && $settingsPath)
                                <li><a class="dropdown-item" href="{{ $settingsPath }}"><i class="bi bi-gear me-2"></i> Settings</a></li>
                            @endif
                            <li><a class="dropdown-item" href="{{ route('support.index') }}"><i class="bi bi-question-circle me-2"></i> Support Desk</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST" class="m-0">
                                    @csrf
                                    <button class="dropdown-item text-danger" type="submit"><i class="bi bi-box-arrow-right me-2"></i> Logout</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Dashboard View Content -->
        <main class="container-fluid p-4 d-flex flex-column" style="min-height: calc(100vh - 60px);">
            <div class="flex-grow-1">
                @yield('content')
            </div>
            <footer class="mt-4 pt-3 border-top text-muted small d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2 opacity-75">
                <div>&copy; {{ date('Y') }} {{ $settings->platform_name ?? 'DOOTOR ENTERPRISES' }}. All rights reserved.</div>
                <div>Developed by <a href="https://kisprojectslab.com" target="_blank" rel="noopener noreferrer" class="text-dark fw-semibold text-decoration-none border-bottom">KendatTech</a></div>
            </footer>
        </main>
    </div>
</div>

<!-- Mobile Sidebar Drawer (Offcanvas) -->
<div class="offcanvas offcanvas-start bg-light border-0" tabindex="-1" id="mobileSidebar" aria-labelledby="mobileSidebarLabel" style="width: 280px; z-index: 1060 !important;">
    <div class="offcanvas-header border-bottom border-light">
        <h5 class="offcanvas-title d-flex align-items-center gap-2 text-dark" id="mobileSidebarLabel">
            @if($settings && $settings->logo_url)
                <img src="{{ app_file_url($settings->logo_url) }}" alt="Logo" class="rounded" style="height: 28px; width: 28px; object-fit: contain;">
            @else
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
            @endif
            <span class="fw-bold">{{ $settings->platform_name ?? 'DOOTOR ENTERPRISES' }}</span>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body d-flex flex-column py-3 px-2">
        <nav class="nav flex-column gap-1">
            @foreach($navItems as $item)
                <a href="{{ $item['href'] }}" class="sidebar-link {{ request()->url() == $item['href'] ? 'active' : '' }}">
                    <i class="bi {{ $item['icon'] }} fs-5"></i>
                    <span>{{ $item['label'] }}</span>
                </a>
            @endforeach
        </nav>
        
        <div class="mt-auto pt-3 border-top border-light px-2">
            <div class="p-2 d-flex align-items-center gap-2 sidebar-profile mb-2">
                @if($user->avatar_url)
                    <img src="{{ app_file_url($user->avatar_url) }}" alt="avatar" class="rounded-circle" style="width: 32px; height: 32px; object-fit: cover;">
                @else
                    <div class="rounded-circle text-dark d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; font-size: 13px; background-color: #d4af37 !important;">
                        {{ strtoupper(substr($user->first_name ?? 'U', 0, 1)) }}{{ strtoupper(substr($user->last_name ?? 'M', 0, 1)) }}
                    </div>
                @endif
                <div class="overflow-hidden">
                    <span class="d-block text-dark fw-semibold small text-truncate" style="font-size: 12px;">{{ $user->first_name }} {{ $user->last_name }}</span>
                    <span class="d-block text-secondary small text-truncate" style="font-size: 10.5px;">{{ ucfirst($role) }}</span>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST" class="m-0">
                @csrf
                <button class="btn btn-link sidebar-link w-100 text-start border-0 bg-transparent text-danger p-2" type="submit" style="color: #ef4444 !important;">
                    <i class="bi bi-box-arrow-right text-danger me-2" style="color: #ef4444 !important;"></i>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
