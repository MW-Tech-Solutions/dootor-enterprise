<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $statusLabel }}</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f1f5f9;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
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
            border-bottom: 4px solid #d4af37;
            text-align: center;
        }
        .email-body {
            padding: 38px 36px;
        }
        .badge {
            display: inline-block;
            padding: 6px 16px;
            border-radius: 50px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            margin-bottom: 20px;
        }
        .badge-approved {
            background-color: #dcfce7;
            color: #15803d;
            border: 1px solid #86efac;
        }
        .badge-rejected {
            background-color: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fca5a5;
        }
        .headline {
            font-size: 22px;
            font-weight: 800;
            color: #0f172a;
            margin: 0 0 16px 0;
            letter-spacing: -0.2px;
        }
        .content-text {
            font-size: 15px;
            line-height: 1.7;
            color: #334155;
            margin-bottom: 24px;
        }
        .alert-box {
            padding: 18px 22px;
            border-radius: 12px;
            margin: 24px 0;
        }
        .alert-approved {
            background-color: #f0fdf4;
            border-left: 4px solid #16a34a;
            color: #14532d;
        }
        .alert-rejected {
            background-color: #fef2f2;
            border-left: 4px solid #dc2626;
            color: #7f1d1d;
        }
        .email-footer {
            background-color: #f8fafc;
            padding: 26px 32px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
            font-size: 12px;
            color: #64748b;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-container">
            <div class="email-header">
                @if ($logoUrl)
                    <img src="{{ $logoUrl }}" alt="{{ $companyName }}" style="max-height: 42px; width: auto; vertical-align: middle;">
                @endif
                <span style="color: #ffffff; font-size: 20px; font-weight: 800; letter-spacing: 1px; vertical-align: middle; margin-left: 10px;">{{ $companyName }}</span>
            </div>
            <div class="email-body">
                <div class="badge {{ $isApproved ? 'badge-approved' : 'badge-rejected' }}">
                    {{ $statusLabel }}
                </div>
                <h1 class="headline">Hello {{ $vendorName }},</h1>
                <div class="content-text">{{ $message }}</div>

                @if (!$isApproved)
                    <div class="alert-box alert-rejected">
                        <div style="font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; color: #991b1b; margin-bottom: 6px;">Reason for Rejection</div>
                        <div style="font-size: 14px; line-height: 1.6; color: #7f1d1d;">{{ $reason }}</div>
                    </div>
                @else
                    <div class="alert-box alert-approved">
                        <div style="font-size: 14px; font-weight: 700; color: #14532d;">✓ Your vendor account has been successfully verified &amp; activated!</div>
                    </div>
                @endif

                <p style="margin: 28px 0 0 0; font-size: 14px; line-height: 1.6; color: #475569;">
                    Regards,<br>
                    <strong style="color: #004225;">{{ $companyName }} Compliance &amp; Verification Team</strong>
                </p>
            </div>
            <div class="email-footer">
                <p style="margin: 0 0 4px 0; font-weight: 600;">&copy; {{ date('Y') }} {{ $companyName }}. All rights reserved.</p>
                <p style="margin: 0;">Automated account decision notice. Please contact support for inquiries.</p>
            </div>
        </div>
    </div>
</body>
</html>
