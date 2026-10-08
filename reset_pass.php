<?php $u = App\Models\User::where('email', 'maged.ghabour2016@gmail.com')->first(); if($u){ $u->password = Hash::make('password'); $u->save(); echo 'Done'; } else { echo 'Not Found'; }
