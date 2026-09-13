<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$testEmail = 'muhd.maher4u@gmail.com';
echo "Testing SMTP Mail delivery to {$testEmail}...\n";

try {
    \Illuminate\Support\Facades\Mail::html('<h1>SMTP Test Email</h1><p>This is a test email from DOOTOR ENTERPRISES to verify SMTP connection.</p>', function ($message) use ($testEmail) {
        $message->to($testEmail)
            ->subject('DOOTOR ENTERPRISES - SMTP Test Email');
    });
    echo "SMTP Mail sent SUCCESSFULLY!\n";
} catch (\Throwable $e) {
    echo "SMTP Mail Sending FAILED!\n";
    echo "Error Message: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

// Check EmailLog table
$logs = \App\Models\EmailLog::latest()->take(5)->get();
echo "\nLast 5 Email Logs in DB:\n";
foreach ($logs as $l) {
    echo "ID: {$l->id}, Recipient: {$l->recipient_email}, Subject: {$l->subject}, Status: {$l->status}, Error: " . ($l->error_message ?? 'None') . "\n";
}
