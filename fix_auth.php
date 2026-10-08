<?php $u = App\Models\User::where('email', 'maged.ghabour2016@gmail.com')->first(); if($u){ $u->status = '???'; $u->save(); echo 'Maged activated.'; }
