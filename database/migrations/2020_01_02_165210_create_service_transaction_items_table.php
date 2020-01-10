<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServiceTransactionItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_transaction_items', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('transaction_id');

            $table->string('name');
            $table->double('price')->default(0);
            $table->string('category')->comment('washing, drying, other, full');

            $table->uuid('washing_service_id')->nullable();
            $table->uuid('drying_service_id')->nullable();
            $table->uuid('other_service_id')->nullable();
            $table->uuid('full_service_id')->nullable();
            $table->boolean('saved')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->foreign('washing_service_id')->references('id')->on('washing_services')->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->foreign('drying_service_id')->references('id')->on('drying_services')->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->foreign('other_service_id')->references('id')->on('other_services')->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->foreign('full_service_id')->references('id')->on('full_services')->onDelete('CASCADE')->onUpdate('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('service_transaction_items');
    }
}
