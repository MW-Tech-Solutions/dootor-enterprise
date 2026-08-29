<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kyc_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('nin')->nullable();
            $table->string('id_card_url')->nullable();
            $table->string('proof_of_address_url')->nullable();
            $table->string('personal_image_url')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('account_name')->nullable();
            $table->string('account_number')->nullable();
            $table->boolean('terms_agreed')->default(false);
            $table->boolean('privacy_agreed')->default(false);
            $table->boolean('data_consent_agreed')->default(false);
            $table->string('digital_signature')->nullable();
            $table->string('status')->default('NotStarted')->index();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });

        Schema::create('storefront_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('storefront_name')->nullable();
            $table->text('storefront_about')->nullable();
            $table->string('theme')->default('light');
            $table->string('color_palette')->default('default');
            $table->string('background_image_url')->nullable();
            $table->string('gradient')->nullable();
            $table->timestamps();
        });

        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('price', 12, 2)->default(0);
            $table->text('description');
            $table->string('image_url')->nullable();
            $table->string('status')->default('Active')->index();
            $table->timestamps();
        });

        Schema::create('vendor_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
            $table->string('name');
            $table->decimal('price', 12, 2)->default(0);
            $table->text('description');
            $table->string('image_url')->nullable();
            $table->string('status')->default('Active')->index();
            $table->timestamps();

            $table->unique(['vendor_id', 'service_id']);
        });

        Schema::create('service_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->nullable()->constrained('services')->nullOnDelete();
            $table->foreignId('vendor_service_id')->nullable()->constrained('vendor_services')->nullOnDelete();
            $table->string('service_name');
            $table->decimal('price', 12, 2)->default(0);
            $table->foreignId('vendor_id')->constrained('users')->cascadeOnDelete();
            $table->string('vendor_name')->nullable();
            $table->foreignId('client_id')->constrained('users')->cascadeOnDelete();
            $table->string('client_name');
            $table->string('client_email');
            $table->string('status')->default('Awaiting Payment')->index();
            $table->string('payment_reference')->nullable()->index();
            $table->string('payment_gateway')->nullable();
            $table->string('payment_status')->default('Unpaid')->index();
            $table->json('documents')->nullable();
            $table->timestamps();
        });

        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('platform_name')->nullable();
            $table->string('logo_url')->nullable();
            $table->string('default_currency')->default('USD');
            $table->boolean('maintenance_mode')->default(false);
            $table->string('payment_gateway')->nullable();
            $table->boolean('payments_enabled')->default(true);
            $table->string('payment_mode')->default('test');
            $table->string('paystack_public_key')->nullable();
            $table->string('paystack_secret_key')->nullable();
            $table->string('paystack_base_url')->nullable();
            $table->string('credo_public_key')->nullable();
            $table->string('credo_secret_key')->nullable();
            $table->string('credo_base_url')->nullable();
            $table->string('smtp_host')->nullable();
            $table->unsignedInteger('smtp_port')->nullable();
            $table->string('smtp_user')->nullable();
            $table->string('smtp_pass')->nullable();
            $table->string('smtp_encryption')->nullable();
            $table->string('smtp_sender_name')->nullable();
            $table->string('smtp_sender_email')->nullable();
            $table->longText('template_vendor_approval')->nullable();
            $table->longText('template_client_registration')->nullable();
            $table->longText('template_payment_confirmation')->nullable();
            $table->longText('template_service_update')->nullable();
            $table->timestamps();
        });

        Schema::create('archived_clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('client_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('client_name');
            $table->string('client_email');
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('archived_clients');
        Schema::dropIfExists('system_settings');
        Schema::dropIfExists('service_requests');
        Schema::dropIfExists('vendor_services');
        Schema::dropIfExists('services');
        Schema::dropIfExists('storefront_settings');
        Schema::dropIfExists('kyc_profiles');
    }
};
