<?php
$f = "c:/Users/fkrtk/Desktop/Clinc/resources/views/patients/show.blade.php";
$c = file_get_contents($f);

// 1. Fix Edit Patient Data button
$c = preg_replace(
    '/<a(.*?)>تعديل بيانات المريض<\/a>/s',
    '<button @click="$dispatch(\'open-edit-modal\')" class="bg-blue-50 text-blue-600 hover:bg-blue-100 px-4 py-2.5 rounded-xl text-sm font-bold transition shadow-sm border border-blue-100 flex items-center gap-2"><i class="fa-solid fa-pen"></i> تعديل بيانات المريض</button>',
    $c
);
// Also catch the case where it might just be the text, but let's replace the whole block manually
$c = str_replace(
    '<button class="bg-blue-50 text-blue-600 hover:bg-blue-100 px-4 py-2.5 rounded-xl text-sm font-bold transition shadow-sm border border-blue-100 flex items-center gap-2">تعديل بيانات المريض</button>',
    '<button @click="$dispatch(\'open-edit-modal\')" class="bg-blue-50 text-blue-600 hover:bg-blue-100 px-4 py-2.5 rounded-xl text-sm font-bold transition shadow-sm border border-blue-100 flex items-center gap-2"><i class="fa-solid fa-pen"></i> تعديل بيانات المريض</button>',
    $c
);

// Actually, let's just do a safer brute-force replacement for all 3 buttons using their icons:
// Edit Button
$c = preg_replace(
    '/<(button|a)[^>]*?>\s*<i class="fa-solid fa-pen"><\/i> تعديل بيانات المريض\s*<\/(button|a)>/s',
    '<button @click="$dispatch(\'open-edit-modal\')" class="bg-blue-50 text-blue-600 hover:bg-blue-100 px-4 py-2.5 rounded-xl text-sm font-bold transition shadow-sm border border-blue-100 flex items-center gap-2"><i class="fa-solid fa-pen"></i> تعديل بيانات المريض</button>',
    $c
);

// Visit Button
$c = preg_replace(
    '/<(button|a)[^>]*?>\s*<i class="fa-solid fa-notes-medical"><\/i> بدء زيارة طبية \(كشف\)\s*<\/(button|a)>/s',
    '<a href="{{ route(\'visits.create\', [\'patient_id\' => $patient->id]) }}" class="bg-teal-custom hover:bg-teal-700 text-white px-6 py-3 rounded-xl text-sm font-bold transition flex items-center justify-center gap-2 shadow-sm"><i class="fa-solid fa-notes-medical"></i> بدء زيارة طبية (كشف)</a>',
    $c
);

// Appointment Button
$c = preg_replace(
    '/<(button|a)[^>]*?>\s*<i class="fa-solid fa-calendar-plus"><\/i> حجز موعد جديد\s*<\/(button|a)>/s',
    '<a href="{{ route(\'appointments.create\', [\'patient_id\' => $patient->id]) }}" class="bg-gray-50 hover:bg-gray-100 text-teal-700 border border-gray-200 px-6 py-3 rounded-xl text-sm font-bold transition flex items-center justify-center gap-2 shadow-sm"><i class="fa-solid fa-calendar-plus"></i> حجز موعد جديد</a>',
    $c
);

file_put_contents($f, $c);
echo "Fixed 3 buttons.\n";
