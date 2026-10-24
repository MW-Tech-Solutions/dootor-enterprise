@php
    $theme = $settings->brand_theme ?? 'light';
    $palette = $settings->brand_color_palette ?? 'default';
    
    // Hex to HSL color helper for CSS variables
    if (!function_exists('hexToHsl')) {
        function hexToHsl($hex) {
            if (!$hex) return null;
            $clean = str_replace('#', '', $hex);
            if (strlen($clean) !== 6) return null;
            $r = hexdec(substr($clean, 0, 2)) / 255;
            $g = hexdec(substr($clean, 2, 2)) / 255;
            $b = hexdec(substr($clean, 4, 2)) / 255;
            $max = max($r, $g, $b);
            $min = min($r, $g, $b);
            $h = 0;
            $s = 0;
            $l = ($max + $min) / 2;
            if ($max !== $min) {
                $d = $max - $min;
                $s = $l > 0.5 ? $d / (2 - $max - $min) : $d / ($max + $min);
                if ($max === $r) $h = ($g - $b) / $d + ($g < $b ? 6 : 0);
                elseif ($max === $g) $h = ($b - $r) / $d + 2;
                elseif ($max === $b) $h = ($r - $g) / $d + 4;
                $h /= 6;
            }
            return round($h * 360) . ' ' . round($s * 100) . '% ' . round($l * 100) . '%';
        }
    }

    $primaryHsl = hexToHsl($settings->brand_primary_color ?? '#93c5fd');
    $secondaryHsl = hexToHsl($settings->brand_secondary_color ?? '#c4b5fd');
    $platformName = $settings->platform_name ?? 'DOOTOR ENTERPRISES';
    $logoUrl = $settings->logo_url ?? null;
@endphp
<!DOCTYPE html>
<html lang="en" class="{{ $theme === 'dark' ? 'dark' : '' }} theme-{{ $palette }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="api-base-url" content="{{ url('/api/location') }}">
    <title>@yield('title', $platformName)</title>
    
    <!-- Dynamic Favicon (Admin Uploaded System Logo) -->
    @if($logoUrl)
        <link rel="icon" href="{{ app_file_url($logoUrl) }}">
        <link rel="shortcut icon" href="{{ app_file_url($logoUrl) }}">
    @else
        <link rel="icon" type="image/png" href="/icons/icon-192x192.png">
    @endif

    <!-- PWA & Mobile Web App Meta Tags (Strict Standalone Mode - Hides Top URL Bar) -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#004225">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="DOOTOR">
    <meta name="application-name" content="{{ $platformName ?? 'DOOTOR ENTERPRISES' }}">
    <meta name="msapplication-TileColor" content="#004225">
    <meta name="msapplication-TileImage" content="/icons/icon-144x144.png">
    
    <!-- iOS Icons & Splash Capabilities -->
    <link rel="apple-touch-icon" href="/icons/apple-touch-icon.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/icons/icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/icons/apple-touch-icon.png">
    <link rel="apple-touch-icon" sizes="192x192" href="/icons/icon-192x192.png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Bootstrap JS Bundle (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>


    <style>
        :root {
            font-family: 'Outfit', sans-serif;
            @if($primaryHsl) --primary: {{ $primaryHsl }}; @endif
            @if($secondaryHsl) --accent: {{ $secondaryHsl }}; @endif
            @if($primaryHsl) --ring: {{ $primaryHsl }}; @endif
            --brand-gradient-from: {{ $settings->brand_gradient_from ?? '#93c5fd' }};
            --brand-gradient-to: {{ $settings->brand_gradient_to ?? '#c4b5fd' }};
        }
        
        body {
            font-family: 'Outfit', sans-serif;
        }
        
        .bg-brand-gradient {
            background-image: linear-gradient(135deg, var(--brand-gradient-from), var(--brand-gradient-to));
        }

        .text-gradient {
            background: linear-gradient(135deg, #004225 0%, #056839 50%, #9e7808 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .text-secondary-contrast {
            color: #1e293b !important;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 16px;
            border-radius: 10px;
            color: #475569 !important;
            text-decoration: none;
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .sidebar-link span {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sidebar-link:hover {
            background-color: #f1f5f9 !important;
            color: #004225 !important;
        }

        .sidebar-link.active {
            background-color: #004225 !important;
            color: #ffffff !important;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(0, 66, 37, 0.25) !important;
        }

        .sidebar-link.active i {
            color: #d4af37 !important;
        }

        .dark .sidebar-link {
            color: #94a3b8 !important;
        }

        .dark .sidebar-link:hover {
            background-color: #1e293b !important;
            color: #f8fafc !important;
        }

        .dark .sidebar-link.active {
            background-color: #004225 !important;
            color: #ffffff !important;
            box-shadow: 0 4px 12px rgba(0, 66, 37, 0.4) !important;
        }

        .sidebar-profile {
            background: #ffffff;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }

        /* Custom spacing helper utilities for high-precision layouts */
        .p-2\.5 { padding: 0.75rem !important; }
        .p-3\.5 { padding: 1.25rem !important; }
        .py-2\.5 { padding-top: 0.65rem !important; padding-bottom: 0.65rem !important; }
        .py-3\.5 { padding-top: 1.15rem !important; padding-bottom: 1.15rem !important; }
        .px-2\.5 { padding-left: 0.75rem !important; padding-right: 0.75rem !important; }
        .px-3\.5 { padding-left: 1.15rem !important; padding-right: 1.15rem !important; }
        .gap-1\.5 { gap: 0.375rem !important; }
        .gap-2\.5 { gap: 0.625rem !important; }
        .gap-3\.5 { gap: 0.875rem !important; }

        /* Global card padding and spacing protection */
        .service-card {
            padding: 20px !important;
        }
        .client-request-card {
            padding: 20px !important;
        }
        @media (min-width: 768px) {
            .service-card {
                padding: 24px !important;
            }
            .client-request-card {
                padding: 24px !important;
            }
        }

        .modal-backdrop {
            z-index: 1050 !important;
        }

        .modal {
            z-index: 1060 !important;
        }

        /* Global Mobile Bottom Navigation Bar Styles - STRICTLY MOBILE ONLY (< 768px) */
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
        @media (max-width: 767.98px) {
            .mobile-bottom-nav {
                position: fixed !important;
                bottom: 0 !important;
                left: 0 !important;
                right: 0 !important;
                width: 100% !important;
                height: 60px !important;
                z-index: 999999 !important;
                background: #ffffff !important;
                border-top: 1px solid #edf2f7 !important;
                box-shadow: 0 -4px 18px rgba(0, 0, 0, 0.05) !important;
                display: flex !important;
                align-items: center !important;
                padding-bottom: env(safe-area-inset-bottom, 0px) !important;
            }
            #globalFloatingWidgets {
                bottom: 75px !important;
                right: 16px !important;
            }
        }
        .mobile-bottom-nav-inner {
            width: 100% !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-around !important;
            padding: 3px 4px !important;
        }
        .mobile-nav-item {
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            justify-content: center !important;
            color: #64748b !important;
            text-decoration: none !important;
            flex: 1 1 0px !important;
            text-align: center !important;
            padding: 2px !important;
            position: relative !important;
            transition: all 0.2s ease !important;
            background: transparent !important;
            border: none !important;
            outline: none !important;
            cursor: pointer !important;
        }
        .mobile-nav-item:hover, .mobile-nav-item:focus {
            color: #004225 !important;
            text-decoration: none !important;
            outline: none !important;
        }
        .mobile-nav-item.active {
            color: #004225 !important;
        }
        .mobile-nav-icon-wrap {
            height: 28px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            margin-bottom: 2px !important;
        }
        .active-grid-icon {
            width: 28px !important;
            height: 28px !important;
            background-color: #004225 !important;
            border-radius: 7px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            box-shadow: 0 2px 6px rgba(0, 66, 37, 0.25) !important;
        }
        .mobile-nav-label {
            font-size: 10.5px !important;
            font-weight: 500 !important;
            line-height: 1.2 !important;
            white-space: nowrap !important;
            display: block !important;
        }
        .mobile-nav-item.active .mobile-nav-label {
            font-weight: 700 !important;
            color: #004225 !important;
        }
        .mobile-nav-indicator {
            width: 22px !important;
            height: 2.5px !important;
            background-color: #004225 !important;
            border-radius: 2px !important;
            margin: 2px auto 0 !important;
        }
        .header-bell-btn {
            width: 36px !important;
            height: 36px !important;
            border-radius: 50% !important;
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            color: #0f172a !important;
            transition: all 0.2s ease !important;
            outline: none !important;
            box-shadow: none !important;
            padding: 0 !important;
        }
        .header-bell-btn:hover, .header-bell-btn:focus, .header-bell-btn:active {
            background: #f8fafc !important;
            border-color: #cbd5e1 !important;
            outline: none !important;
            box-shadow: none !important;
        }
        @media (max-width: 767.98px) {
            #globalFloatingWidgets {
                bottom: 74px !important;
                right: 16px !important;
            }
        }
        /* Native App Preloader (Splash Screen) */
        .app-preloader {
            position: fixed;
            inset: 0;
            width: 100vw;
            height: 100vh;
            background: #ffffff;
            z-index: 99999999;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 1;
            visibility: visible;
            transition: opacity 0.4s cubic-bezier(0.16, 1, 0.3, 1), visibility 0.4s ease;
        }
        .app-preloader.fade-out {
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
        }
        .preloader-content {
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 24px;
        }
        .preloader-logo-wrap {
            width: 90px;
            height: 90px;
            border-radius: 24px;
            background: linear-gradient(135deg, #004225 0%, #0a5c36 100%);
            box-shadow: 0 12px 30px rgba(0, 66, 37, 0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 18px;
            animation: preloaderPulse 1.8s ease-in-out infinite;
        }
        .preloader-logo-img {
            width: 60px;
            height: 60px;
            object-fit: contain;
        }
        .preloader-brand-title {
            font-size: 20px;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: #004225;
            margin: 0 0 5px 0;
        }
        .preloader-tagline {
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 20px;
        }
        .preloader-progress-track {
            width: 150px;
            height: 3.5px;
            background-color: #e2e8f0;
            border-radius: 4px;
            overflow: hidden;
            position: relative;
        }
        .preloader-progress-bar {
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            width: 45%;
            background: linear-gradient(90deg, #004225, #d4af37, #004225);
            border-radius: 4px;
            animation: preloaderShimmer 1.4s ease-in-out infinite;
        }
        @keyframes preloaderPulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.06);
                box-shadow: 0 16px 36px rgba(0, 66, 37, 0.35);
            }
        }
        @keyframes preloaderShimmer {
            0% {
                left: -45%;
            }
            100% {
                left: 100%;
            }
        }
    </style>
    @yield('styles')
</head>
<body class="bg-light text-dark">
    <!-- Native App Preloader (System Logo Splash Screen) -->
    <div id="appPreloader" class="app-preloader">
        <div class="preloader-content">
            <div class="preloader-logo-wrap">
                @if(!empty($logoUrl))
                    <img src="{{ app_file_url($logoUrl) }}" alt="DOOTOR Logo" class="preloader-logo-img" onerror="this.onerror=null; this.src='/icons/icon-192x192.png'; this.onerror=function(){ this.style.display='none'; document.getElementById('preloaderSvgFallback').style.display='block'; };">
                    <svg id="preloaderSvgFallback" width="54" height="54" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg" style="display: none;">
                        <path d="M20 20C40 20 52 32 52 50C52 68 40 80 20 80C14 80 14 74 14 74V26C14 26 14 20 20 20Z" fill="url(#goldLogoPreload)" />
                        <path d="M28 32C38 32 44 40 44 50C44 60 38 68 28 68V32Z" fill="#FFFFFF" />
                        <path d="M50 24H80V35H64V44H76V53H64V63H80V74H50V24Z" fill="#FFFFFF" />
                        <defs>
                            <linearGradient id="goldLogoPreload" x1="14" y1="20" x2="52" y2="80" gradientUnits="userSpaceOnUse">
                                <stop offset="0%" stop-color="#FFE57F" />
                                <stop offset="50%" stop-color="#D4AF37" />
                                <stop offset="100%" stop-color="#AA820A" />
                            </linearGradient>
                        </defs>
                    </svg>
                @else
                    <img src="/icons/icon-192x192.png" alt="DOOTOR Logo" class="preloader-logo-img" onerror="this.style.display='none'; document.getElementById('preloaderSvgFallback').style.display='block';">
                    <svg id="preloaderSvgFallback" width="54" height="54" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg" style="display: none;">
                        <path d="M20 20C40 20 52 32 52 50C52 68 40 80 20 80C14 80 14 74 14 74V26C14 26 14 20 20 20Z" fill="url(#goldLogoPreload)" />
                        <path d="M28 32C38 32 44 40 44 50C44 60 38 68 28 68V32Z" fill="#FFFFFF" />
                        <path d="M50 24H80V35H64V44H76V53H64V63H80V74H50V24Z" fill="#FFFFFF" />
                        <defs>
                            <linearGradient id="goldLogoPreload" x1="14" y1="20" x2="52" y2="80" gradientUnits="userSpaceOnUse">
                                <stop offset="0%" stop-color="#FFE57F" />
                                <stop offset="50%" stop-color="#D4AF37" />
                                <stop offset="100%" stop-color="#AA820A" />
                            </linearGradient>
                        </defs>
                    </svg>
                @endif
            </div>
            <h2 class="preloader-brand-title">{{ $platformName ?? 'DOOTOR ENTERPRISES' }}</h2>
            <span class="preloader-tagline">Consular &amp; Administrative Services</span>
            <div class="preloader-progress-track">
                <div class="preloader-progress-bar"></div>
            </div>
        </div>
    </div>
    @yield('body')

    <!-- Global Toast / Alerts -->
    @if(session('success') || session('error'))
    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1070;">
        <div id="liveToast" class="toast show align-items-center {{ session('success') ? 'text-bg-success' : 'text-bg-danger' }} border-0 position-relative overflow-hidden shadow-lg" role="alert" aria-live="assertive" aria-atomic="true" style="border-radius: 10px;">
            <div class="d-flex py-1">
                <div class="toast-body fw-medium px-3">
                    @if(session('success'))
                        <i class="bi bi-check-circle-fill me-2"></i>
                    @else
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    @endif
                    {{ session('success') ?? session('error') }}
                </div>
                <button type="button" class="btn-close btn-close-white me-3 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <!-- Progress bar visual timer -->
            <div id="toastProgressBar" class="position-absolute bottom-0 start-0 bg-white opacity-50" style="height: 3px; width: 100%; transition: width 3s linear;"></div>
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var toastEl = document.getElementById('liveToast');
            var progressEl = document.getElementById('toastProgressBar');
            
            if (toastEl) {
                setTimeout(function() {
                    if (progressEl) {
                        progressEl.style.width = '0%';
                    }
                }, 50);

                setTimeout(function() {
                    toastEl.style.transition = 'all 0.4s ease';
                    toastEl.style.opacity = '0';
                    toastEl.style.transform = 'translateY(15px)';
                    setTimeout(function() {
                        toastEl.remove();
                    }, 400);
                }, 3050);
            }
        });
    </script>
    @endif

    <!-- Global Bootstrap Modal Stack & Z-Index Auto-Hoisting Fix -->
    <script>
        document.addEventListener('show.bs.modal', function (event) {
            var modal = event.target;
            if (modal && modal.parentNode !== document.body) {
                document.body.appendChild(modal);
            }
        });
    </script>

    <!-- Global Floating Widgets (WhatsApp & Go To Top Button) -->
    <div id="globalFloatingWidgets" class="position-fixed" style="bottom: 25px; right: 25px; z-index: 9990; display: flex; flex-direction: column; gap: 12px; align-items: center;">
        <!-- Go To Top Button (Appears when scrolled > 100px) -->
        <button id="scrollToTopBtn" 
                type="button" 
                class="btn rounded-circle shadow-lg d-flex align-items-center justify-content-center p-0" 
                style="width: 48px; height: 48px; background-color: #004225; border: 1.5px solid rgba(212, 175, 55, 0.6); color: #ffffff; opacity: 0; visibility: hidden; pointer-events: none; transform: translateY(12px); transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); cursor: pointer;"
                title="Scroll to Top"
                onclick="scrollToTop()">
            <i class="bi bi-chevron-up fs-5"></i>
        </button>

        <!-- WhatsApp Floating Widget -->
        <a href="https://wa.me/14164589707?text=Hello%20Dootor%20Enterprises,%20I%20would%20like%20to%20inquire%20about%20your%20services." 
           target="_blank" 
           class="rounded-circle shadow-lg text-white d-flex align-items-center justify-content-center" 
           style="width: 52px; height: 52px; background-color: #25D366; transition: all 0.3s ease; text-decoration: none;"
           onmouseover="this.style.transform='scale(1.1)';" 
           onmouseout="this.style.transform='scale(1)';"
           title="Chat on WhatsApp">
            <i class="bi bi-whatsapp" style="font-size: 28px;"></i>
        </a>
    </div>

    <script>
        function scrollToTop() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
            var dashScroll = document.getElementById('dashboardMainScroll') || document.querySelector('.overflow-y-auto');
            if (dashScroll) {
                dashScroll.scrollTo({ top: 0, behavior: 'smooth' });
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            var btn = document.getElementById('scrollToTopBtn');
            if (!btn) return;

            function checkScrollPosition() {
                var scTop = window.scrollY || document.documentElement.scrollTop || 0;
                var dashScroll = document.getElementById('dashboardMainScroll') || document.querySelector('.overflow-y-auto');
                if (dashScroll && dashScroll.scrollTop > scTop) {
                    scTop = dashScroll.scrollTop;
                }

                if (scTop > 100) {
                    btn.style.opacity = '1';
                    btn.style.visibility = 'visible';
                    btn.style.pointerEvents = 'auto';
                    btn.style.transform = 'translateY(0)';
                } else {
                    btn.style.opacity = '0';
                    btn.style.visibility = 'hidden';
                    btn.style.pointerEvents = 'none';
                    btn.style.transform = 'translateY(12px)';
                }
            }

            // Listen in capture phase so inner overflow container scroll events trigger checkScrollPosition
            window.addEventListener('scroll', checkScrollPosition, { capture: true, passive: true });
            
            var dashScroll = document.getElementById('dashboardMainScroll');
            if (dashScroll) {
                dashScroll.addEventListener('scroll', checkScrollPosition, { passive: true });
            }
        });
    </script>

    <!-- Native App Preloader & PWA Registration Scripts -->
    <script>
        (function() {
            var preloader = document.getElementById('appPreloader');
            function dismissPreloader() {
                if (preloader && !preloader.classList.contains('fade-out')) {
                    preloader.classList.add('fade-out');
                    setTimeout(function() {
                        if (preloader.parentNode) {
                            preloader.parentNode.removeChild(preloader);
                        }
                    }, 450);
                }
            }
            window.addEventListener('load', function() {
                setTimeout(dismissPreloader, 320);
            });
            // Fail-safe timeout so user is never blocked
            setTimeout(dismissPreloader, 1800);
        })();

        // Register Service Worker for PWA
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/sw.js').then(function(reg) {
                    console.log('PWA ServiceWorker registered with scope:', reg.scope);
                }).catch(function(err) {
                    console.warn('PWA ServiceWorker error:', err);
                });
            });
        }

        // PWA Install Prompt Handler
        var deferredPrompt = null;
        window.addEventListener('beforeinstallprompt', function(e) {
            e.preventDefault();
            deferredPrompt = e;
            var installBanner = document.getElementById('pwaInstallBanner');
            if (installBanner && !sessionStorage.getItem('pwa_banner_dismissed')) {
                installBanner.style.display = 'block';
            }
        });

        window.installPwaApp = function() {
            if (deferredPrompt) {
                deferredPrompt.prompt();
                deferredPrompt.userChoice.then(function(choiceResult) {
                    if (choiceResult.outcome === 'accepted') {
                        console.log('User accepted PWA installation');
                    }
                    deferredPrompt = null;
                    var installBanner = document.getElementById('pwaInstallBanner');
                    if (installBanner) installBanner.style.display = 'none';
                });
            } else {
                window.location.href = "{{ route('app.download.apk') }}";
            }
        };

        window.dismissPwaBanner = function() {
            sessionStorage.setItem('pwa_banner_dismissed', 'true');
            var installBanner = document.getElementById('pwaInstallBanner');
            if (installBanner) installBanner.style.display = 'none';
        };
    </script>

    <!-- PWA Install Banner -->
    <div id="pwaInstallBanner" class="position-fixed start-50 translate-middle-x px-3" style="bottom: 74px; z-index: 999998; max-width: 480px; width: 94%; display: none;">
        <div class="card border-0 shadow-lg p-2.5 rounded-4 bg-white" style="border: 1px solid rgba(0,66,37,0.15) !important;">
            <div class="d-flex align-items-center gap-2.5">
                <img src="/icons/icon-192x192.png" alt="App Icon" class="rounded-3 shadow-sm flex-shrink-0" style="width: 42px; height: 42px; object-fit: contain; background: #004225;">
                <div class="flex-grow-1 min-w-0">
                    <h6 class="fw-bold mb-0 text-dark" style="font-size: 13.5px;">Install DOOTOR App</h6>
                    <span class="text-secondary small d-block" style="font-size: 11px;">Instant launch &amp; full screen without URL bar</span>
                </div>
                <button type="button" class="btn btn-sm btn-dark rounded-pill px-3 py-1.5 fw-semibold flex-shrink-0" onclick="installPwaApp()" style="background-color: #004225; border-color: #004225; font-size: 12px;">
                    Install
                </button>
                <button type="button" class="btn-close ms-1 flex-shrink-0" onclick="dismissPwaBanner()" style="font-size: 10px;"></button>
            </div>
        </div>
    </div>

    @yield('scripts')
    @stack('scripts')
</body>
</html>
