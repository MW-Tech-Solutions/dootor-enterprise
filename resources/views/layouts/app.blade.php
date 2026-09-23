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
        <link rel="apple-touch-icon" href="{{ app_file_url($logoUrl) }}">
    @endif

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

        .modal-backdrop {
            z-index: 1050 !important;
        }

        .modal {
            z-index: 1060 !important;
        }
    </style>
    @yield('styles')
</head>
<body class="bg-light text-dark">
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
        <!-- Go To Top Button (Appears on scroll > 250px) -->
        <button id="scrollToTopBtn" 
                type="button" 
                class="btn rounded-circle shadow-lg d-flex align-items-center justify-content-center p-0" 
                style="width: 48px; height: 48px; background-color: #004225; border: 1.5px solid rgba(212, 175, 55, 0.6); color: #ffffff; opacity: 0; visibility: hidden; transform: translateY(12px); transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); cursor: pointer;"
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
            var dashScroll = document.querySelector('.overflow-y-auto');
            if (dashScroll) {
                dashScroll.scrollTo({ top: 0, behavior: 'smooth' });
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            var btn = document.getElementById('scrollToTopBtn');
            if (!btn) return;

            function checkScrollPosition() {
                var scTop = window.scrollY || document.documentElement.scrollTop || 0;
                var dashScroll = document.querySelector('.overflow-y-auto');
                if (dashScroll && dashScroll.scrollTop > scTop) {
                    scTop = dashScroll.scrollTop;
                }

                if (scTop > 250) {
                    btn.style.opacity = '1';
                    btn.style.visibility = 'visible';
                    btn.style.transform = 'translateY(0)';
                } else {
                    btn.style.opacity = '0';
                    btn.style.visibility = 'hidden';
                    btn.style.transform = 'translateY(12px)';
                }
            }

            window.addEventListener('scroll', checkScrollPosition, { passive: true });
            var dashScroll = document.querySelector('.overflow-y-auto');
            if (dashScroll) {
                dashScroll.addEventListener('scroll', checkScrollPosition, { passive: true });
            }
        });
    </script>

    @yield('scripts')
    @stack('scripts')
</body>
</html>
