<?php
$content = file_get_contents("app/Models/User.php");
$content = str_replace(
    "protected \$fillable = [\n        'name',\n        'email',\n        'password',\n        'username',",
    "protected \$fillable = [\n        'name',\n        'email',\n        'password',\n        'username',\n        'avatar',",
    $content
);
file_put_contents("app/Models/User.php", $content);

