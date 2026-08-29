<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Contact Enquiry</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f4f6f8; margin: 0; padding: 0; }
        .wrapper { max-width: 600px; margin: 32px auto; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.08); }
        .header { background: #003b1c; padding: 28px 32px; border-bottom: 3px solid #d4af37; }
        .header h1 { color: #d4af37; margin: 0; font-size: 22px; font-weight: 700; }
        .header p { color: rgba(255,255,255,0.7); margin: 4px 0 0; font-size: 13px; }
        .body { padding: 28px 32px; }
        .label { font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 2px; }
        .value { font-size: 15px; color: #1e293b; margin-bottom: 20px; }
        .message-box { background: #f8fafc; border: 1px solid #e2e8f0; border-left: 4px solid #003b1c; border-radius: 8px; padding: 16px 20px; font-size: 14px; color: #374151; line-height: 1.7; white-space: pre-wrap; margin-bottom: 24px; }
        .reply-note { background: #fefce8; border: 1px solid #d4af37; border-radius: 8px; padding: 14px 18px; font-size: 13px; color: #854d0e; margin-bottom: 20px; }
        .reply-note strong { display: block; margin-bottom: 4px; }
        .footer { background: #f8fafc; padding: 18px 32px; border-top: 1px solid #e2e8f0; font-size: 12px; color: #94a3b8; }
        .tag { display: inline-block; background: #003b1c; color: #d4af37; font-size: 11px; font-weight: 600; padding: 3px 10px; border-radius: 20px; margin-bottom: 20px; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <h1>📩 New Website Enquiry</h1>
        <p>Submitted via the DOOTOR ENTERPRISES contact form</p>
    </div>
    <div class="body">
        <span class="tag">New Message</span>

        <div class="label">Full Name</div>
        <div class="value">{{ $senderFirstName }} {{ $senderLastName }}</div>

        <div class="label">Email Address</div>
        <div class="value"><a href="mailto:{{ $senderEmail }}" style="color:#003b1c;">{{ $senderEmail }}</a></div>

        <div class="label">Subject</div>
        <div class="value">{{ $enquirySubject }}</div>

        <div class="label">Message</div>
        <div class="message-box">{{ $enquiryMessage }}</div>

        <div class="reply-note">
            <strong>⚡ How to Reply</strong>
            Simply click <strong>Reply</strong> in your email client — your reply will be sent <strong>directly to {{ $senderFirstName }}'s inbox</strong> at <strong>{{ $senderEmail }}</strong>. No copy-paste needed.
        </div>
    </div>
    <div class="footer">
        This message was sent via the contact form at <strong>DOOTOR ENTERPRISES</strong>. &nbsp;|&nbsp; Received: {{ now()->format('D, d M Y, g:i A') }}
    </div>
</div>
</body>
</html>
