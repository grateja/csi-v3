<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLagoonPartnersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('lagoon_partners')) {
            Schema::create('lagoon_partners', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('shop_name')->nullable();
                $table->string('address')->nullable();
                $table->string('contact_number')->nullable();
                $table->timestamps();
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
        Schema::dropIfExists('lagoon_partners');
    }
}
