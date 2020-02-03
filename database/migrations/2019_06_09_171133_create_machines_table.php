<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateMachinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('machines', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('ip_address')->nullable();

            $table->string('machine_type')->nullable()->comment('rw = regular washer, rd = regular dryer, tw = titan washer, td = titan dryer');
            $table->string('machine_name')->nullable();
            $table->timestamp('time_activated')->nullable();
            $table->integer('total_minutes')->default(0);
            $table->integer('initial_time')->comment('initial pulse')->nullable();
            $table->integer('additional_time')->comment('additional pulse')->nullable();
            $table->double('initial_price')->nullable();
            $table->double('additional_price')->nullable();
            $table->integer('initial_cycle_count')->nullable();
            $table->uuid('customer_id')->nullable();
            $table->text('remarks')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('SET NULL')->onUpdate('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('machines');
    }
}
