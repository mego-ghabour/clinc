<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Automated Database Backup Schedule
\Illuminate\Support\Facades\Schedule::command('app:backup')
    ->dailyAt('02:00')
    ->when(function () {
        return \App\Models\Setting::where('key', 'backup_auto_enabled')->value('value') === '1';
    });

