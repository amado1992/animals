<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->integer('old_id')->nullable();
            $table->string('first_name', 255)->nullable();
            $table->string('last_name', 255)->nullable();
            $table->enum('title', ['Mr.', 'Mrs.', 'Ms.', 'Dr.', 'Ing.'])->nullable();
            $table->string('position', 150)->nullable();
            $table->string('email', 100)->nullable()->unique();
            $table->string('domain_name', 255)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('mobile_phone', 30)->nullable();
            //$table->string('language', 6)->nullable();
            $table->foreignId('organisation_id')->nullable()->constrained()->onDelete('cascade');
            $table->enum('source', ['website', 'crm'])->nullable();
            $table->enum('member_approved_status', ['active', 'no_active', 'question', 'website_not_working', 'no_websites', 'cancel'])->nullable();
            $table->enum('mailing_category', ['all_mailings', 'no_mailings', 'unsubscribed', 'not_approved_for_website', 'not_serious', 'not_valid_anymore', 'only_for_supplying', 'unknown'])->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('inserted_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contacts');
    }
}
