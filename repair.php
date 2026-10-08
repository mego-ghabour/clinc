<?php
$files = [
    "app/Http/Controllers/PatientController.php",
    "resources/views/patients/index.blade.php"
];

$migrations = glob("database/migrations/*_create_patients_table.php");
if (count($migrations) > 0) {
    $files[] = $migrations[0];
}

foreach ($files as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        $fixed = mb_convert_encoding($content, "Windows-1252", "UTF-8");
        if (preg_match("/[\x{0600}-\x{06FF}]/u", $fixed)) {
            file_put_contents($file, $fixed);
            echo "Repaired: $file\n";
        } else {
            echo "Skipped: $file\n";
        }
    }
}
