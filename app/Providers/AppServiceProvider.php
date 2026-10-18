<?php

namespace App\Providers;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Event;
use Spatie\Activitylog\Facades\Activity;
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
        \Spatie\Activitylog\Models\Activity::saving(function ($activity) {
            $properties = $activity->properties ? $activity->properties->toArray() : [];
            if (!isset($properties['ip'])) {
                $properties['ip'] = request()->ip() ?? '127.0.0.1';
                $properties['user_agent'] = request()->userAgent() ?? 'غير معروف';
                $activity->properties = $properties;
            }
        });

        Event::listen(Login::class, function ($event) {
            activity()
                ->causedBy($event->user)
                ->event('login')
                ->log('تسجيل دخول للنظام');
        });

        Event::listen(Logout::class, function ($event) {
            if ($event->user) {
                activity()
                    ->causedBy($event->user)
                    ->event('logout')
                    ->log('تسجيل خروج من النظام');
            }
        });
        
        try {
            if (\Schema::hasTable('settings')) {
                $settings = \App\Models\Setting::pluck('value', 'key')->toArray();
                \View::share('global_settings', $settings);

                // Dynamically apply Timezone
                if (!empty($settings['app_timezone'])) {
                    config(['app.timezone' => $settings['app_timezone']]);
                    date_default_timezone_set($settings['app_timezone']);
                }

                // Dynamically apply Locale
                if (!empty($settings['app_locale'])) {
                    config(['app.locale' => $settings['app_locale']]);
                    app()->setLocale($settings['app_locale']);
                }
            }
        } catch (\Exception $e) {
            // Ignore for artisan commands
        }
    }
}
