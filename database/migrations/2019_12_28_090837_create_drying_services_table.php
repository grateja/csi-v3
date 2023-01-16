<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDryingServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('drying_services')) {
            Schema::create('drying_services', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('name');
                $table->string('description')->nullable();
                $table->string('img_path')->nullable();
                $table->double('price')->default(0);
                // $table->double('price_per_load')->default(0);
                $table->string('machine_type')->comment('REGULAR, TITAN');
                $table->integer('minutes')->comment('Must be divisible by 10');
                $table->double('points')->default(0);

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
        Schema::dropIfExists('drying_services');
    }
}
