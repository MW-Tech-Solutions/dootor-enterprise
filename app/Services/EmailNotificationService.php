<?php

namespace App\Services;

use App\Models\EmailLog;
use App\Models\EmailTemplate;
use App\Models\ServiceRequest;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class EmailNotificationService
{
    /**
     * Send email notification using managed templates.
     */
    public static function send(
        string $templateCode,
        string $recipientEmail,
        array $data = [],
        ?ServiceRequest $serviceRequest = null,
        ?User $user = null
    ): bool {
        $template = EmailTemplate::where('code', $templateCode)->where('is_active', true)->first();

        $settings = SystemSetting::first();
        $companyName = $settings->platform_name ?? config('app.name', 'DOOTOR ENTERPRISES');
        $dashboardUrl = route('client.requests');

        $replacements = [
            '{company_name}' => $companyName,
            '{dashboard_link}' => $dashboardUrl,
            '{full_name}' => $user ? $user->name : ($data['full_name'] ?? 'Valued Customer'),
            '{code}' => $data['code'] ?? '',
            '{service_name}' => $serviceRequest ? $serviceRequest->service_name : ($data['service_name'] ?? 'Service'),
            '{reference_number}' => $serviceRequest ? $serviceRequest->reference_number : ($data['reference_number'] ?? 'N/A'),
            '{current_stage}' => $serviceRequest ? ($serviceRequest->current_stage_name ?? $serviceRequest->status) : ($data['current_stage'] ?? 'N/A'),
            '{status}' => $serviceRequest ? $serviceRequest->status : ($data['status'] ?? 'N/A'),
            '{application_date}' => $serviceRequest ? $serviceRequest->created_at->format('M d, Y H:i A') : date('M d, Y H:i A'),
            '{notes}' => $data['notes'] ?? '',
        ];

        foreach ($data as $k => $v) {
            if (is_scalar($v)) {
                $replacements['{' . trim($k, '{}') . '}'] = (string) $v;
            }
        }

        if ($template) {
            $subject = strtr($template->subject, $replacements);
            $htmlBody = strtr($template->body_html, $replacements);
        } else {
            // Default fallback if template record missing
            $subject = $data['subject'] ?? "[{$companyName}] Notification regarding " . ($serviceRequest?->reference_number ?? 'Application');
            $msg = $data['message'] ?? 'Your application status has been updated.';
            if (str_contains($msg, '<div') || str_contains($msg, '<p') || str_contains($msg, '<html')) {
                $htmlBody = $msg;
            } else {
                $htmlBody = "<p>Hello " . e($replacements['{full_name}']) . ",</p><p>" . e($msg) . "</p>";
            }
        }

        try {
            Mail::html($htmlBody, function ($message) use ($recipientEmail, $subject, $companyName) {
                $message->to($recipientEmail)
                    ->subject($subject);
            });

            EmailLog::create([
                'service_request_id' => $serviceRequest?->id,
                'user_id' => $user?->id ?? $serviceRequest?->client_id,
                'recipient_email' => $recipientEmail,
                'subject' => $subject,
                'body' => $htmlBody,
                'status' => 'Sent',
                'sent_at' => now(),
            ]);

            return true;
        } catch (Throwable $e) {
            Log::error("Failed to send email notification [{$templateCode}] to {$recipientEmail}: " . $e->getMessage());

            EmailLog::create([
                'service_request_id' => $serviceRequest?->id,
                'user_id' => $user?->id ?? $serviceRequest?->client_id,
                'recipient_email' => $recipientEmail,
                'subject' => $subject ?? 'Email Notification',
                'body' => $htmlBody ?? null,
                'status' => 'Failed',
                'error_message' => $e->getMessage(),
                'sent_at' => now(),
            ]);

            return false;
        }
    }
}
