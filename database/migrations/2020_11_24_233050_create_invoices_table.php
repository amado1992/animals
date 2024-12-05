<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('cascade');
            $table->enum('payment_type', ['deposit', 'balance'])->nullable();
            $table->enum('invoice_type', ['credit', 'debit'])->nullable();
            $table->foreignId('bank_account_id')->nullable()->constrained();
            $table->integer('bank_account_number')->nullable();
            $table->foreignId('invoice_contact_id')->nullable()->constrained('contacts');
            $table->string('invoice_currency', 5)->nullable();
            $table->double('invoice_percent')->nullable();
            $table->double('invoice_amount')->nullable();
            $table->double('paid_value')->nullable();
            $table->double('banking_cost')->nullable();
            $table->timestamp('invoice_date')->nullable();
            $table->timestamp('paid_date')->nullable();
            $table->enum('invoice_from', ['species', 'transport', 'crates', 'transport_crates', 'species_crates', 'fixed_costs', 'all'])->nullable();
            $table->string('invoice_file', 255)->nullable();
            $table->string('remark', 255)->nullable();
            $table->boolean('belong_to_order')->default(true);
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
        Schema::dropIfExists('invoices');
    }
}
