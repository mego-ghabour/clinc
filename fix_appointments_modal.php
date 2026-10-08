<?php
$f = "c:/Users/fkrtk/Desktop/Clinc/resources/views/appointments/index.blade.php";
$c = file_get_contents($f);

// Check if open_modal is already handled, if not inject it
if (strpos($c, "isModalOpen: {{ request('open_modal') ? 'true' : 'false' }}") === false) {
    $c = str_replace(
        "isModalOpen: false,",
        "isModalOpen: {{ request('open_modal') ? 'true' : 'false' }},",
        $c
    );
    // Auto-select patient
    $c = str_replace(
        "patient_id: '',",
        "patient_id: '{{ request(\'patient_id\') ?? \'\' }}',",
        $c
    );
    file_put_contents($f, $c);
}
echo "Fixed appointments index modal.\n";
