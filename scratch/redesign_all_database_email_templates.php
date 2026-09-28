<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\EmailTemplate;

$templates = EmailTemplate::all();
echo "Found " . $templates->count() . " templates in DB:\n";
foreach ($templates as $t) {
    echo "- Code: {$t->code} | Title: {$t->title}\n";
}

// System Templates to Redesign with High-End Dark Green & Gold Layout
$redesignedTemplates = [
    'new_application_user' => [
        'title' => 'User Application Submission Confirmation',
        'subject' => 'Application Received: {service_name} ({reference_number})',
        'body_html' => '<div style="font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, sans-serif; max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 16px; overflow: hidden; border: 1px solid #e2e8f0; box-shadow: 0 10px 30px rgba(0,46,26,0.08);">
            <div style="background: linear-gradient(135deg, #002e1a 0%, #004225 100%); padding: 30px; text-align: center; border-bottom: 4px solid #d4af37;">
                <h1 style="color: #ffffff; margin: 0; font-size: 22px; font-weight: 800; letter-spacing: 0.5px;">{company_name}</h1>
                <p style="color: #d4af37; margin: 4px 0 0 0; font-size: 13px; font-weight: 600;">Official Application Confirmation</p>
            </div>
            <div style="padding: 36px 32px;">
                <div style="display: inline-block; background: #dcfce7; color: #15803d; border: 1px solid #86efac; padding: 4px 14px; border-radius: 50px; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 20px;">✓ Application Submitted</div>
                <h2 style="color: #0f172a; font-size: 20px; font-weight: 800; margin: 0 0 16px 0;">Hello {full_name},</h2>
                <p style="color: #334155; font-size: 15px; line-height: 1.7; margin-bottom: 24px;">Thank you for trusting <strong>{company_name}</strong>. Your service application has been successfully logged into our processing portal.</p>

                <table style="width: 100%; border-collapse: collapse; margin: 24px 0; background: #f8fafc; border-radius: 12px; overflow: hidden; border: 1px solid #e2e8f0; font-size: 14px;">
                    <tr style="border-bottom: 1px solid #e2e8f0;"><td style="padding: 12px 16px; font-weight: 700; color: #64748b; width: 40%;">Reference Number:</td><td style="padding: 12px 16px; font-weight: 800; color: #004225;">{reference_number}</td></tr>
                    <tr style="border-bottom: 1px solid #e2e8f0;"><td style="padding: 12px 16px; font-weight: 700; color: #64748b;">Service Requested:</td><td style="padding: 12px 16px; font-weight: 700; color: #0f172a;">{service_name}</td></tr>
                    <tr style="border-bottom: 1px solid #e2e8f0;"><td style="padding: 12px 16px; font-weight: 700; color: #64748b;">Date Submitted:</td><td style="padding: 12px 16px; color: #334155;">{application_date}</td></tr>
                    <tr><td style="padding: 12px 16px; font-weight: 700; color: #64748b;">Current Status:</td><td style="padding: 12px 16px;"><span style="background: #e0f2fe; color: #0369a1; padding: 3px 10px; border-radius: 20px; font-weight: 700; font-size: 12px;">{status}</span></td></tr>
                </table>

                <p style="color: #334155; font-size: 14px; line-height: 1.6; margin-bottom: 28px;">You can track real-time progress and upload supporting documents directly from your personal dashboard:</p>

                <div style="text-align: center; margin: 28px 0;">
                    <a href="{dashboard_link}" style="background: linear-gradient(135deg, #004225 0%, #002e1a 100%); color: #ffffff; text-decoration: none; padding: 14px 36px; border-radius: 50px; font-weight: 700; font-size: 14px; display: inline-block; box-shadow: 0 6px 18px rgba(0,66,37,0.25);">Track Application &rarr;</a>
                </div>
            </div>
            <div style="background: #f8fafc; padding: 22px 32px; border-top: 1px solid #e2e8f0; text-align: center; font-size: 12px; color: #64748b; line-height: 1.6;">
                &copy; ' . date('Y') . ' {company_name}. All rights reserved. Automated security notification.
            </div>
        </div>',
        'variables_description' => '{full_name}, {service_name}, {reference_number}, {application_date}, {status}, {company_name}, {dashboard_link}',
    ],

    'new_application_admin' => [
        'title' => 'Admin Job Alert - New Application',
        'subject' => 'New Job Alert: {service_name} Application ({reference_number})',
        'body_html' => '<div style="font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, sans-serif; max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 16px; overflow: hidden; border: 1px solid #e2e8f0; box-shadow: 0 10px 30px rgba(0,46,26,0.08);">
            <div style="background: linear-gradient(135deg, #002e1a 0%, #004225 100%); padding: 30px; text-align: center; border-bottom: 4px solid #d4af37;">
                <h1 style="color: #ffffff; margin: 0; font-size: 22px; font-weight: 800;">{company_name}</h1>
                <p style="color: #d4af37; margin: 4px 0 0 0; font-size: 13px; font-weight: 600;">Administrative Work Queue Alert</p>
            </div>
            <div style="padding: 36px 32px;">
                <div style="display: inline-block; background: #fef3c7; color: #92400e; border: 1px solid #fde68a; padding: 4px 14px; border-radius: 50px; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 20px;">🔔 New Action Item</div>
                <h2 style="color: #0f172a; font-size: 20px; font-weight: 800; margin: 0 0 16px 0;">New Application Submitted</h2>
                <p style="color: #334155; font-size: 15px; line-height: 1.7; margin-bottom: 24px;">A new client application has been registered and requires vetting or assignment.</p>

                <table style="width: 100%; border-collapse: collapse; margin: 24px 0; background: #f8fafc; border-radius: 12px; overflow: hidden; border: 1px solid #e2e8f0; font-size: 14px;">
                    <tr style="border-bottom: 1px solid #e2e8f0;"><td style="padding: 12px 16px; font-weight: 700; color: #64748b; width: 40%;">Client Name:</td><td style="padding: 12px 16px; font-weight: 700; color: #0f172a;">{full_name}</td></tr>
                    <tr style="border-bottom: 1px solid #e2e8f0;"><td style="padding: 12px 16px; font-weight: 700; color: #64748b;">Service:</td><td style="padding: 12px 16px; font-weight: 700; color: #0f172a;">{service_name}</td></tr>
                    <tr style="border-bottom: 1px solid #e2e8f0;"><td style="padding: 12px 16px; font-weight: 700; color: #64748b;">Reference No:</td><td style="padding: 12px 16px; font-weight: 800; color: #004225;">{reference_number}</td></tr>
                    <tr><td style="padding: 12px 16px; font-weight: 700; color: #64748b;">Submission Time:</td><td style="padding: 12px 16px; color: #334155;">{application_date}</td></tr>
                </table>

                <div style="text-align: center; margin: 28px 0;">
                    <a href="{dashboard_link}" style="background: linear-gradient(135deg, #004225 0%, #002e1a 100%); color: #ffffff; text-decoration: none; padding: 14px 36px; border-radius: 50px; font-weight: 700; font-size: 14px; display: inline-block;">Open Admin Portal &rarr;</a>
                </div>
            </div>
            <div style="background: #f8fafc; padding: 22px 32px; border-top: 1px solid #e2e8f0; text-align: center; font-size: 12px; color: #64748b;">
                Internal System Notification for Authorized Staff.
            </div>
        </div>',
        'variables_description' => '{full_name}, {service_name}, {reference_number}, {application_date}, {company_name}',
    ],

    'stage_updated_user' => [
        'title' => 'Application Stage Transition Notice',
        'subject' => 'Update on your {service_name} Application ({reference_number})',
        'body_html' => '<div style="font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, sans-serif; max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 16px; overflow: hidden; border: 1px solid #e2e8f0; box-shadow: 0 10px 30px rgba(0,46,26,0.08);">
            <div style="background: linear-gradient(135deg, #002e1a 0%, #004225 100%); padding: 30px; text-align: center; border-bottom: 4px solid #d4af37;">
                <h1 style="color: #ffffff; margin: 0; font-size: 22px; font-weight: 800;">{company_name}</h1>
                <p style="color: #d4af37; margin: 4px 0 0 0; font-size: 13px; font-weight: 600;">Status Tracker Update</p>
            </div>
            <div style="padding: 36px 32px;">
                <div style="display: inline-block; background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; padding: 4px 14px; border-radius: 50px; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 20px;">⚡ Stage Progress Alert</div>
                <h2 style="color: #0f172a; font-size: 20px; font-weight: 800; margin: 0 0 16px 0;">Hello {full_name},</h2>
                <p style="color: #334155; font-size: 15px; line-height: 1.7; margin-bottom: 24px;">Your application <strong>{reference_number}</strong> for <strong>{service_name}</strong> has successfully moved to a new processing milestone.</p>

                <div style="background: #f8fafc; border-left: 4px solid #004225; padding: 20px; border-radius: 10px; margin: 24px 0; border: 1px solid #e2e8f0; border-left-width: 4px;">
                    <div style="font-size: 11px; font-weight: 800; text-transform: uppercase; color: #64748b; letter-spacing: 0.5px; margin-bottom: 4px;">Current Milestone Stage</div>
                    <div style="font-size: 18px; font-weight: 800; color: #004225; margin-bottom: 6px;">{current_stage}</div>
                    <div style="font-size: 13px; color: #475569;">Status: <strong>{status}</strong></div>
                </div>

                {notes}

                <div style="text-align: center; margin: 28px 0;">
                    <a href="{dashboard_link}" style="background: linear-gradient(135deg, #004225 0%, #002e1a 100%); color: #ffffff; text-decoration: none; padding: 14px 36px; border-radius: 50px; font-weight: 700; font-size: 14px; display: inline-block; box-shadow: 0 6px 18px rgba(0,66,37,0.25);">View Timeline & Progress &rarr;</a>
                </div>
            </div>
            <div style="background: #f8fafc; padding: 22px 32px; border-top: 1px solid #e2e8f0; text-align: center; font-size: 12px; color: #64748b;">
                &copy; ' . date('Y') . ' {company_name}. All rights reserved.
            </div>
        </div>',
        'variables_description' => '{full_name}, {service_name}, {reference_number}, {current_stage}, {status}, {notes}, {company_name}, {dashboard_link}',
    ],

    'password_reset_code' => [
        'title' => 'Password Reset Verification Code',
        'subject' => '[{company_name}] Your Password Reset Code: {code}',
        'body_html' => '<div style="font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, sans-serif; max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 16px; overflow: hidden; border: 1px solid #e2e8f0; box-shadow: 0 10px 30px rgba(0,46,26,0.08);">
            <div style="background: linear-gradient(135deg, #002e1a 0%, #004225 100%); padding: 30px; text-align: center; border-bottom: 4px solid #d4af37;">
                <h1 style="color: #ffffff; margin: 0; font-size: 22px; font-weight: 800;">{company_name}</h1>
                <p style="color: #d4af37; margin: 4px 0 0 0; font-size: 13px; font-weight: 600;">Account Security Authorization</p>
            </div>
            <div style="padding: 36px 32px;">
                <div style="display: inline-block; background: #fef3c7; color: #92400e; border: 1px solid #fde68a; padding: 4px 14px; border-radius: 50px; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 20px;">🔒 Security Code</div>
                <h2 style="color: #0f172a; font-size: 20px; font-weight: 800; margin: 0 0 16px 0;">Hello {full_name},</h2>
                <p style="color: #334155; font-size: 15px; line-height: 1.7; margin-bottom: 24px;">You requested to reset your password on <strong>{company_name}</strong>. Use the secure verification code below to set your new password:</p>

                <div style="text-align: center; margin: 32px 0;">
                    <span style="font-size: 34px; font-weight: 800; letter-spacing: 8px; color: #004225; background: #f1f5f9; padding: 16px 32px; border-radius: 12px; border: 2px dashed #004225; display: inline-block; font-family: monospace;">{code}</span>
                </div>

                <p style="color: #64748b; font-size: 13px; line-height: 1.6; text-align: center; margin-bottom: 0;">This verification code is valid for <strong>30 minutes</strong>. If you did not initiate this request, no action is required.</p>
            </div>
            <div style="background: #f8fafc; padding: 22px 32px; border-top: 1px solid #e2e8f0; text-align: center; font-size: 12px; color: #64748b;">
                &copy; ' . date('Y') . ' {company_name}. Official Security Notice.
            </div>
        </div>',
        'variables_description' => '{full_name}, {code}, {company_name}',
    ],
];

foreach ($redesignedTemplates as $code => $data) {
    EmailTemplate::updateOrCreate(
        ['code' => $code],
        array_merge($data, ['is_active' => true])
    );
    echo "Updated email template [{$code}]\n";
}

echo "All email templates successfully updated to luxury dark green & gold design system!\n";
