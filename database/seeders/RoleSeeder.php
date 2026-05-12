<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Role::findOrCreate('admin');
        $staff = Role::findOrCreate('staff');

        $admin->syncPermissions(Permission::all());

        $staff->syncPermissions([
            'view_any_applicant',
            'view_applicant',
            'delete_applicant',
            'delete_any_applicant',
            'view_any_collectiv',
            'view_collectiv',
            'delete_collectiv',
            'delete_any_collectiv',
        ]);
    }
}
