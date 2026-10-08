<?php
$f = "c:/Users/fkrtk/Desktop/Clinc/resources/views/patients/show.blade.php";
$c = file_get_contents($f);

// Fix patient show buttons
$c = str_replace('<button class="bg-teal-custom hover:bg-teal-700 text-white px-6 py-3 rounded-xl text-sm font-bold transition flex items-center justify-center gap-2 shadow-sm">',
'<a href="{{ route(\'visits.create\', [\'patient_id\' => $patient->id]) }}" class="bg-teal-custom hover:bg-teal-700 text-white px-6 py-3 rounded-xl text-sm font-bold transition flex items-center justify-center gap-2 shadow-sm">', $c);
$c = str_replace('<i class="fa-solid fa-notes-medical"></i> Ø¨Ø¯Ø¡ Ø²ÙŠØ§Ø±Ø© Ø·Ø¨ÙŠØ© (ÙƒØ´Ù )', '<i class="fa-solid fa-notes-medical"></i> بدء زيارة طبية (كشف)', $c);
$c = str_replace('</button>', '</a>', $c); // Careful with this!

// Let's do string replace with map instead
$map = [
    'Ø·Ø¨Ø§Ø¹Ø© Ø§Ù„Ù…Ù„Ù ' => 'طباعة الملف',
    'ØªØ¹Ø¯ÙŠÙ„ Ø¨ÙŠØ§Ù†Ø§Øª Ø§Ù„Ù…Ø±ÙŠØ¶' => 'تعديل بيانات المريض',
    'Ø±Ø¬ÙˆØ¹' => 'رجوع',
    'Ø¨Ø¯Ø¡ Ø²ÙŠØ§Ø±Ø© Ø·Ø¨ÙŠØ© (ÙƒØ´Ù )' => 'بدء زيارة طبية (كشف)',
    'Ø­Ø¬Ø² Ù…ÙˆØ¹Ø¯ Ø¬Ø¯ÙŠØ¯' => 'حجز موعد جديد',
    'Ù†Ø¸Ø±Ø© Ø¹Ø§Ù…Ø©' => 'نظرة عامة',
    'Ø§Ù„Ø²ÙŠØ§Ø±Ø§Øª Ø§Ù„Ø·Ø¨ÙŠØ© (EMR)' => 'الزيارات الطبية (EMR)',
    'Ø§Ù„Ù…ÙˆØ§Ø¹ÙŠØ¯' => 'المواعيد',
    'Ø¥Ù„ØºØ§Ø¡' => 'إلغاء',
    'Ø­Ù Ø¸ Ø§Ù„ØªØ¹Ø¯ÙŠÙ„Ø§Øª' => 'حفظ التعديلات'
];

// Re-read file to discard the bad replace above
$c = file_get_contents($f);
foreach ($map as $bad => $good) {
    $c = str_replace($bad, $good, $c);
}

// Convert "start visit" to an anchor tag pointing to visit creation (if visits route exists)
$c = preg_replace(
    '/<button(.*?)>\s*<i class="fa-solid fa-notes-medical"><\/i> بدء زيارة طبية \(كشف\)\s*<\/button>/s',
    '<a href="#" class="bg-teal-custom hover:bg-teal-700 text-white px-6 py-3 rounded-xl text-sm font-bold transition flex items-center justify-center gap-2 shadow-sm"><i class="fa-solid fa-notes-medical"></i> بدء زيارة طبية (كشف)</a>',
    $c
);

// Convert "book appointment" to an anchor tag
$c = preg_replace(
    '/<button(.*?)>\s*<i class="fa-solid fa-calendar-plus"><\/i> حجز موعد جديد\s*<\/button>/s',
    '<a href="#" class="bg-gray-50 hover:bg-gray-100 text-teal-700 border border-gray-200 px-6 py-3 rounded-xl text-sm font-bold transition flex items-center justify-center gap-2 shadow-sm"><i class="fa-solid fa-calendar-plus"></i> حجز موعد جديد</a>',
    $c
);

file_put_contents($f, $c);
echo "Fixed patient show buttons.\n";
