<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentRemarksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_remarks', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('transaction_payment_id')->nullable();
            $table->uuid('user_id')->nullable()->comment('User who added the remarks');
            $table->text('remarks')->nullable();

            $table->timestamps();
            $table->softDeletes();
            $table->timestamp('synched')->nullable();

            $table->foreign('transaction_payment_id')->references('transaction_id')->on('transaction_payments')->onDelete('CASCADE');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_remarks');
    }
}
