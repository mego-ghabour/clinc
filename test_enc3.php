<?php
$content = file_get_contents("app/Http/Controllers/PatientController.php");
$fixed3 = mb_convert_encoding($content, "CP1256", "UTF-8");
file_put_contents("test3.txt", $fixed3);

