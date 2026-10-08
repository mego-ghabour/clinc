<?php
$content = file_get_contents("app/Http/Controllers/PatientController.php");
$fixed1 = mb_convert_encoding($content, "Windows-1252", "UTF-8");
$fixed2 = mb_convert_encoding($content, "ISO-8859-1", "UTF-8");
file_put_contents("test1.txt", $fixed1);
file_put_contents("test2.txt", $fixed2);

