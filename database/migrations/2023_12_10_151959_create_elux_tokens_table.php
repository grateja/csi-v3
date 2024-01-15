<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEluxTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('elux_tokens')) {
            Schema::create('elux_tokens', function (Blueprint $table) {
                $table->uuid('id')->primary();

                $table->uuid('customer_id')->nullable();
                $table->uuid('elux_service_transaction_item_id')->nullable();
                $table->uuid('elux_machine_id')->nullable();

                $table->string('service_type')->comment('washer/dryer')->nullable();
                $table->string('name')->nullable();
                $table->double('price')->default(0);
                $table->integer('pulse_count')->default(0);
                $table->string('model')->nullable();
                $table->string('minutes')->default(0);
                $table->timestamp('used')->nullable();

                $table->timestamps();
                $table->softDeletes();
                $table->timestamp('synched')->nullable();

                $table->foreign('elux_service_transaction_item_id')->references('id')->on('elux_service_transaction_items')->onDelete('SET NULL')->onUpdate('CASCADE');
                $table->foreign('elux_machine_id')->references('id')->on('elux_machines')->onDelete('SET NULL')->onUpdate('CASCADE');
                $table->foreign('customer_id')->references('id')->on('customers')->onDelete('SET NULL')->onUpdate('CASCADE');
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
        Schema::dropIfExists('elux_tokens');
    }
}
