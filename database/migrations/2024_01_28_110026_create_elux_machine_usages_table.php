<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEluxMachineUsagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('elux_machine_usages')) {
            Schema::create('elux_machine_usages', function (Blueprint $table) {
                $table->uuid('id')->primary();

                $table->uuid('elux_machine_id');
                $table->string('customer_name')->nullable();
                $table->double('minutes');
                $table->float('price')->default(0);
                $table->text('remarks')->nullable();
                $table->timestamp('synched')->nullable();

                $table->timestamps();
                $table->foreign('elux_machine_id')->references('id')->on('elux_machines')->onDelete('CASCADE')->onUpdate('CASCADE');
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
        Schema::dropIfExists('elux_machine_usages');
    }
}
