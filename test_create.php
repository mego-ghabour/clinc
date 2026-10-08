<?php
require __DIR__."/vendor/autoload.php";
$app = require_once __DIR__."/bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
try {
    $p = \App\Models\Patient::create([
        "first_name" => "Ali",
        "family_name" => "Ahmad"
    ]);
    echo "Created: " . $p->id . "\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

