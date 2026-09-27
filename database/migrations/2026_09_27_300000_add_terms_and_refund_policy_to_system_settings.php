<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('system_settings')) {
            Schema::table('system_settings', function (Blueprint $table) {
                if (!Schema::hasColumn('system_settings', 'terms_conditions')) {
                    $table->longText('terms_conditions')->nullable()->after('template_service_update');
                }
                if (!Schema::hasColumn('system_settings', 'refund_policy')) {
                    $table->longText('refund_policy')->nullable()->after('terms_conditions');
                }
            });

            // Seed default contents if empty
            $setting = DB::table('system_settings')->first();
            $defaultTerms = <<<HTML
<h5>1. Platform Usage & Services</h5>
<p>By creating a client account on DOOTOR ENTERPRISES, you agree to submit authentic application details and supporting documents for official vetting, consultation, and document authentication services.</p>

<h5>2. Client Obligations</h5>
<p>Applicants are responsible for ensuring all provided identity information, names, contact numbers, and uploaded files are accurate, complete, and valid.</p>

<h5>3. Confidentiality & Data Protection</h5>
<p>DOOTOR ENTERPRISES handles all personal data and document records under strict confidentiality protocols. Data is only accessible by assigned processing officers and authorized administrators.</p>

<h5>4. Disclaimer</h5>
<p>DOOTOR ENTERPRISES is an independent document consultancy enterprise and is not a government annex or official embassy branch.</p>
HTML;

            $defaultRefund = <<<HTML
<h5>1. Processing & Cancellation Window</h5>
<p>Clients may request a full refund prior to service assignment or initial application review. Once document processing or officer assignment has commenced, partial administrative processing fees apply.</p>

<h5>2. Refund Eligibility</h5>
<p>Full refunds are granted if DOOTOR ENTERPRISES is unable to initiate processing for your request within the designated service timeline due to internal operational issues.</p>

<h5>3. Non-Refundable Items</h5>
<p>Government statutory filing fees or official third-party courier dispatch costs already disbursed on behalf of the applicant are non-refundable.</p>

<h5>4. Requesting a Refund</h5>
<p>To request a refund, please contact customer support through your client dashboard support portal with your application reference number.</p>
HTML;

            if ($setting) {
                DB::table('system_settings')->where('id', $setting->id)->update([
                    'terms_conditions' => $setting->terms_conditions ?: $defaultTerms,
                    'refund_policy' => $setting->refund_policy ?: $defaultRefund,
                ]);
            } else {
                DB::table('system_settings')->insert([
                    'platform_name' => 'DOOTOR ENTERPRISES',
                    'terms_conditions' => $defaultTerms,
                    'refund_policy' => $defaultRefund,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('system_settings')) {
            Schema::table('system_settings', function (Blueprint $table) {
                if (Schema::hasColumn('system_settings', 'terms_conditions')) {
                    $table->dropColumn('terms_conditions');
                }
                if (Schema::hasColumn('system_settings', 'refund_policy')) {
                    $table->dropColumn('refund_policy');
                }
            });
        }
    }
};
