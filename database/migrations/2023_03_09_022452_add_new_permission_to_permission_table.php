<?php

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewPermissionToPermissionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $role = Role::where('name', 'admin')->first();
        if (!empty($role)) {
            $users                      = User::whereRoleIs($role->name)->get();
            $permission                 = new Permission();
            $permission['name']         = 'inbox.info';
            $permission['display_name'] = 'Can view email info@';
            $permission->save();
            $role->attachPermission($permission);
            foreach ($users as $user) {
                $user->attachPermission($permission);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('permissions', function (Blueprint $table) {
            //
        });
    }
}
