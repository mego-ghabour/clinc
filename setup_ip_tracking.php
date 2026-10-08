<?php
$content = file_get_contents("app/Providers/AppServiceProvider.php");

$ipTrackingLogic = <<<PHP
\Spatie\Activitylog\Models\Activity::saving(function (\$activity) {
            \$properties = \$activity->properties ? \$activity->properties->toArray() : [];
            if (!isset(\$properties['ip'])) {
                \$properties['ip'] = request()->ip();
                \$properties['user_agent'] = request()->userAgent();
                \$activity->properties = \$properties;
            }
        });
PHP;

$content = preg_replace("/Event::listen\(Login::class, function /", $ipTrackingLogic . "\n\n        Event::listen(Login::class, function ", $content);

file_put_contents("app/Providers/AppServiceProvider.php", $content);

