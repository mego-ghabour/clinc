<?php
require __DIR__."/vendor/autoload.php";
$app = require_once __DIR__."/bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$c = new \App\Models\Clinic();
$c->name = "Test";
$c->status = "????";
$c->save();
echo "ID: " . $c->id . "\n";
$c->delete();

try {
    $res = app(\App\Http\Controllers\ClinicController::class)->destroy($c->id);
    echo "Response status: " . $res->getStatusCode() . "\n";
    if ($res->getSession()) {
        $errors = $res->getSession()->get("errors");
        if ($errors) echo "Errors: " . json_encode($errors->getMessages()) . "\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

