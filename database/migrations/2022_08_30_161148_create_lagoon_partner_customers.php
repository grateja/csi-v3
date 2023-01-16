<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLagoonPartnerCustomers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('lagoon_partner_customers')) {
            Schema::create('lagoon_partner_customers', function (Blueprint $table) {
                $table->uuid('customer_id');
                $table->uuid('lagoon_partner_id');

                $table->foreign('customer_id')->references('id')->on('customers')->onUpdate('CASCADE')->onDelete('CASCADE');
                $table->foreign('lagoon_partner_id')->references('id')->on('lagoon_partners')->onUpdate('CASCADE')->onDelete('CASCADE');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lagoon_partner_customers');
    }
}
