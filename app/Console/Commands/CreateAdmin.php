<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class CreateAdmin extends Command
{
    protected $signature = 'app:create-admin';
    protected $description = 'Create default admin account';

    public function handle()
    {
        $user = User::firstOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'مدير النظام',
                'email' => 'admin@clinc.local',
                'password' => bcrypt('password'),
                'phone' => '0500000000',
                'status' => 'نشط',
                'role' => 'Super Admin'
            ]
        );

        $user->assignRole('Super Admin');
        $this->info('Admin created: admin / password');
    }
}
