<?php
$files = [
    "c:/Users/fkrtk/Desktop/Clinc/resources/views/layouts/app.blade.php",
    "c:/Users/fkrtk/Desktop/Clinc/resources/views/booking/index.blade.php"
];

$fontLogicOld = <<<'HTML'
    <!-- Dynamic Font from Settings -->
    @php
        $appFont = \App\Models\Setting::where('key', 'app_font')->value('value') ?? 'Tajawal';
        $fontFamily = $appFont === 'Cairo' ? 'Cairo' : 'Tajawal';
    @endphp
    
    @if($fontFamily === 'Cairo')
        <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    @else
        <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800;900&display=swap" rel="stylesheet">
    @endif
HTML;

$fontLogicNew = <<<'HTML'
    <!-- Dynamic Font from Settings -->
    @php
        $appFont = \App\Models\Setting::where('key', 'app_font')->value('value') ?? 'Tajawal';
        $fontFamily = in_array($appFont, ['Cairo', 'Tajawal', 'Alexandria', 'Almarai']) ? $appFont : 'Tajawal';
    @endphp
    <link href="https://fonts.googleapis.com/css2?family={{ $fontFamily }}:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
HTML;

foreach ($files as $f) {
    if (file_exists($f)) {
        $c = file_get_contents($f);
        $c = str_replace($fontLogicOld, $fontLogicNew, $c);
        file_put_contents($f, $c);
    }
}
echo "Applied dynamic fonts.\n";
