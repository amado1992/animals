<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToTasks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tasks', function (Blueprint $table) {
            if (Schema::hasColumn('tasks', 'action_date')) {
                $table->dropColumn('action_date');
            }

            $table->foreignId('created_by')->after('finished_at')->nullable()->constrained('users');

            //$table->enum('quick_action_date', ['today', 'tomorrow', 'week', 'month', 'specific', 'none'])->default('none');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn('created_by');
            //$table->dropColumn('quick_action_date');
        });
    }
}
