<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveIndices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::table('contacts', function (Blueprint $table) {
            $table->dropForeign('contacts_organisation_id_foreign');
            $table->dropIndex('contacts_organisation_id_foreign');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign('invoices_invoice_contact_id_foreign');
            $table->dropIndex('invoices_contact_id_foreign');
        });

        Schema::table('offers', function (Blueprint $table) {
            $table->dropForeign('offers_client_id_foreign');
            $table->dropIndex('offers_client_id_foreign');
            $table->dropForeign('offers_institution_id_foreign');
            $table->dropIndex('offers_institution_id_foreign');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign('orders_client_id_foreign');
            $table->dropIndex('orders_client_id_foreign');
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();

        Schema::table('contacts', function (Blueprint $table) {
            $table->index('organisation_id', 'contacts_organisation_id_foreign');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->index('invoice_contact_id', 'invoices_invoice_contact_id_foreign');
        });

        Schema::table('offers', function (Blueprint $table) {
            $table->index('client_id', 'offers_client_id_foreign');
            $table->index('institution_id', 'offers_institution_id_foreign');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->index('client_id', 'orders_client_id_foreign');
        });

        Schema::enableForeignKeyConstraints();
    }
}
