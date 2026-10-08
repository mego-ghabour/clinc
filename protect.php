<?php
$f = "c:/Users/fkrtk/Desktop/Clinc/app/Http/Controllers/SystemUserController.php";
$c = file_get_contents($f);

$c = preg_replace(
    '/public function toggleStatus\(\$id\)\s*\{\s*\$user = User::findOrFail\(\$id\);/',
    'public function toggleStatus($id)
    {
        $user = User::findOrFail($id);
        if ($user->hasRole("Super Admin")) return back()->withErrors(["error" => "حماية أمنية: يُمنع إيقاف حساب يمتلك دور (مدير النظام 👑)."]);',
    $c
);

$c = preg_replace(
    '/public function updatePermissions\(Request \$request, \$id\)\s*\{\s*\$user = User::findOrFail\(\$id\);/',
    'public function updatePermissions(Request $request, $id)
    {
        $user = User::findOrFail($id);
        if ($user->hasRole("Super Admin")) return back()->withErrors(["error" => "حماية أمنية: مدير النظام يمتلك كافة الصلاحيات."]);',
    $c
);

file_put_contents($f, $c);
echo "Protected successfully!\n";
