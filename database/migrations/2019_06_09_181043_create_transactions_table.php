<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->dateTime('date')->nullable();
            $table->uuid('customer_id')->nullable();
            $table->uuid('user_id')->nullable()->comment('user who saved the transaction');
            $table->string('job_order')->nullable();
            $table->dateTime('saved')->nullable();

            $table->softDeletes();
            $table->timestamp('synched')->nullable();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('CASCADE');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
