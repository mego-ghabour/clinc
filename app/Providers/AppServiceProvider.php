<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
        try {
            if (\Schema::hasTable('settings')) {
                $settings = \App\Models\Setting::pluck('value', 'key')->toArray();
                \View::share('global_settings', $settings);
            }
        } catch (\Exception $e) {
            // Ignore for artisan commands
        }
    }
}
