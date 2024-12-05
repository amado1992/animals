<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemDashboardTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_dashboards', function (Blueprint $table) {
            $table->id();
            $table->nullableMorphs('itemable');
            $table->foreignId('dashboard_id')->nullable()->constrained('dashboards');
            $table->boolean('new')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('item_dashboards');
    }
}
