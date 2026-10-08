<?php
$content = file_get_contents("app/Http/Controllers/SystemUserController.php");
$showMethod = <<<PHP

    public function show(\$id)
    {
        \$user = User::with("roles", "permissions")->findOrFail(\$id);
        \$activities = \Spatie\Activitylog\Models\Activity::where("causer_id", \$id)
                        ->latest()
                        ->take(50)
                        ->get();
        \$allPermissions = \Spatie\Permission\Models\Permission::all()->groupBy(function(\$permission) {
            return explode("_", \$permission->name)[1] ?? "general";
        });
        
        return view("users.show", compact("user", "activities", "allPermissions"));
    }
PHP;

$content = preg_replace("/public function store/", ltrim(\$showMethod) . "\n\n    public function store", \$content);
file_put_contents("app/Http/Controllers/SystemUserController.php", \$content);

