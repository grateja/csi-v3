<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEluxServiceTransactionItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('elux_service_transaction_items')) {
            Schema::create('elux_service_transaction_items', function (Blueprint $table) {
                $table->uuid('id')->primary();

                $table->uuid('transaction_id')->nullable();
                $table->uuid('elux_service_id')->nullable();

                $table->string('service_type')->comment('washer/dryer')->nullable();
                $table->string('name')->nullable();
                $table->double('price')->default(0);
                $table->integer('pulse_count')->default(0);
                $table->string('model')->nullable();
                $table->string('minutes')->default(0);
                $table->boolean('saved')->default(false);

                $table->timestamps();
                $table->softDeletes();
                $table->timestamp('synched')->nullable();

                $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('SET NULL')->onUpdate('CASCADE');
                $table->foreign('elux_service_id')->references('id')->on('elux_services')->onDelete('SET NULL')->onUpdate('CASCADE');
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
        Schema::dropIfExists('elux_service_transaction_items');
    }
}
