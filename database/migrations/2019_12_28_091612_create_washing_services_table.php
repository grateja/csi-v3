<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWashingServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('washing_services', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('img_path')->nullable();
            $table->double('price')->default(0);
            // $table->double('price_per_load')->default(0);
            $table->string('machine_type')->comment('REGULAR, TITAN');
            $table->integer('regular_minutes')->default(0);
            $table->integer('additional_minutes')->default(0);
            $table->double('points')->default(0);

            $table->timestamps();
            $table->softDeletes();
            $table->timestamp('synched')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('washing_services');
    }
}
