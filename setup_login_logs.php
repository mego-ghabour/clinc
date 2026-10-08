<?php
$content = file_get_contents("app/Providers/AppServiceProvider.php");
$useStatements = "use Illuminate\Auth\Events\Login;\nuse Illuminate\Auth\Events\Logout;\nuse Illuminate\Support\Facades\Event;\nuse Spatie\Activitylog\Facades\Activity;\n";
$content = preg_replace("/namespace App\\\Providers;/", "namespace App\Providers;\n\n" . $useStatements, $content);

$listeners = <<<PHP
Event::listen(Login::class, function (\$event) {
            activity()
                ->causedBy(\$event->user)
                ->event('login')
                ->log('????? ???? ??????');
        });

        Event::listen(Logout::class, function (\$event) {
            if (\$event->user) {
                activity()
                    ->causedBy(\$event->user)
                    ->event('logout')
                    ->log('????? ???? ?? ??????');
            }
        });
PHP;

$content = preg_replace("/public function boot\(\): void\n    {/", "public function boot(): void\n    {\n        " . $listeners, $content);

file_put_contents("app/Providers/AppServiceProvider.php", $content);

