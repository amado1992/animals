<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBankAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->string('iban', 50);
            $table->string('currency', 5);
            $table->string('company_name', 50);
            $table->string('company_address');

            $table->string('beneficiary_name', 50);
            $table->string('beneficiary_fullname', 50);
            $table->string('beneficiary_address');
            $table->string('beneficiary_account', 50);
            $table->string('beneficiary_swift', 50);
            $table->string('beneficiary_aba', 50)->nullable();

            $table->string('correspondent_name', 50)->nullable();
            $table->string('correspondent_address')->nullable();
            $table->string('correspondent_swift', 50)->nullable();
            $table->string('beneficiary_account_in_correspondent', 50)->nullable();

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
        Schema::dropIfExists('bank_accounts');
    }
}
