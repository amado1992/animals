<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = Permission::all();

        $adminRole = Role::create([
            'name'         => 'admin',
            'display_name' => 'Administrator', // optional
        ]);

        $projectsRole = Role::create([
            'name'         => 'projects',
            'display_name' => 'Projects', // optional
        ]);

        $transportRole = Role::create([
            'name'         => 'transport',
            'display_name' => 'Transport', // optional
        ]);

        $officeRole = Role::create([
            'name'         => 'office',
            'display_name' => 'Office',
        ]);

        $bookkeeperRole = Role::create([
            'name'         => 'bookkeeper',
            'display_name' => 'Bookkeeper',
        ]);

        $websiteRole = Role::create([
            'name'         => 'website-user',
            'display_name' => 'Website user',
        ]);

        foreach ($permissions as $permission) {
            $adminRole->attachPermission($permission);
            //$managerRole->attachPermission($permission);
        }
    }
}
