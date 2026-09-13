<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$templateCode = 'password_reset_code';
$templateData = [
    'title' => 'Password Reset Verification Code',
    'subject' => '[{company_name}] Your Password Reset Code: {code}',
    'body_html' => '<div style="font-family: Arial, sans-serif; max-width: 550px; margin: 0 auto; padding: 25px; border: 1px solid #e2e8f0; border-radius: 12px; background: #ffffff;">
    <h2 style="color: #004225; margin-bottom: 10px;">Password Reset Request</h2>
    <p style="color: #475569; font-size: 14px;">Hello <strong>{full_name}</strong>,</p>
    <p style="color: #475569; font-size: 14px;">You requested to reset your password on <strong>{company_name}</strong>. Use the 6-character alphanumeric verification code below to verify your identity and set a new password:</p>
    <div style="text-align: center; margin: 25px 0;">
        <span style="font-size: 32px; font-weight: 800; letter-spacing: 8px; color: #004225; background: #f1f5f9; padding: 14px 28px; border-radius: 8px; border: 2px dashed #004225; display: inline-block; font-family: monospace;">{code}</span>
    </div>
    <p style="color: #64748b; font-size: 12px; text-align: center;">This verification code is valid for 30 minutes. If you did not request this reset, please secure your account.</p>
</div>',
    'is_active' => true,
];

$template = \App\Models\EmailTemplate::updateOrCreate(
    ['code' => $templateCode],
    $templateData
);

echo "Email template '{$templateCode}' updated/created successfully! ID: {$template->id}\n";
