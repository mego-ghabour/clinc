<?php $u = App\Models\User::where('email', 'admin@clinc.local')->first(); print_r($u->toArray());
