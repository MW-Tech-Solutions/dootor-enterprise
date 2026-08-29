<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $statusLabel }}</title>
</head>
<body style="margin:0;background:#f4f7fb;font-family:Arial,Helvetica,sans-serif;color:#111827;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f4f7fb;padding:32px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:640px;background:#ffffff;border-radius:10px;overflow:hidden;border:1px solid #e5e7eb;">
                    <tr>
                        <td style="padding:28px 32px;background:{{ $accent }};color:#ffffff;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td style="vertical-align:middle;">
                                        @if ($logoUrl)
                                            <img src="{{ $logoUrl }}" alt="{{ $companyName }}" style="height:42px;max-width:160px;object-fit:contain;background:#ffffff;border-radius:8px;padding:6px;">
                                        @else
                                            <div style="font-size:20px;font-weight:700;">{{ $companyName }}</div>
                                        @endif
                                    </td>
                                    <td align="right" style="vertical-align:middle;font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;">
                                        {{ $statusLabel }}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:32px;">
                            <h1 style="margin:0 0 12px;font-size:24px;line-height:1.3;color:#111827;">Hello {{ $vendorName }},</h1>
                            <p style="margin:0 0 20px;font-size:16px;line-height:1.7;color:#374151;">{{ $message }}</p>

                            @if (!$isApproved)
                                <div style="margin:24px 0;padding:18px 20px;border-left:4px solid {{ $accent }};background:#fef2f2;border-radius:8px;">
                                    <div style="font-size:13px;font-weight:700;text-transform:uppercase;color:#991b1b;margin-bottom:8px;">Reason for rejection</div>
                                    <div style="font-size:15px;line-height:1.6;color:#7f1d1d;">{{ $reason }}</div>
                                </div>
                            @else
                                <div style="margin:24px 0;padding:18px 20px;border-left:4px solid {{ $accent }};background:#f0fdf4;border-radius:8px;">
                                    <div style="font-size:15px;line-height:1.6;color:#14532d;">Your vendor account is now active.</div>
                                </div>
                            @endif

                            <p style="margin:0;font-size:15px;line-height:1.7;color:#4b5563;">
                                Regards,<br>
                                <strong>{{ $companyName }} Verification Team</strong>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:18px 32px;background:#f9fafb;border-top:1px solid #e5e7eb;font-size:12px;line-height:1.6;color:#6b7280;">
                            This message was sent automatically after an admin reviewed your vendor KYC submission.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
