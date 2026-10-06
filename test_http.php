<?php
require __DIR__."/vendor/autoload.php";
$app = require_once __DIR__."/bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$c = new \App\Models\Clinic();
$c->name = "Test 404";
$c->status = "????";
$c->save();
$id = $c->id;
$c->delete();

$request = Illuminate\Http\Request::create("/clinics/".$id, "DELETE");
$app->instance(Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class, new class {
    public function handle($request, $next) { return $next($request); }
});

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request);
echo "Status: " . $response->getStatusCode() . "\n";

