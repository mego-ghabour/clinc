<?php
$f = "c:/Users/fkrtk/Desktop/Clinc/resources/views/patients/show.blade.php";
$c = file_get_contents($f);

$c = str_replace(
    "{{ route('visits.index', array_merge, ['patient_id' => \$patient->id], ['open_modal' => 1])) }}",
    "{{ route('visits.index', ['patient_id' => \$patient->id, 'open_modal' => 1]) }}",
    $c
);

$c = str_replace(
    "{{ route('appointments.index', array_merge, ['patient_id' => \$patient->id], ['open_modal' => 1])) }}",
    "{{ route('appointments.index', ['patient_id' => \$patient->id, 'open_modal' => 1]) }}",
    $c
);

file_put_contents($f, $c);
echo "Fixed route array_merge error.\n";
