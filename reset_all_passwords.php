<?php
$u1 = App\Models\User::where('email', 'admin@clinc.local')->first();
if ($u1) {
    $u1->password = \Illuminate\Support\Facades\Hash::make('password');
    $u1->save();
}

$u2 = App\Models\User::where('email', 'maged.ghabour2016@gmail.com')->first();
if ($u2) {
    $u2->password = \Illuminate\Support\Facades\Hash::make('password');
    $u2->save();
}
echo "Passwords reset to 'password' for both accounts.\n";
