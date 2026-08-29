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
    <title>@yield('title', $platformName)</title>
    
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
            background: linear-gradient(135deg, var(--brand-gradient-from), var(--brand-gradient-to));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 16px;
            border-radius: 8px;
            color: #64748b;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .sidebar-link:hover {
            background-color: #f1f5f9;
            color: #0f172a;
        }

        .sidebar-link.active {
            background-color: #f1f5f9;
            color: hsl(var(--primary, 209, 84%, 79%));
            font-weight: 600;
        }

        .dark .sidebar-link {
            color: #94a3b8;
        }

        .dark .sidebar-link:hover {
            background-color: #1e293b;
            color: #f8fafc;
        }

        .dark .sidebar-link.active {
            background-color: #1e293b;
            color: hsl(var(--primary, 209, 84%, 79%));
        }
    </style>
    @yield('styles')
</head>
<body class="bg-light text-dark">
    @yield('body')

    <!-- Global Toast / Alerts -->
    @if(session('success') || session('error'))
    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1050;">
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
                // Animate progress bar shrinking
                setTimeout(function() {
                    if (progressEl) {
                        progressEl.style.width = '0%';
                    }
                }, 50);

                // Auto close and remove toast
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

    @yield('scripts')
</body>
</html>
