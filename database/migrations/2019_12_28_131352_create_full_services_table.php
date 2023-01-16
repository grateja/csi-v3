<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFullServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('full_services')) {
            Schema::create('full_services', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('name');
                $table->float('additional_charge')->default(0);
                $table->float('discount')->default(0);

                $table->timestamps();
                $table->softDeletes();
                $table->timestamp('synched')->nullable();
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
        Schema::dropIfExists('full_services');
    }
}
