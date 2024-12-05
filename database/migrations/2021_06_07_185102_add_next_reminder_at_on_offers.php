<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNextReminderAtOnOffers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('offers', function (Blueprint $table) {
            $table->date('next_reminder_at')->nullable()->after('offer_status');
            $table->integer('times_reminded')->nullable()->after('next_reminder_at');
            $table->boolean('extra_fee')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('offers', function (Blueprint $table) {
            $table->dropColumn('next_reminder_at');
            $table->dropColumn('times_reminded');
            $table->dropColumn('extra_fee');
        });
    }
}
