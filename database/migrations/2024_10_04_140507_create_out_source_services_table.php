<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOutSourceServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('out_source_services', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('name');
            $table->string('description')->nullable();
            $table->integer('pulse_count');
            $table->integer('minutes');

            $table->timestamp('synched');
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
        Schema::dropIfExists('out_source_services');
    }
}
