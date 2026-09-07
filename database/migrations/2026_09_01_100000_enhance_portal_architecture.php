<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Convert existing tables to InnoDB engine if needed for foreign key support
        try {
            DB::statement('ALTER TABLE users ENGINE=InnoDB');
            DB::statement('ALTER TABLE services ENGINE=InnoDB');
            DB::statement('ALTER TABLE service_requests ENGINE=InnoDB');
        } catch (\Throwable $e) {
            // Ignore if engine alter fails
        }

        Schema::table('services', function (Blueprint $table) {
            if (!Schema::hasColumn('services', 'service_fee')) {
                $table->decimal('service_fee', 12, 2)->default(0)->after('price');
            }
            if (!Schema::hasColumn('services', 'processing_fee')) {
                $table->decimal('processing_fee', 12, 2)->default(0)->after('service_fee');
            }
            if (!Schema::hasColumn('services', 'processing_days')) {
                $table->unsignedInteger('processing_days')->nullable()->after('processing_fee');
            }
            if (!Schema::hasColumn('services', 'category')) {
                $table->string('category')->nullable()->after('name');
            }
            if (!Schema::hasColumn('services', 'required_documents')) {
                $table->json('required_documents')->nullable()->after('image_url');
            }
            if (!Schema::hasColumn('services', 'custom_fields')) {
                $table->json('custom_fields')->nullable()->after('required_documents');
            }
        });

        Schema::table('service_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('service_requests', 'reference_number')) {
                $table->string('reference_number')->nullable()->unique()->index()->after('id');
            }
            if (!Schema::hasColumn('service_requests', 'assigned_staff_id')) {
                $table->unsignedBigInteger('assigned_staff_id')->nullable()->after('vendor_id');
            }
            if (!Schema::hasColumn('service_requests', 'amount_paid')) {
                $table->decimal('amount_paid', 12, 2)->default(0)->after('price');
            }
            if (!Schema::hasColumn('service_requests', 'outstanding_balance')) {
                $table->decimal('outstanding_balance', 12, 2)->default(0)->after('amount_paid');
            }
            if (!Schema::hasColumn('service_requests', 'form_data')) {
                $table->json('form_data')->nullable()->after('documents');
            }
        });

        Schema::dropIfExists('ticket_messages');
        Schema::dropIfExists('support_tickets');
        Schema::dropIfExists('request_documents');

        Schema::create('request_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('service_request_id')->index();
            $table->string('document_name');
            $table->string('file_path');
            $table->string('file_name')->nullable();
            $table->string('status')->default('Pending')->index(); // Pending, Received, Accepted, Rejected, Requires Correction
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });

        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique()->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('service_request_id')->nullable()->index();
            $table->string('subject');
            $table->string('category')->default('General');
            $table->string('status')->default('Open')->index(); // Open, In Progress, Resolved, Closed
            $table->string('priority')->default('Normal'); // Low, Normal, High, Urgent
            $table->unsignedBigInteger('assigned_staff_id')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('ticket_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('support_ticket_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->text('message');
            $table->json('attachments')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_messages');
        Schema::dropIfExists('support_tickets');
        Schema::dropIfExists('request_documents');

        Schema::table('service_requests', function (Blueprint $table) {
            $table->dropColumn(['reference_number', 'assigned_staff_id', 'amount_paid', 'outstanding_balance', 'form_data']);
        });

        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['service_fee', 'processing_fee', 'processing_days', 'category', 'required_documents', 'custom_fields']);
        });
    }
};
