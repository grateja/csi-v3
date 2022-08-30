<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLagoonPartnerTransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lagoon_partner_transactions', function (Blueprint $table) {
            $table->uuid('transaction_id');
            $table->uuid('lagoon_partner_id');

            $table->foreign('transaction_id')->references('id')->on('transactions')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('lagoon_partner_id')->references('id')->on('lagoon_partners')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lagoon_partner_transactions');
    }
}
