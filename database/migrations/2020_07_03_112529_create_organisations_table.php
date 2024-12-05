<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrganisationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('organisations', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('domain_name', 255)->nullable();
            $table->string('organisation_type', 5)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('fax', 50)->nullable();
            $table->string('website', 255)->nullable();
            $table->string('facebook_page', 255)->nullable();
            $table->string('address', 255)->nullable();
            $table->string('zipcode', 20)->nullable();
            $table->string('city', 100)->nullable();
            $table->foreignId('country_id')->nullable()->constrained();
            $table->string('vat_number', 100)->nullable();
            //$table->string('language', 6)->nullable();
            $table->enum('level', ['A', 'B', 'C'])->nullable();
            $table->enum('info_status', ['site_under_construction', 'nothing_on_internet', 'is_closed', 'website_has_contact_form', 'no_website_facebook', 'has_only_facebook', 'email_exists'])->nullable();
            $table->text('remarks')->nullable();
            $table->text('open_remarks')->nullable();
            $table->text('internal_remarks')->nullable();
            $table->text('short_description')->nullable();
            $table->text('public_zoos_relation')->nullable();
            $table->text('animal_related_association')->nullable();
            $table->boolean('is_approved')->default(false);
            $table->timestamps();

            $table->foreign('organisation_type')
                ->references('key')
                ->on('organisation_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('organisations');
    }
}
