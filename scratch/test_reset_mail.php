<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::first();
if (!$user) {
    die("No user found");
}

$template = \App\Models\EmailTemplate::where('code', 'password_reset_code')->first();
if ($template) {
    echo "Found 'password_reset_code' template in DB:\n";
    echo "Subject: " . $template->subject . "\n";
    echo "Body: " . $template->body_html . "\n\n";
} else {
    echo "'password_reset_code' template NOT found in email_templates table!\n\n";
}

// Test sending password_reset_code email directly
$code = 'X7K9P2';
$result = \App\Services\EmailNotificationService::send('password_reset_code', $user->email, [
    'code' => $code,
    'full_name' => $user->name,
    'subject' => "[DOOTOR ENTERPRISES] Your Password Reset Code: {$code}",
    'message' => "Use verification code {$code} to reset your password.",
], null, $user);

echo "EmailNotificationService result: " . ($result ? "SUCCESS" : "FAILED") . "\n";

$lastLog = \App\Models\EmailLog::latest()->first();
if ($lastLog) {
    echo "Last Log Body:\n" . $lastLog->body . "\n";
    echo "Last Log Status: " . $lastLog->status . "\n";
    echo "Last Log Error: " . ($lastLog->error_message ?? 'None') . "\n";
}
