<?php
$f = "c:/Users/fkrtk/Desktop/Clinc/app/Models/Invoice.php";
$c = file_get_contents($f);
$c = str_replace(
    '$lastId = self::orderBy(\'id\', \'desc\')->first()?->id ?? 0;',
    '$lastId = self::withTrashed()->orderBy(\'id\', \'desc\')->first()?->id ?? 0;',
    $c
);
file_put_contents($f, $c);
echo "Fixed Invoice model.\n";
