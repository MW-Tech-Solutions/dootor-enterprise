<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? 'Official Notice' }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #f1f5f9;
            margin: 0;
            padding: 0;
            -webkit-text-size-adjust: 100%;
            color: #1e293b;
        }
        .email-wrapper {
            width: 100%;
            background-color: #f1f5f9;
            padding: 40px 16px;
            box-sizing: border-box;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 12px 32px rgba(0, 46, 26, 0.08);
            border: 1px solid #e2e8f0;
        }
        .email-header {
            background: linear-gradient(135deg, #002e1a 0%, #004225 100%);
            padding: 30px 32px;
            text-align: center;
            border-bottom: 4px solid #d4af37;
        }
        .email-header img {
            max-height: 44px;
            width: auto;
            vertical-align: middle;
        }
        .brand-title {
            color: #ffffff;
            font-size: 21px;
            font-weight: 800;
            letter-spacing: 1px;
            margin-left: 10px;
            vertical-align: middle;
            display: inline-block;
        }
        .email-body {
            padding: 38px 36px;
        }
        .category-badge {
            display: inline-block;
            padding: 6px 16px;
            border-radius: 50px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            margin-bottom: 22px;
        }
        .badge-maintenance {
            background-color: #fef3c7;
            color: #92400e;
            border: 1px solid #fde68a;
        }
        .badge-announcement {
            background-color: #e0f2fe;
            color: #0369a1;
            border: 1px solid #bae6fd;
        }
        .badge-service_update {
            background-color: #dcfce7;
            color: #15803d;
            border: 1px solid #86efac;
        }
        .badge-custom {
            background-color: #f3e8ff;
            color: #6b21a8;
            border: 1px solid #e9d5ff;
        }
        .headline {
            font-size: 22px;
            font-weight: 800;
            color: #0f172a;
            margin: 0 0 18px 0;
            line-height: 1.35;
            letter-spacing: -0.2px;
        }
        .greeting {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 16px;
        }
        .content-text {
            font-size: 15px;
            line-height: 1.7;
            color: #334155;
            margin-bottom: 26px;
        }
        .btn-wrapper {
            text-align: center;
            margin: 32px 0 28px 0;
        }
        .btn-cta {
            display: inline-block;
            background: linear-gradient(135deg, #004225 0%, #002e1a 100%);
            color: #ffffff !important;
            padding: 14px 36px;
            border-radius: 50px;
            font-size: 14px;
            font-weight: 700;
            text-decoration: none;
            box-shadow: 0 6px 18px rgba(0, 66, 37, 0.25);
            letter-spacing: 0.3px;
        }
        .email-footer {
            background-color: #f8fafc;
            padding: 26px 32px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
            font-size: 12px;
            line-height: 1.6;
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

                    $rawBody = $bodyContent ?? '';
                    $plainBody = ltrim(strip_tags($rawBody));
                    $hasGreeting = preg_match('/^(Hello|Dear|Hi|Greetings|Good\s+(morning|afternoon|evening))/i', $plainBody);
                @endphp

                <div class="category-badge {{ $badgeClass }}">
                    {{ $badgeLabel }}
                </div>

                @if(!empty($headline))
                    <h1 class="headline">{{ $headline }}</h1>
                @endif

                @if(!$hasGreeting)
                    <div class="greeting">Hello {{ $recipientName ?? 'Valued Member' }},</div>
                @endif

                <div class="content-text">
                    {!! nl2br(e($rawBody)) !!}
                </div>

                @if(!empty($buttonText) && !empty($buttonUrl))
                    <div class="btn-wrapper">
                        <a href="{{ $buttonUrl }}" class="btn-cta" target="_blank">{{ $buttonText }} &rarr;</a>
                    </div>
                @endif
            </div>

            <!-- Footer -->
            <div class="email-footer">
                <p style="margin: 0 0 6px 0; font-weight: 600; color: #475569;">&copy; {{ date('Y') }} {{ $companyName ?? 'DOOTOR ENTERPRISES' }}. All rights reserved.</p>
                <p style="margin: 0 0 6px 0; color: #64748b;">Secured Portal for Verified Document Handling &amp; Official Services.</p>
                <p style="margin: 0;">Need assistance? <a href="mailto:support@dootor-enterprises.com">support@dootor-enterprises.com</a></p>
            </div>
        </div>
    </div>
</body>
</html>
