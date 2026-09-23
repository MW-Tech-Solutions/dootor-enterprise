<?php

namespace App\Providers;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

require_once __DIR__ . '/../Helpers/functions.php';

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\Schema::defaultStringLength(191);
        \Illuminate\Pagination\Paginator::useBootstrapFive();

        View::composer('*', function ($view) {
            $settings = null;
            try {
                $settings = SystemSetting::first();
            } catch (\Exception $e) {
                // Database might not be migrated yet
            }
            $view->with('settings', $settings);
        });
    }
}

