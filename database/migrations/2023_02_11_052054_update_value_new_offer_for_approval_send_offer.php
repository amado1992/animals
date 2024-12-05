<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateValueNewOfferForApprovalSendOffer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement("UPDATE offers SET new_offer_forapproval = 0 WHERE new_offer_forapproval = 1 AND status_level = 'Sendoffer'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \DB::statement('UPDATE offers SET new_offer_forapproval = 0 WHERE new_offer_forapproval = 0 ');
    }
}
