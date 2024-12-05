<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewFieldIsDraftToEmails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('emails', function (Blueprint $table) {
            $table->boolean('is_draft')->default(0);
            $table->enum('type_draft', ['new', 'reply', 'forward'])->nullable();
            $table->text('attachments_draft')->nullable();
            $table->string('cc_email')->nullable();
            $table->string('bcc_email')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('emails', function (Blueprint $table) {
            $table->dropColumn('is_draft');
            $table->dropColumn('type_draft');
            $table->dropColumn('attachments_draft');
            $table->dropColumn('cc_email');
            $table->dropColumn('bcc_email');
        });
    }
}
