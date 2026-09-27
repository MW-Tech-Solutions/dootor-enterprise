<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Enhance Users Table
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'middle_name')) {
                $table->string('middle_name')->nullable()->after('first_name');
            }
            if (!Schema::hasColumn('users', 'country_applying_from')) {
                $table->string('country_applying_from')->nullable()->after('country');
            }
            if (!Schema::hasColumn('users', 'country_service_requested')) {
                $table->string('country_service_requested')->nullable()->after('country_applying_from');
            }
            if (!Schema::hasColumn('users', 'city')) {
                $table->string('city')->nullable()->after('state');
            }
            if (!Schema::hasColumn('users', 'staff_file_number')) {
                $table->string('staff_file_number')->nullable()->unique()->after('role');
            }
            if (!Schema::hasColumn('users', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        // 2. Enhance Services Table
        Schema::table('services', function (Blueprint $table) {
            if (!Schema::hasColumn('services', 'parent_id')) {
                $table->foreignId('parent_id')->nullable()->after('id')->constrained('services')->nullOnDelete();
            }
            if (!Schema::hasColumn('services', 'is_primary')) {
                $table->boolean('is_primary')->default(false)->after('status');
            }
            if (!Schema::hasColumn('services', 'icon')) {
                $table->string('icon')->nullable()->after('is_primary');
            }
            if (!Schema::hasColumn('services', 'short_description')) {
                $table->text('short_description')->nullable()->after('description');
            }
        });

        // 3. Enhance Service Requests Table
        Schema::table('service_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('service_requests', 'sub_service_id')) {
                $table->foreignId('sub_service_id')->nullable()->after('service_id')->constrained('services')->nullOnDelete();
            }
            if (!Schema::hasColumn('service_requests', 'sub_service_name')) {
                $table->string('sub_service_name')->nullable()->after('service_name');
            }
            if (!Schema::hasColumn('service_requests', 'country_applying_from')) {
                $table->string('country_applying_from')->nullable()->after('client_email');
            }
            if (!Schema::hasColumn('service_requests', 'country_service_requested')) {
                $table->string('country_service_requested')->nullable()->after('country_applying_from');
            }
            if (!Schema::hasColumn('service_requests', 'current_step')) {
                $table->unsignedInteger('current_step')->default(1)->after('status');
            }
            if (!Schema::hasColumn('service_requests', 'progress_percent')) {
                $table->unsignedInteger('progress_percent')->default(0)->after('current_step');
            }
            if (!Schema::hasColumn('service_requests', 'last_saved_at')) {
                $table->timestamp('last_saved_at')->nullable()->after('updated_at');
            }
            if (!Schema::hasColumn('service_requests', 'assigned_at')) {
                $table->timestamp('assigned_at')->nullable()->after('assigned_role_id');
            }
            if (!Schema::hasColumn('service_requests', 'submitted_at')) {
                $table->timestamp('submitted_at')->nullable()->after('assigned_at');
            }
            if (!Schema::hasColumn('service_requests', 'admin_notified_draft_at')) {
                $table->timestamp('admin_notified_draft_at')->nullable()->after('submitted_at');
            }
        });

        // 4. Create Assignment History Table
        if (!Schema::hasTable('assignment_histories')) {
            Schema::create('assignment_histories', function (Blueprint $table) {
                $table->id();
                $table->foreignId('service_request_id')->constrained('service_requests')->cascadeOnDelete();
                $table->foreignId('previous_staff_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('new_staff_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('assigned_by_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('assignment_histories');

        Schema::table('service_requests', function (Blueprint $table) {
            $table->dropForeign(['sub_service_id']);
            $table->dropColumn([
                'sub_service_id', 'sub_service_name', 'country_applying_from', 'country_service_requested',
                'current_step', 'progress_percent', 'last_saved_at', 'assigned_at', 'submitted_at', 'admin_notified_draft_at'
            ]);
        });

        Schema::table('services', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn(['parent_id', 'is_primary', 'icon', 'short_description']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['middle_name', 'country_applying_from', 'country_service_requested', 'city', 'staff_file_number', 'deleted_at']);
        });
    }
};
