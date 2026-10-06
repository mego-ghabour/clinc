<?php
require __DIR__."/vendor/autoload.php";
$app = require_once __DIR__."/bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$c = new \App\Models\Clinic();
$c->name = "Test Route";
$c->status = "????";
$c->save();
$id = $c->id;
$c->delete();

$request = Illuminate\Http\Request::create("/clinics/".$id, "DELETE");
$response = $kernel->handle($request);
echo "Status: " . $response->getStatusCode() . "\n";
echo "Content: " . substr($response->getContent(), 0, 500) . "\n";

