<?php
$f = "c:/Users/fkrtk/Desktop/Clinc/resources/views/users/index.blade.php";
$c = file_get_contents($f);

// Fix the openEditModal attribute
$c = str_replace(
    '<button @click="openEditModal({{ $user }}, {{ $user->roles->pluck(\'name\') }})"',
    "<button @click='openEditModal(@json(\$user), @json(\$user->roles->pluck(\"name\")))'",
    $c
);

file_put_contents($f, $c);
echo "Fixed Users index.\n";
