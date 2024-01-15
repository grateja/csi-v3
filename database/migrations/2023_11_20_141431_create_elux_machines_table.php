<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEluxMachinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('elux_machines')) {
            Schema::create('elux_machines', function (Blueprint $table) {
                $table->uuid('id')->primary();

                $table->string('machine_name')->nullable();
                $table->string('machine_type')->nullable()->comment('washer/dryer');
                $table->string('model')->nullable();
                $table->string('ip_address')->nullable();
                $table->string('stack_order')->default(0)->nullable();
                $table->string('customer_name')->nullable();

                $table->timestamp('time_activated')->nullable();
                $table->integer('total_minutes')->default(0);

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
        Schema::dropIfExists('elux_machines');
    }
}
