<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Roles Table
        if (!Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 2. Permissions Table
        if (!Schema::hasTable('permissions')) {
            Schema::create('permissions', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->string('module')->default('general');
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        // 3. Role Permissions Pivot
        if (!Schema::hasTable('role_permissions')) {
            Schema::create('role_permissions', function (Blueprint $table) {
                $table->foreignId('role_id')->constrained()->cascadeOnDelete();
                $table->foreignId('permission_id')->constrained()->cascadeOnDelete();
                $table->primary(['role_id', 'permission_id']);
            });
        }

        // 4. User Roles Pivot
        if (!Schema::hasTable('user_roles')) {
            Schema::create('user_roles', function (Blueprint $table) {
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('role_id')->constrained()->cascadeOnDelete();
                $table->primary(['user_id', 'role_id']);
            });
        }

        // 5. User Direct Permissions Pivot
        if (!Schema::hasTable('user_permissions')) {
            Schema::create('user_permissions', function (Blueprint $table) {
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('permission_id')->constrained()->cascadeOnDelete();
                $table->primary(['user_id', 'permission_id']);
            });
        }

        // 6. Service Modular Form Fields Table
        if (!Schema::hasTable('service_fields')) {
            Schema::create('service_fields', function (Blueprint $table) {
                $table->id();
                $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
                $table->string('field_label');
                $table->string('field_name');
                $table->string('field_type')->default('text'); // text, textarea, number, email, phone, date, time, dropdown, radio, checkbox, yes_no, file, passport, image, document, address, country, state, lga, instructions, declaration
                $table->string('placeholder')->nullable();
                $table->text('help_text')->nullable();
                $table->boolean('is_required')->default(false);
                $table->json('options')->nullable(); // For dropdown, radio, checkbox
                $table->string('allowed_file_types')->nullable();
                $table->unsignedInteger('max_file_size')->nullable(); // In KB
                $table->integer('sort_order')->default(0);
                $table->boolean('is_enabled')->default(true);
                $table->timestamps();
            });
        }

        // 7. Service Workflow Stages Table
        if (!Schema::hasTable('service_workflow_stages')) {
            Schema::create('service_workflow_stages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
                $table->string('stage_name');
                $table->text('description')->nullable();
                $table->string('status_key')->default('processing');
                $table->integer('sort_order')->default(0);
                $table->boolean('is_user_visible')->default(true);
                $table->boolean('notification_enabled')->default(true);
                $table->foreignId('assigned_role_id')->nullable()->constrained('roles')->nullOnDelete();
                $table->foreignId('assigned_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->unsignedInteger('estimated_days')->nullable();
                $table->timestamps();
            });
        }

        // 8. Enhance Service Requests Table
        Schema::table('service_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('service_requests', 'application_method')) {
                $table->string('application_method')->default('online')->after('reference_number'); // online, manual
            }
            if (!Schema::hasColumn('service_requests', 'passport_photo_path')) {
                $table->string('passport_photo_path')->nullable()->after('form_data');
            }
            if (!Schema::hasColumn('service_requests', 'manual_form_path')) {
                $table->string('manual_form_path')->nullable()->after('passport_photo_path');
            }
            if (!Schema::hasColumn('service_requests', 'assigned_role_id')) {
                $table->foreignId('assigned_role_id')->nullable()->after('assigned_staff_id')->constrained('roles')->nullOnDelete();
            }
            if (!Schema::hasColumn('service_requests', 'current_stage_id')) {
                $table->foreignId('current_stage_id')->nullable()->after('assigned_role_id')->constrained('service_workflow_stages')->nullOnDelete();
            }
            if (!Schema::hasColumn('service_requests', 'current_stage_name')) {
                $table->string('current_stage_name')->nullable()->after('current_stage_id');
            }
        });

        // 9. Application Stage History Table
        if (!Schema::hasTable('application_stage_histories')) {
            Schema::create('application_stage_histories', function (Blueprint $table) {
                $table->id();
                $table->foreignId('service_request_id')->constrained('service_requests')->cascadeOnDelete();
                $table->foreignId('stage_id')->nullable()->constrained('service_workflow_stages')->nullOnDelete();
                $table->string('stage_name');
                $table->string('status')->default('In Progress');
                $table->foreignId('changed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->text('notes')->nullable();
                $table->boolean('is_user_visible')->default(true);
                $table->timestamps();
            });
        }

        // 10. Application Notes Table
        if (!Schema::hasTable('application_notes')) {
            Schema::create('application_notes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('service_request_id')->constrained('service_requests')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->text('note');
                $table->boolean('is_internal')->default(true);
                $table->timestamps();
            });
        }

        // 11. Email Templates Table
        if (!Schema::hasTable('email_templates')) {
            Schema::create('email_templates', function (Blueprint $table) {
                $table->id();
                $table->string('code')->unique();
                $table->string('title');
                $table->string('subject');
                $table->longText('body_html');
                $table->text('body_text')->nullable();
                $table->text('variables_description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 12. Email Logs Table
        if (!Schema::hasTable('email_logs')) {
            Schema::create('email_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('service_request_id')->nullable()->constrained('service_requests')->nullOnDelete();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('recipient_email');
                $table->string('subject');
                $table->longText('body')->nullable();
                $table->string('status')->default('Sent'); // Sent, Failed
                $table->text('error_message')->nullable();
                $table->timestamp('sent_at')->nullable();
                $table->timestamps();
            });
        }

        // 13. Audit Logs Table
        if (!Schema::hasTable('audit_logs')) {
            Schema::create('audit_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('user_name')->nullable();
                $table->string('user_role')->nullable();
                $table->string('action');
                $table->string('target_type')->nullable();
                $table->string('target_id')->nullable();
                $table->string('reference_number')->nullable();
                $table->json('old_values')->nullable();
                $table->json('new_values')->nullable();
                $table->string('ip_address')->nullable();
                $table->string('user_agent')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('email_logs');
        Schema::dropIfExists('email_templates');
        Schema::dropIfExists('application_notes');
        Schema::dropIfExists('application_stage_histories');
        
        Schema::table('service_requests', function (Blueprint $table) {
            $table->dropForeign(['assigned_role_id']);
            $table->dropForeign(['current_stage_id']);
            $table->dropColumn(['application_method', 'passport_photo_path', 'manual_form_path', 'assigned_role_id', 'current_stage_id', 'current_stage_name']);
        });

        Schema::dropIfExists('service_workflow_stages');
        Schema::dropIfExists('service_fields');
        Schema::dropIfExists('user_permissions');
        Schema::dropIfExists('user_roles');
        Schema::dropIfExists('role_permissions');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
    }
};
