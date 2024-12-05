<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $adminRole      = Role::where('name', 'admin')->firstOrFail();
        $projectsRole   = Role::where('name', 'projects')->firstOrFail();
        $transportRole  = Role::where('name', 'transport')->firstOrFail();
        $officeRole     = Role::where('name', 'office')->firstOrFail();
        $bookkeeperRole = Role::where('name', 'bookkeeper')->firstOrFail();
        $websiteRole    = Role::where('name', 'website-user')->firstOrFail();

        $admin = User::create([
            'name'      => 'Yoandris',
            'last_name' => 'Savon',
            'email'     => 'yoandris.savon87@gmail.com',
            'password'  => bcrypt('testnewizs'),
        ]);
        $admin->attachRole($adminRole);

        $admin = User::create([
            'name'      => 'Test3',
            'last_name' => 'Test3',
            'email'     => 'test3@test3.com',
            'password'  => bcrypt('test3izs'),
        ]);
        $admin->attachRole($websiteRole);
    }
}
