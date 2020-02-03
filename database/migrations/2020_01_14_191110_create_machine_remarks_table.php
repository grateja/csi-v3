<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMachineRemarksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('machine_remarks', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('title');
            $table->text('remarks');
            $table->uuid('user_id')->nullable();
            $table->uuid('machine_id');
            $table->integer('remaining_time');

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('CASCADE');
            $table->foreign('machine_id')->references('id')->on('machines')->onDelete('CASCADE')->onUpdate('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('machine_remarks');
    }
}
