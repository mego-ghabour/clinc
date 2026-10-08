<?php
require __DIR__."/vendor/autoload.php";
\$app = require_once __DIR__."/bootstrap/app.php";
\$kernel = \$app->make(Illuminate\Contracts\Console\Kernel::class);
\$kernel->bootstrap();

\$user = \App\Models\User::firstOrCreate(
    ["username" => "admin"],
    [
        "name" => "???? ??????",
        "email" => "admin@clinc.local",
        "password" => bcrypt("password"),
        "phone" => "0500000000",
        "status" => "???",
        "role" => "Super Admin"
    ]
);

\$user->assignRole("Super Admin");
echo "Admin user created: username=admin, password=password";

