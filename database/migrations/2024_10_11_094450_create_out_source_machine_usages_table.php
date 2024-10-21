<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOutSourceMachineUsagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('out_source_machine_usages', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('machine_id')->nullable();
            $table->uuid('elux_machine_id')->nullable();
            $table->uuid('user_id');
            $table->uuid('out_source_id');
            $table->uuid('out_source_service_id');
            $table->integer('minutes');

            $table->timestamp('synched');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('machine_id')->references('id')->on('machines')->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->foreign('elux_machine_id')->references('id')->on('elux_machines')->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->foreign('out_source_id')->references('id')->on('out_sources')->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->foreign('out_source_service_id', 'foreign_out_source_service_id')->references('id')->on('out_source_services')->onDelete('CASCADE')->onUpdate('CASCADE');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('out_source_machine_usages');
    }
}
