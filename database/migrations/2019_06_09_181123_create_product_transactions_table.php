<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('transaction_id')->nullable();
            $table->uuid('added_by')->nullable()->comment('user id who added the transaction');
            $table->uuid('product_id')->nullable();
            $table->boolean('saved')->default(false);

            $table->softDeletes();

            $table->timestamps();

            $table->foreign('added_by')->references('id')->on('users')->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('CASCADE');
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
        Schema::dropIfExists('transaction_items');
    }
}
