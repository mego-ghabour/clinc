<?php
$f = "c:/Users/fkrtk/Desktop/Clinc/resources/views/visits/index.blade.php";
if (file_exists($f)) {
    $c = file_get_contents($f);
    if (strpos($c, "isModalOpen: {{ request('open_modal') ? 'true' : 'false' }}") === false) {
        $c = str_replace(
            "isModalOpen: false,",
            "isModalOpen: {{ request('open_modal') ? 'true' : 'false' }},",
            $c
        );
        $c = str_replace(
            "patient_id: '',",
            "patient_id: '{{ request(\'patient_id\') ?? \'\' }}',",
            $c
        );
        file_put_contents($f, $c);
    }
}
echo "Fixed visits index modal.\n";
