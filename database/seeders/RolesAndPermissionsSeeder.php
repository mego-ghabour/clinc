<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Array of permissions grouped by module
        $permissions = [
            'المرضى' => [
                'view_patients',
                'create_patients',
                'edit_patients',
                'delete_patients',
            ],
            'الأطباء' => [
                'view_doctors',
                'create_doctors',
                'edit_doctors',
                'delete_doctors',
            ],
            'المواعيد' => [
                'view_appointments',
                'create_appointments',
                'edit_appointments',
                'delete_appointments',
            ],
            'العيادات' => [
                'view_clinics',
                'create_clinics',
                'edit_clinics',
                'delete_clinics',
            ],
            'الإعدادات والصلاحيات' => [
                'view_settings',
                'manage_roles',
                'manage_users',
                'view_audit_logs',
            ]
        ];

        // Create permissions
        foreach ($permissions as $group => $perms) {
            foreach ($perms as $perm) {
                Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
            }
        }

        // Create default roles and assign existing permissions
        $roleSuperAdmin = Role::firstOrCreate(['name' => 'Super Admin']);
        // Super Admin gets all permissions via Gate::before in AuthServiceProvider usually, but let's assign explicitly just in case
        $roleSuperAdmin->givePermissionTo(Permission::all());

        $roleReceptionist = Role::firstOrCreate(['name' => 'موظف استقبال']);
        $roleReceptionist->givePermissionTo([
            'view_patients',
            'create_patients',
            'edit_patients',
            'view_appointments',
            'create_appointments',
            'edit_appointments',
            'view_doctors',
            'view_clinics'
        ]);

        $roleDoctor = Role::firstOrCreate(['name' => 'طبيب']);
        $roleDoctor->givePermissionTo([
            'view_patients',
            'view_appointments',
            'edit_appointments', // to mark as completed
        ]);
        
        $roleNurse = Role::firstOrCreate(['name' => 'ممرض']);
        $roleNurse->givePermissionTo([
            'view_patients',
            'edit_patients',
            'view_appointments',
        ]);
    }
}
