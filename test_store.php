<?php
require __DIR__."/vendor/autoload.php";
$app = require_once __DIR__."/bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$request = Illuminate\Http\Request::create("/patients", "POST", [
    "first_name" => "Test",
    "family_name" => "Patient",
    "mobile" => "0500000000",
    "id_number" => "1234567890"
]);

// Bypass CSRF for testing
$app->instance(Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class, new class {
    public function handle($request, $next) { return $next($request); }
});

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request);

echo "Status: " . $response->getStatusCode() . "\n";
if ($response->getSession() && $response->getSession()->has("errors")) {
    echo "Errors: " . json_encode($response->getSession()->get("errors")->getMessages()) . "\n";
}
if ($response->getSession() && $response->getSession()->has("success")) {
    echo "Success: " . $response->getSession()->get("success") . "\n";
}

