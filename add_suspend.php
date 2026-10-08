<?php
$content = file_get_contents("app/Http/Controllers/DoctorController.php");

$method = <<<PHP
    public function toggleAccountStatus(\$id)
    {
        \$doctor = User::findOrFail(\$id);
        
        if (\$doctor->status === "???") {
            \$doctor->update(["status" => "?????"]);
            return back()->with("success", "?? ????? ???? ?????? ?????");
        } else {
            \$doctor->update(["status" => "???"]);
            return back()->with("success", "?? ????? ???? ?????? ?????");
        }
    }
PHP;

$content = str_replace("public function toggleStatus(\$id)", $method . "\n\n    public function toggleStatus(\$id)", $content);
file_put_contents("app/Http/Controllers/DoctorController.php", $content);

