<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOutSourceMachineUsageLinensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('out_source_machine_usage_linens', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('out_source_machine_usage_id');
            $table->string('name');
            $table->double('quantity');

            $table->timestamp('synched');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('out_source_machine_usage_id', 'foreign_out_source_machine_usage_id')->references('id')->on('out_source_machine_usages')->onDelete('CASCADE')->onUpdate('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('out_source_machine_usage_linens');
    }
}
