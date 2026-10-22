<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Create faqs table if not exists
        if (!Schema::hasTable('faqs')) {
            Schema::create('faqs', function (Blueprint $table) {
                $table->id();
                $table->string('question');
                $table->text('answer');
                $table->string('category')->nullable()->default('General');
                $table->integer('sort_order')->default(0);
                $table->string('status')->default('Active'); // Active, Inactive
                $table->timestamps();
            });

            // Seed initial FAQ entries
            $defaultFaqs = [
                [
                    'question' => 'How does DOOTOR ENTERPRISES assist with Nigerian documentation?',
                    'answer' => 'We provide specialized administrative assistance, form completion support, fee processing, document review, and appointment scheduling support for diaspora clients seeking Nigerian passports, visas, BVN, NIN, and related civil registry documentation.',
                    'category' => 'General',
                    'sort_order' => 1,
                    'status' => 'Active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'question' => 'Are you an official government office or embassy?',
                    'answer' => 'No. DOOTOR ENTERPRISES is an independent document assistance consultancy. We are not government officials, diplomats, or an annex of the Nigerian High Commission or any embassy. We assist applicants with legal, procedural, and logistical requirements.',
                    'category' => 'Legal & Compliance',
                    'sort_order' => 2,
                    'status' => 'Active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'question' => 'How long does document processing typically take?',
                    'answer' => 'Processing times depend on the specific document category, regulatory authority processing schedules, and embassy appointment backlogs. Estimated timelines are provided for each service on your client dashboard.',
                    'category' => 'Processing',
                    'sort_order' => 3,
                    'status' => 'Active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'question' => 'Is my identity data and uploaded documentation secure?',
                    'answer' => 'Yes. We maintain strict data security and confidential document storage protocols. Personal records and identity files are encrypted, restricted to assigned vetting personnel, and never sold to third parties.',
                    'category' => 'Security',
                    'sort_order' => 4,
                    'status' => 'Active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ];

            DB::table('faqs')->insert($defaultFaqs);
        }

        // 2. Add social media and contact fields to system_settings if not exists
        if (Schema::hasTable('system_settings')) {
            Schema::table('system_settings', function (Blueprint $table) {
                if (!Schema::hasColumn('system_settings', 'facebook_url')) {
                    $table->string('facebook_url')->nullable()->after('privacy_policy');
                }
                if (!Schema::hasColumn('system_settings', 'twitter_url')) {
                    $table->string('twitter_url')->nullable()->after('facebook_url');
                }
                if (!Schema::hasColumn('system_settings', 'instagram_url')) {
                    $table->string('instagram_url')->nullable()->after('twitter_url');
                }
                if (!Schema::hasColumn('system_settings', 'linkedin_url')) {
                    $table->string('linkedin_url')->nullable()->after('instagram_url');
                }
                if (!Schema::hasColumn('system_settings', 'whatsapp_number')) {
                    $table->string('whatsapp_number')->nullable()->after('linkedin_url');
                }
                if (!Schema::hasColumn('system_settings', 'youtube_url')) {
                    $table->string('youtube_url')->nullable()->after('whatsapp_number');
                }
                if (!Schema::hasColumn('system_settings', 'tiktok_url')) {
                    $table->string('tiktok_url')->nullable()->after('youtube_url');
                }
                if (!Schema::hasColumn('system_settings', 'contact_email')) {
                    $table->string('contact_email')->nullable()->after('tiktok_url');
                }
                if (!Schema::hasColumn('system_settings', 'contact_phone')) {
                    $table->string('contact_phone')->nullable()->after('contact_email');
                }
                if (!Schema::hasColumn('system_settings', 'contact_address')) {
                    $table->text('contact_address')->nullable()->after('contact_phone');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faqs');

        if (Schema::hasTable('system_settings')) {
            Schema::table('system_settings', function (Blueprint $table) {
                $table->dropColumn([
                    'facebook_url',
                    'twitter_url',
                    'instagram_url',
                    'linkedin_url',
                    'whatsapp_number',
                    'youtube_url',
                    'tiktok_url',
                    'contact_email',
                    'contact_phone',
                    'contact_address',
                ]);
            });
        }
    }
};
