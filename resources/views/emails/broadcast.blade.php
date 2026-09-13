<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? 'Notice' }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f1f5f9;
            margin: 0;
            padding: 0;
            -webkit-text-size-adjust: 100%;
            color: #334155;
        }
        .email-wrapper {
            width: 100%;
            background-color: #f1f5f9;
            padding: 30px 15px;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            border: 1px solid #e2e8f0;
        }
        .email-header {
            background-color: #004225;
            padding: 24px 30px;
            text-align: center;
            border-bottom: 3px solid #d4af37;
        }
        .email-header img {
            max-height: 42px;
            width: auto;
            vertical-align: middle;
        }
        .brand-title {
            color: #ffffff;
            font-size: 20px;
            font-weight: 700;
            letter-spacing: 0.5px;
            margin-left: 8px;
            vertical-align: middle;
            display: inline-block;
        }
        .email-body {
            padding: 32px 30px;
        }
        .category-badge {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            margin-bottom: 20px;
        }
        .badge-maintenance {
            background-color: #fff9db;
            color: #b45309;
            border: 1px solid #fde047;
        }
        .badge-announcement {
            background-color: #dbeafe;
            color: #1d4ed8;
            border: 1px solid #93c5fd;
        }
        .badge-service_update {
            background-color: #dcfce7;
            color: #15803d;
            border: 1px solid #86efac;
        }
        .badge-custom {
            background-color: #f3e8ff;
            color: #7e22ce;
            border: 1px solid #d8b4fe;
        }
        .headline {
            font-size: 22px;
            font-weight: 800;
            color: #0f172a;
            margin: 0 0 16px 0;
            line-height: 1.3;
        }
        .greeting {
            font-size: 15px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 16px;
        }
        .content-text {
            font-size: 15px;
            line-height: 1.65;
            color: #334155;
            margin-bottom: 24px;
        }
        .btn-wrapper {
            text-align: center;
            margin: 30px 0 20px 0;
        }
        .btn-cta {
            display: inline-block;
            background-color: #004225;
            color: #ffffff !important;
            padding: 13px 32px;
            border-radius: 50px;
            font-size: 14px;
            font-weight: 700;
            text-decoration: none;
            box-shadow: 0 4px 12px rgba(0, 66, 37, 0.25);
            letter-spacing: 0.3px;
        }
        .btn-cta:hover {
            background-color: #002e1a;
        }
        .email-footer {
            background-color: #f8fafc;
            padding: 24px 30px;
            text-align: center;
            border-top: 1px solid #f1f5f9;
            font-size: 12px;
            color: #64748b;
        }
        .email-footer a {
            color: #004225;
            text-decoration: none;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-container">
            <!-- Header -->
            <div class="email-header">
                @if(!empty($logoUrl))
                    <img src="{{ $logoUrl }}" alt="{{ $companyName }}">
                @endif
                <span class="brand-title">{{ $companyName ?? 'DOOTOR ENTERPRISES' }}</span>
            </div>

            <!-- Body -->
            <div class="email-body">
                @php
                    $cat = $category ?? 'custom';
                    $badgeClass = 'badge-' . $cat;
                    $badgeLabel = match($cat) {
                        'maintenance' => '🛠️ System Maintenance Notice',
                        'announcement' => '📢 Platform Announcement',
                        'service_update' => '⚡ Service Update & Alert',
                        default => '✉️ Official Direct Notice'
                    };
                @endphp

                <div class="category-badge {{ $badgeClass }}">
                    {{ $badgeLabel }}
                </div>

                @if(!empty($headline))
                    <h1 class="headline">{{ $headline }}</h1>
                @endif

                <div class="greeting">Hello {{ $recipientName ?? 'Valued Member' }},</div>

                <div class="content-text">
                    {!! nl2br(e($bodyContent ?? '')) !!}
                </div>

                @if(!empty($buttonText) && !empty($buttonUrl))
                    <div class="btn-wrapper">
                        <a href="{{ $buttonUrl }}" class="btn-cta" target="_blank">{{ $buttonText }} &rarr;</a>
                    </div>
                @endif
            </div>

            <!-- Footer -->
            <div class="email-footer">
                <p style="margin: 0 0 8px 0;">&copy; {{ date('Y') }} {{ $companyName ?? 'DOOTOR ENTERPRISES' }}. All rights reserved.</p>
                <p style="margin: 0 0 8px 0;">Secured Portal for Verified Document Handling & Official Services.</p>
                <p style="margin: 0;">Need assistance? <a href="mailto:support@dootor-enterprises.com">Contact Support Desk</a></p>
            </div>
        </div>
    </div>
</body>
</html>
