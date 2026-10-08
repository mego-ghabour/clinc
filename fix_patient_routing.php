<?php
$f = "c:/Users/fkrtk/Desktop/Clinc/resources/views/patients/show.blade.php";
$c = file_get_contents($f);

// Fix "Book Appointment" to point to index with param
$c = str_replace(
    'href="' . route('appointments.create', ['patient_id' => 1]) . '"',
    'href="' . route('appointments.index', ['patient_id' => 1, 'open_modal' => 1]) . '"',
    $c
);
// General regex replace for safety
$c = preg_replace(
    '/route\(\'appointments\.create\'(.*?)\]\)/',
    'route(\'appointments.index\', array_merge$1], [\'open_modal\' => 1]))',
    $c
);
$c = preg_replace(
    '/route\(\'visits\.create\'(.*?)\]\)/',
    'route(\'visits.index\', array_merge$1], [\'open_modal\' => 1]))',
    $c
);

// Actually, preg_replace with array_merge might be messy if I write it wrong. Let's just do it cleanly:
$c = str_replace(
    "route('appointments.create', ['patient_id' => \$patient->id])",
    "route('appointments.index', ['patient_id' => \$patient->id, 'open_modal' => 1])",
    $c
);
$c = str_replace(
    "route('visits.create', ['patient_id' => \$patient->id])",
    "route('visits.index', ['patient_id' => \$patient->id, 'open_modal' => 1])",
    $c
);

file_put_contents($f, $c);
echo "Fixed patient buttons routing.\n";
