<?php
$f = "c:/Users/fkrtk/Desktop/Clinc/resources/views/booking/index.blade.php";
$c = file_get_contents($f);

$c = str_replace(
    '<link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800;900&display=swap" rel="stylesheet">',
    '@php $appFont = \App\Models\Setting::where("key", "app_font")->value("value") ?? "Tajawal"; $fontFamily = $appFont === "Cairo" ? "Cairo" : "Tajawal"; @endphp @if($fontFamily === "Cairo") <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet"> @else <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800;900&display=swap" rel="stylesheet"> @endif',
    $c
);

$c = str_replace(
    "body { font-family: 'Tajawal', sans-serif; background-color: #f8fafc; }",
    "body { font-family: '{{ \$fontFamily }}', sans-serif; background-color: #f8fafc; }",
    $c
);

$c = str_replace(
    "sans: ['Tajawal', 'sans-serif'],",
    "sans: ['{{ \$fontFamily }}', 'sans-serif'],",
    $c
);

file_put_contents($f, $c);
echo "Font applied.\n";
