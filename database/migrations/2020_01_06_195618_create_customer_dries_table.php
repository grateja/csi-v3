<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomerDriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('customer_dries')) {
            Schema::create('customer_dries', function (Blueprint $table) {
                $table->uuid('id')->primary();

                $table->uuid('customer_id');
                $table->uuid('service_transaction_item_id');
                $table->string('service_name');
                $table->string('dryer_name')->nullable();
                $table->string('machine_type')->comment('TITAN, REGULAR');
                $table->integer('pulse_count');
                $table->integer('minutes');
                $table->float('price')->default(0);
                $table->dateTime('used')->nullable();
                $table->string('staff_name')->nullable()->comment('staff who activates the service');
                $table->string('job_order');

                $table->timestamps();
                $table->softDeletes();
                $table->timestamp('synched')->nullable();

                $table->foreign('customer_id')->references('id')->on('customers')->onDelete('CASCADE')->onUpdate('CASCADE');
                $table->foreign('service_transaction_item_id')->references('id')->on('service_transaction_items')->onDelete('CASCADE')->onUpdate('CASCADE');
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
        Schema::dropIfExists('customer_dries');
    }
}
