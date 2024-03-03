<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEluxServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('elux_services')) {
            Schema::create('elux_services', function (Blueprint $table) {
                $table->uuid('id')->primary();

                $table->string('service_type')->comment('washer/dryer')->nullable();
                $table->string('name')->nullable();
                $table->double('price')->default(0);
                $table->integer('pulse_count')->default(0);
                $table->string('model')->nullable();
                $table->string('minutes')->default(0);

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
        Schema::dropIfExists('elux_services');
    }
}
