<?php
require __DIR__."/vendor/autoload.php";
$app = require_once __DIR__."/bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$view = view("patients.index", ["patients" => \App\Models\Patient::paginate(15)])->render();
file_put_contents("rendered.html", $view);
echo "Rendered!";

