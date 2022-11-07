<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('products')) {
            Schema::create('products', function (Blueprint $table) {
                $table->uuid('id')->primary();

                $table->string('name');
                $table->string('description')->nullable();
                $table->string('img_path')->nullable();
                $table->double('selling_price')->nullable();
                $table->integer('current_stock')->nullable();

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
        Schema::dropIfExists('products');
    }
}
