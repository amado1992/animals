<?php

use App\Models\Permission;
use App\Models\Task;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewFieldStatusToTasks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tasks', function (Blueprint $table) {
            \DB::statement("Alter table tasks Add column status enum('new', 'forapproval', 'complete', 'incomplete') default 'new';");
        });

        $task = Task::whereNotNull('finished_at')->update(['status' => 'complete']);

        Permission::create(['name' => 'tasks.complete', 'display_name' => 'Complete Tasks']);
        Permission::create(['name' => 'tasks.view-all', 'display_name' => 'View All Tasks']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}
