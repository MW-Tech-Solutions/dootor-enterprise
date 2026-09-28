<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Contact Enquiry</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; background-color: #f1f5f9; margin: 0; padding: 0; color: #1e293b; }
        .wrapper { max-width: 600px; margin: 40px auto; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 12px 32px rgba(0, 46, 26, 0.08); border: 1px solid #e2e8f0; }
        .header { background: linear-gradient(135deg, #002e1a 0%, #004225 100%); padding: 30px 32px; border-bottom: 4px solid #d4af37; text-align: center; }
        .header h1 { color: #ffffff; margin: 0; font-size: 21px; font-weight: 800; letter-spacing: 0.5px; }
        .header p { color: #d4af37; margin: 6px 0 0; font-size: 13px; font-weight: 600; }
        .body { padding: 36px 32px; }
        .tag { display: inline-block; background-color: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; font-size: 11px; font-weight: 700; padding: 5px 14px; border-radius: 50px; text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 24px; }
        .label { font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 4px; }
        .value { font-size: 15px; color: #0f172a; font-weight: 600; margin-bottom: 20px; }
        .message-box { background-color: #f8fafc; border: 1px solid #e2e8f0; border-left: 4px solid #004225; border-radius: 10px; padding: 18px 22px; font-size: 14px; color: #334155; line-height: 1.7; white-space: pre-wrap; margin-bottom: 24px; }
        .reply-note { background-color: #fefce8; border: 1px solid #fde047; border-radius: 10px; padding: 16px 20px; font-size: 13px; color: #854d0e; margin-bottom: 24px; }
        .reply-note strong { display: block; margin-bottom: 4px; color: #713f12; }
        .footer { background-color: #f8fafc; padding: 24px 32px; border-top: 1px solid #e2e8f0; font-size: 12px; color: #64748b; text-align: center; line-height: 1.6; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <h1>📩 New Website Enquiry</h1>
        <p>DOOTOR ENTERPRISES — Contact Form Submission</p>
    </div>
    <div class="body">
        <span class="tag">New Message</span>

        <div class="label">Full Name</div>
        <div class="value">{{ $senderFirstName }} {{ $senderLastName }}</div>

        <div class="label">Email Address</div>
        <div class="value"><a href="mailto:{{ $senderEmail }}" style="color:#004225; text-decoration: underline;">{{ $senderEmail }}</a></div>

        <div class="label">Subject</div>
        <div class="value">{{ $enquirySubject }}</div>

        <div class="label">Message Content</div>
        <div class="message-box">{{ $enquiryMessage }}</div>

        <div class="reply-note">
            <strong>⚡ Quick Reply</strong>
            Click <strong>Reply</strong> in your email client to respond directly to <strong>{{ $senderFirstName }}</strong> at <code>{{ $senderEmail }}</code>.
        </div>
    </div>
    <div class="footer">
        Received: {{ now()->format('D, d M Y, g:i A') }} &nbsp;|&nbsp; Official Website Enquiry Portal
    </div>
</div>
</body>
</html>
