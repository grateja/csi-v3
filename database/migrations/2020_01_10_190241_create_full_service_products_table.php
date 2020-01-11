<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFullServiceProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('full_service_products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('full_service_id');

            $table->uuid('product_id')->nullable();

            $table->string('name');
            $table->integer('quantity')->default(1);
            $table->float('price')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('full_service_id')->references('id')->on('full_services')->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('CASCADE')->onUpdate('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('full_service_products');
    }
}
