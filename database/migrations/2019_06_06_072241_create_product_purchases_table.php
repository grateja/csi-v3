<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductPurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_purchases', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->dateTime('date')->nullable();
            $table->uuid('product_id')->nullable();
            $table->string('product_name')->nullable();
            $table->string('receipt')->nullable();
            $table->integer('quantity')->nullable();
            $table->double('unit_cost')->nullable();
            $table->text('remarks')->nullable();
            $table->string('staff_name')->nullable();

            $table->softDeletes();

            $table->timestamp('synched')->nullable();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_purchases');
    }
}
