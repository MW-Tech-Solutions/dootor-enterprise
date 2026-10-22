<?php

namespace App\Console\Commands;

use App\Services\ApplicationExpirationService;
use Illuminate\Console\Command;

class PurgeExpiredApplications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'applications:purge-expired 
                            {--action= : Override default purge action (delete or mark_expired)} 
                            {--force : Force execution even if auto_purge_enabled is disabled}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically purge or mark expired unfinished halfway applications (default 30 days) and paid incomplete applications (default 3 months) based on dynamic admin configuration.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $settings = ApplicationExpirationService::getSettings();
        $isForce = $this->option('force');

        if (!$settings['auto_purge_enabled'] && !$isForce) {
            $this->warn('Auto-purge is currently disabled in System Settings. Use --force to execute manually.');
            return self::SUCCESS;
        }

        $action = $this->option('action') ?: $settings['purge_action'];

        $this->info("Scanning for expired applications...");
        $this->line("• Draft Inactivity Threshold: {$settings['draft_expiry_value']} {$settings['draft_expiry_unit']}");
        $this->line("• Paid Incomplete Threshold: {$settings['paid_incomplete_expiry_value']} {$settings['paid_incomplete_expiry_unit']}");
        $this->line("• Purge Action: {$action}");

        $results = ApplicationExpirationService::purgeAllExpired($action);

        $this->info("Purge process complete.");
        $this->table(
            ['Category', 'Count Processed'],
            [
                ['Unfinished Drafts Expired', $results['purged_drafts']],
                ['Paid Incomplete Expired', $results['purged_paid']],
                ['Total Purged Applications', $results['total_purged']],
            ]
        );

        return self::SUCCESS;
    }
}
