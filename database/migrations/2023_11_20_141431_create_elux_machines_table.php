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

                $table->string('machine_type')->nullable()->comment('washer/dryer');
                $table->string('model');
                $table->string('capacity');
                $table->string('ip_address');
                $table->string('stack_order');
                $table->string('user_name');
                $table->string('customer_name');

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
