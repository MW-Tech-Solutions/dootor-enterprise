<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Service;
use App\Models\ServiceWorkflowStage;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ComprehensivePortalSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Permissions
        $permissionsData = [
            // Services
            ['name' => 'View Services', 'slug' => 'services.view', 'module' => 'services', 'description' => 'View service listings and details'],
            ['name' => 'Create Services', 'slug' => 'services.create', 'module' => 'services', 'description' => 'Create new services'],
            ['name' => 'Edit Services', 'slug' => 'services.edit', 'module' => 'services', 'description' => 'Modify existing services'],
            ['name' => 'Delete Services', 'slug' => 'services.delete', 'module' => 'services', 'description' => 'Delete services from catalog'],
            ['name' => 'Publish Services', 'slug' => 'services.publish', 'module' => 'services', 'description' => 'Publish or unpublish services'],
            ['name' => 'Update Service Pricing', 'slug' => 'services.pricing.update', 'module' => 'services', 'description' => 'Modify service and processing fees'],

            // Documents
            ['name' => 'View Document Requirements', 'slug' => 'documents.view', 'module' => 'documents', 'description' => 'View required service documents'],
            ['name' => 'Create Document Requirement', 'slug' => 'documents.create', 'module' => 'documents', 'description' => 'Add new required document types'],
            ['name' => 'Edit Document Requirement', 'slug' => 'documents.edit', 'module' => 'documents', 'description' => 'Edit required document types'],
            ['name' => 'Delete Document Requirement', 'slug' => 'documents.delete', 'module' => 'documents', 'description' => 'Remove required document types'],
            ['name' => 'Verify Uploaded Documents', 'slug' => 'documents.verify', 'module' => 'documents', 'description' => 'Approve or verify client uploaded documents'],
            ['name' => 'Reject Uploaded Documents', 'slug' => 'documents.reject', 'module' => 'documents', 'description' => 'Reject client uploaded documents'],

            // Applications
            ['name' => 'View Applications', 'slug' => 'applications.view', 'module' => 'applications', 'description' => 'View service applications'],
            ['name' => 'Review Applications', 'slug' => 'applications.review', 'module' => 'applications', 'description' => 'Review application details and inputs'],
            ['name' => 'Update Application', 'slug' => 'applications.update', 'module' => 'applications', 'description' => 'Update application parameters'],
            ['name' => 'Update Application Status', 'slug' => 'applications.status.update', 'module' => 'applications', 'description' => 'Change application overall status'],
            ['name' => 'Update Workflow Stage', 'slug' => 'applications.stage.update', 'module' => 'applications', 'description' => 'Advance or modify application workflow stage'],
            ['name' => 'Approve Applications', 'slug' => 'applications.approve', 'module' => 'applications', 'description' => 'Approve submitted applications'],
            ['name' => 'Reject Applications', 'slug' => 'applications.reject', 'module' => 'applications', 'description' => 'Reject submitted applications'],
            ['name' => 'Request Additional Info', 'slug' => 'applications.request_info', 'module' => 'applications', 'description' => 'Request corrections or missing documents from applicant'],
            ['name' => 'Assign Applications', 'slug' => 'applications.assign', 'module' => 'applications', 'description' => 'Assign applications to staff members or roles'],

            // Users
            ['name' => 'View Users', 'slug' => 'users.view', 'module' => 'users', 'description' => 'View user accounts'],
            ['name' => 'Create Users', 'slug' => 'users.create', 'module' => 'users', 'description' => 'Create administrative or client user accounts'],
            ['name' => 'Edit Users', 'slug' => 'users.edit', 'module' => 'users', 'description' => 'Update user profiles and details'],
            ['name' => 'Suspend Users', 'slug' => 'users.suspend', 'module' => 'users', 'description' => 'Suspend or activate user accounts'],
            ['name' => 'Assign User Roles', 'slug' => 'users.assign_roles', 'module' => 'users', 'description' => 'Assign roles to user accounts'],
            ['name' => 'Assign Direct Permissions', 'slug' => 'users.assign_permissions', 'module' => 'users', 'description' => 'Assign specific direct permissions to user accounts'],

            // Roles & System Settings
            ['name' => 'View Roles', 'slug' => 'roles.view', 'module' => 'roles', 'description' => 'View dynamic system roles'],
            ['name' => 'Manage Roles', 'slug' => 'roles.manage', 'module' => 'roles', 'description' => 'Create, edit, and assign permissions to roles'],
            ['name' => 'View Email Templates', 'slug' => 'email.view', 'module' => 'email', 'description' => 'View email notification templates'],
            ['name' => 'Manage Email Templates', 'slug' => 'email.manage', 'module' => 'email', 'description' => 'Edit email templates and settings'],
            ['name' => 'Send Manual Emails', 'slug' => 'email.send', 'module' => 'email', 'description' => 'Send email notifications directly to applicants'],
            ['name' => 'View Reports', 'slug' => 'reports.view', 'module' => 'reports', 'description' => 'View analytical reports'],
            ['name' => 'Export Reports', 'slug' => 'reports.export', 'module' => 'reports', 'description' => 'Export application & revenue data as CSV'],
            ['name' => 'View Settings', 'slug' => 'settings.view', 'module' => 'settings', 'description' => 'View system configuration settings'],
            ['name' => 'Manage Settings', 'slug' => 'settings.manage', 'module' => 'settings', 'description' => 'Update platform & branding settings'],
            ['name' => 'View Audit Logs', 'slug' => 'audit.view', 'module' => 'audit', 'description' => 'Inspect administrative audit log trail'],
        ];

        $permissionModels = [];
        foreach ($permissionsData as $pData) {
            $permissionModels[$pData['slug']] = Permission::updateOrCreate(
                ['slug' => $pData['slug']],
                $pData
            );
        }

        // 2. Seed Standard Roles
        $rolesData = [
            [
                'name' => 'Super Admin',
                'slug' => 'super-admin',
                'description' => 'Full unrestricted system administrative access',
                'permissions' => array_keys($permissionModels),
            ],
            [
                'name' => 'Administrator',
                'slug' => 'administrator',
                'description' => 'Full administrative access excluding structural role modifications',
                'permissions' => array_keys($permissionModels),
            ],
            [
                'name' => 'Service Manager',
                'slug' => 'service-manager',
                'description' => 'Manages services, dynamic forms, document requirements, and pricing',
                'permissions' => ['services.view', 'services.create', 'services.edit', 'services.delete', 'services.publish', 'services.pricing.update', 'documents.view', 'documents.create', 'documents.edit', 'documents.delete', 'applications.view', 'applications.review', 'reports.view'],
            ],
            [
                'name' => 'Application Officer',
                'slug' => 'application-officer',
                'description' => 'Reviews, processes, updates workflow stages, and completes applications',
                'permissions' => ['applications.view', 'applications.review', 'applications.update', 'applications.status.update', 'applications.stage.update', 'applications.approve', 'applications.reject', 'applications.request_info', 'documents.view', 'documents.verify'],
            ],
            [
                'name' => 'Document Verification Officer',
                'slug' => 'document-verification-officer',
                'description' => 'Verifies client uploaded documents and checks form uploads',
                'permissions' => ['documents.view', 'documents.verify', 'documents.reject', 'applications.view', 'applications.review'],
            ],
            [
                'name' => 'Finance Officer',
                'slug' => 'finance-officer',
                'description' => 'Manages pricing, payment status checks, and revenue reports',
                'permissions' => ['services.pricing.update', 'applications.view', 'reports.view', 'reports.export'],
            ],
            [
                'name' => 'Customer Support',
                'slug' => 'customer-support',
                'description' => 'Monitors applications, responds to user queries, and sends email notifications',
                'permissions' => ['applications.view', 'users.view', 'email.send'],
            ],
            [
                'name' => 'Content Manager',
                'slug' => 'content-manager',
                'description' => 'Manages service descriptions, media, and site content',
                'permissions' => ['services.view', 'services.edit'],
            ],
            [
                'name' => 'Email Manager',
                'slug' => 'email-manager',
                'description' => 'Manages notification email templates and logs',
                'permissions' => ['email.view', 'email.manage', 'email.send'],
            ],
            [
                'name' => 'Read Only Staff',
                'slug' => 'read-only-staff',
                'description' => 'Read-only visibility for reporting and operational tracking',
                'permissions' => ['services.view', 'documents.view', 'applications.view', 'reports.view'],
            ],
        ];

        foreach ($rolesData as $rData) {
            $role = Role::firstOrCreate(
                ['slug' => $rData['slug']],
                [
                    'name' => $rData['name'],
                    'description' => $rData['description'],
                    'is_active' => true,
                ]
            );

            // CRITICAL SECURITY REQUIREMENT: Default permissions MUST ONLY be assigned when the role is first created.
            // If the role already exists and has been customized by an Administrator, preserve its database permissions.
            if ($role->wasRecentlyCreated) {
                $pIds = [];
                foreach ($rData['permissions'] as $pSlug) {
                    if (isset($permissionModels[$pSlug])) {
                        $pIds[] = $permissionModels[$pSlug]->id;
                    }
                }
                $role->permissions()->sync($pIds);
            }
        }

        // 3. Attach Super Admin role to designated primary admin user
        $superAdminRole = Role::where('slug', 'super-admin')->first();
        if ($superAdminRole) {
            $primaryAdmin = User::where('email', 'admin@test.com')->first();
            if ($primaryAdmin && !$primaryAdmin->roles()->where('role_id', $superAdminRole->id)->exists()) {
                $primaryAdmin->roles()->attach($superAdminRole->id);
            }
        }

        // 4. Seed Email Templates
        $emailTemplates = [
            [
                'code' => 'new_application_user',
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
                        &copy; {company_name}. All rights reserved. Automated security notification.
                    </div>
                </div>',
                'variables_description' => '{full_name}, {service_name}, {reference_number}, {application_date}, {status}, {company_name}, {dashboard_link}',
            ],
            [
                'code' => 'new_application_admin',
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
            [
                'code' => 'stage_updated_user',
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
                        &copy; {company_name}. All rights reserved.
                    </div>
                </div>',
                'variables_description' => '{full_name}, {service_name}, {reference_number}, {current_stage}, {status}, {notes}, {company_name}, {dashboard_link}',
            ],
            [
                'code' => 'new_client_registration_admin',
                'title' => 'New Client Account Created (Admin Notification)',
                'subject' => 'New Client Registration – {company_name}',
                'body_html' => '<div style="font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, sans-serif; max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 16px; overflow: hidden; border: 1px solid #e2e8f0; box-shadow: 0 10px 30px rgba(0,46,26,0.08);">
                    <div style="background: linear-gradient(135deg, #002e1a 0%, #004225 100%); padding: 30px; text-align: center; border-bottom: 4px solid #d4af37;">
                        <h1 style="color: #ffffff; margin: 0; font-size: 22px; font-weight: 800;">{company_name}</h1>
                        <p style="color: #d4af37; margin: 4px 0 0 0; font-size: 13px; font-weight: 600;">Client Account Registration Alert</p>
                    </div>
                    <div style="padding: 36px 32px;">
                        <h2 style="color: #0f172a; font-size: 20px; font-weight: 800; margin: 0 0 16px 0;">New Client Account Registered</h2>
                        <p style="color: #334155; font-size: 15px; line-height: 1.7; margin-bottom: 24px;">A new client account has been registered on <strong>{company_name}</strong>.</p>
                        <table style="width: 100%; border-collapse: collapse; margin: 24px 0; background: #f8fafc; border-radius: 12px; overflow: hidden; border: 1px solid #e2e8f0; font-size: 14px;">
                            <tr style="border-bottom: 1px solid #e2e8f0;"><td style="padding: 12px 16px; font-weight: 700; color: #64748b; width: 40%;">Client Name:</td><td style="padding: 12px 16px; font-weight: 700; color: #0f172a;">{full_name}</td></tr>
                            <tr style="border-bottom: 1px solid #e2e8f0;"><td style="padding: 12px 16px; font-weight: 700; color: #64748b;">Email:</td><td style="padding: 12px 16px; color: #004225;">{email}</td></tr>
                            <tr style="border-bottom: 1px solid #e2e8f0;"><td style="padding: 12px 16px; font-weight: 700; color: #64748b;">Phone:</td><td style="padding: 12px 16px;">{phone}</td></tr>
                            <tr style="border-bottom: 1px solid #e2e8f0;"><td style="padding: 12px 16px; font-weight: 700; color: #64748b;">Applying From:</td><td style="padding: 12px 16px;">{country_applying_from}</td></tr>
                            <tr><td style="padding: 12px 16px; font-weight: 700; color: #64748b;">Service Country:</td><td style="padding: 12px 16px;">{country_service_requested}</td></tr>
                        </table>
                    </div>
                </div>',
                'variables_description' => '{full_name}, {email}, {phone}, {country_applying_from}, {country_service_requested}, {company_name}',
            ],
            [
                'code' => 'application_assigned_staff',
                'title' => 'Application Assignment Notice (Staff)',
                'subject' => 'Application Assigned: {reference_number} – {service_name}',
                'body_html' => '<div style="font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, sans-serif; max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 16px; overflow: hidden; border: 1px solid #e2e8f0; box-shadow: 0 10px 30px rgba(0,46,26,0.08);">
                    <div style="background: linear-gradient(135deg, #002e1a 0%, #004225 100%); padding: 30px; text-align: center; border-bottom: 4px solid #d4af37;">
                        <h1 style="color: #ffffff; margin: 0; font-size: 22px; font-weight: 800;">{company_name}</h1>
                        <p style="color: #d4af37; margin: 4px 0 0 0; font-size: 13px; font-weight: 600;">Work Queue Assignment Notice</p>
                    </div>
                    <div style="padding: 36px 32px;">
                        <h2 style="color: #0f172a; font-size: 20px; font-weight: 800; margin: 0 0 16px 0;">New Application Assigned</h2>
                        <p style="color: #334155; font-size: 15px; line-height: 1.7; margin-bottom: 24px;">Application <strong>{reference_number}</strong> ({service_name}) has been assigned to you for active processing.</p>
                        <p style="color: #334155; font-size: 15px;">Applicant: <strong>{full_name}</strong></p>
                    </div>
                </div>',
                'variables_description' => '{full_name}, {service_name}, {reference_number}, {company_name}',
            ],
        ];

        foreach ($emailTemplates as $tmplData) {
            EmailTemplate::updateOrCreate(
                ['code' => $tmplData['code']],
                [
                    'title' => $tmplData['title'],
                    'subject' => $tmplData['subject'],
                    'body_html' => $tmplData['body_html'],
                    'variables_description' => $tmplData['variables_description'],
                    'is_active' => true,
                ]
            );
        }

        // 5. Seed Primary & Sub-services
        $primaryServices = [
            [
                'name' => 'Passport Services',
                'category' => 'Passport',
                'is_primary' => true,
                'icon' => 'bi-passport',
                'price' => 150000,
                'service_fee' => 120000,
                'processing_fee' => 30000,
                'processing_days' => 14,
                'short_description' => 'Complete official international passport renewal, fresh application, lost passport replacement, and data modifications.',
                'description' => 'Full service international passport processing including fresh applications, renewals, lost passport handling, and change of data.',
                'sub_services' => [
                    ['name' => 'Fresh / Renewal Application', 'price' => 150000, 'short_description' => 'New passport issuing or standard renewal of expired passport booklet.'],
                    ['name' => 'Change of Application Data', 'price' => 175000, 'short_description' => 'Modification of name, marital status, or date of birth on passport records.'],
                    ['name' => 'Lost Case Replacement', 'price' => 200000, 'short_description' => 'Official replacement for lost, stolen, or damaged international passports.'],
                ],
            ],
            [
                'name' => 'NIN Services',
                'category' => 'NIN',
                'is_primary' => true,
                'icon' => 'bi-person-badge',
                'price' => 45000,
                'service_fee' => 35000,
                'processing_fee' => 10000,
                'processing_days' => 3,
                'short_description' => 'National Identification Number (NIN) pre-enrollment, adult/child registration, and data modifications.',
                'description' => 'National Identity Management Commission (NIMC) NIN pre-enrollment for adults, children, and demographic corrections.',
                'sub_services' => [
                    ['name' => 'Adult Pre-Enrollment Application', 'price' => 45000, 'short_description' => 'Pre-enrollment registration for adults aged 16 and above.'],
                    ['name' => 'Child Pre-Enrollment Application', 'price' => 35000, 'short_description' => 'Pre-enrollment registration for minors under 16 years.'],
                    ['name' => 'NIN Modifications', 'price' => 55000, 'short_description' => 'Correction of name, date of birth, address, or phone number on NIN database.'],
                ],
            ],
            [
                'name' => 'Emergency Travel Certificate',
                'category' => 'Consular',
                'is_primary' => true,
                'icon' => 'bi-airplane-engines',
                'price' => 120000,
                'service_fee' => 100000,
                'processing_fee' => 20000,
                'processing_days' => 2,
                'short_description' => 'Expedited one-way emergency travel document for urgent travel obligations.',
                'description' => 'Official Emergency Travel Certificate (ETC) processing for citizens requiring immediate emergency travel.',
                'sub_services' => [],
            ],
            [
                'name' => 'Authorization Letter / Power of Attorney',
                'category' => 'Consular',
                'is_primary' => true,
                'icon' => 'bi-file-earmark-check',
                'price' => 85000,
                'service_fee' => 70000,
                'processing_fee' => 15000,
                'processing_days' => 5,
                'short_description' => 'Consular legalization and attestation of power of attorney and legal authorization letters.',
                'description' => 'Official consular authentication and attestation for legal powers of attorney and authorization instruments.',
                'sub_services' => [],
            ],
            [
                'name' => 'Waiver / Appointment Reschedule',
                'category' => 'Consular',
                'is_primary' => true,
                'icon' => 'bi-calendar-event',
                'price' => 60000,
                'service_fee' => 50000,
                'processing_fee' => 10000,
                'processing_days' => 2,
                'short_description' => 'Fast-track biometric appointment waiver or priority appointment rescheduling.',
                'description' => 'Official consular appointment waiver request and emergency scheduling priority.',
                'sub_services' => [],
            ],
            [
                'name' => 'Same Day Collection',
                'category' => 'Express Services',
                'is_primary' => true,
                'icon' => 'bi-clock-history',
                'price' => 95000,
                'service_fee' => 75000,
                'processing_fee' => 20000,
                'processing_days' => 1,
                'short_description' => 'Ultra-express same day retrieval and collection of completed consular documents.',
                'description' => 'Same day dispatch and physical retrieval of processed passports, certificates, and legal documents.',
                'sub_services' => [],
            ],
            [
                'name' => 'Police Clearance Certificate',
                'category' => 'Legal & Security',
                'is_primary' => false,
                'icon' => 'bi-shield-check',
                'price' => 95000,
                'service_fee' => 80000,
                'processing_fee' => 15000,
                'processing_days' => 7,
                'short_description' => 'Official Police Character Certificate issued by Criminal Investigation Department.',
                'description' => 'Character clearance certificate processing for international visa, employment, or immigration purposes.',
                'sub_services' => [],
            ],
            [
                'name' => 'Birth Certificate Attestation',
                'category' => 'Attestation',
                'is_primary' => false,
                'icon' => 'bi-award',
                'price' => 50000,
                'service_fee' => 40000,
                'processing_fee' => 10000,
                'processing_days' => 5,
                'short_description' => 'Authentication of birth certificates with Ministry of Foreign Affairs & Ministry of Education.',
                'description' => 'Attestation and legalization of birth certificates for international legal recognition.',
                'sub_services' => [],
            ],
        ];

        $standardStages = [
            ['stage_name' => 'Application Submitted', 'description' => 'Application received and registered in portal.', 'status_key' => 'Submitted', 'sort_order' => 1],
            ['stage_name' => 'Payment Confirmed', 'description' => 'Payment has been processed successfully.', 'status_key' => 'Payment Confirmed', 'sort_order' => 2],
            ['stage_name' => 'Documents Under Review', 'description' => 'Verification officer is inspecting uploaded documents.', 'status_key' => 'In Review', 'sort_order' => 3],
            ['stage_name' => 'Verification in Progress', 'description' => 'Undergoing background & agency verification checks.', 'status_key' => 'Verification', 'sort_order' => 4],
            ['stage_name' => 'Processing', 'description' => 'Service is currently being fulfilled.', 'status_key' => 'Processing', 'sort_order' => 5],
            ['stage_name' => 'Ready for Collection / Delivery', 'description' => 'Completed documents or certificates are ready.', 'status_key' => 'Ready', 'sort_order' => 6],
            ['stage_name' => 'Completed', 'description' => 'Service application successfully completed and delivered.', 'status_key' => 'Completed', 'sort_order' => 7],
        ];

        foreach ($primaryServices as $pData) {
            $subData = $pData['sub_services'] ?? [];
            unset($pData['sub_services']);

            $mainService = Service::updateOrCreate(
                ['name' => $pData['name']],
                array_merge($pData, ['status' => 'Active'])
            );

            // Create workflow stages for main service
            if ($mainService->workflowStages()->count() === 0) {
                foreach ($standardStages as $stage) {
                    ServiceWorkflowStage::create([
                        'service_id' => $mainService->id,
                        'stage_name' => $stage['stage_name'],
                        'description' => $stage['description'],
                        'status_key' => $stage['status_key'],
                        'sort_order' => $stage['sort_order'],
                        'is_user_visible' => true,
                        'notification_enabled' => true,
                    ]);
                }
            }

            // Create Sub-services
            foreach ($subData as $sItem) {
                $subService = Service::updateOrCreate(
                    ['name' => $sItem['name'], 'parent_id' => $mainService->id],
                    [
                        'parent_id' => $mainService->id,
                        'category' => $mainService->category,
                        'price' => $sItem['price'],
                        'service_fee' => (float) ($sItem['price'] * 0.8),
                        'processing_fee' => (float) ($sItem['price'] * 0.2),
                        'processing_days' => $mainService->processing_days,
                        'short_description' => $sItem['short_description'],
                        'description' => $sItem['short_description'],
                        'icon' => $mainService->icon,
                        'is_primary' => false,
                        'status' => 'Active',
                    ]
                );

                if ($subService->workflowStages()->count() === 0) {
                    foreach ($standardStages as $stage) {
                        ServiceWorkflowStage::create([
                            'service_id' => $subService->id,
                            'stage_name' => $stage['stage_name'],
                            'description' => $stage['description'],
                            'status_key' => $stage['status_key'],
                            'sort_order' => $stage['sort_order'],
                            'is_user_visible' => true,
                            'notification_enabled' => true,
                        ]);
                    }
                }
            }
        }
    }
}
