<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('analytics_events')) {
            Schema::create('analytics_events', function (Blueprint $table) {
                $table->id();
                $table->string('event_type'); // 'page_view', 'click', 'social_click', 'service_click'
                $table->string('category')->nullable(); // 'social', 'cta', 'service', 'navigation'
                $table->string('label')->nullable(); // e.g. 'whatsapp', 'facebook', 'apply_now', service name
                $table->string('url')->nullable();
                $table->string('ip_address')->nullable();
                $table->text('user_agent')->nullable();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->timestamps();

                $table->index(['event_type', 'created_at']);
                $table->index(['category', 'label']);
            });
        }

        if (!Schema::hasTable('reviews')) {
            Schema::create('reviews', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->string('client_name');
                $table->string('client_title')->nullable(); // e.g. 'Diaspora Applicant (Canada)'
                $table->string('service_name')->nullable(); // e.g. 'International Passport Fast-Track'
                $table->unsignedTinyInteger('rating')->default(5); // 1-5
                $table->text('comment');
                $table->string('avatar_url')->nullable();
                $table->boolean('is_approved')->default(true);
                $table->boolean('is_featured')->default(true);
                $table->timestamps();

                $table->index(['is_approved', 'is_featured']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('analytics_events');
        Schema::dropIfExists('reviews');
    }
};
