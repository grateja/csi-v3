<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServiceTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('transaction_id')->nullable();
            $table->uuid('added_by')->nullable()->comment('user id who added the transaction');
            $table->uuid('service_id')->nullable();
            $table->boolean('saved')->default(false);

            $table->softDeletes();

            $table->timestamps();

            $table->foreign('added_by')->references('id')->on('users')->onDelete('CASCADE');
            $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('CASCADE');
            $table->foreign('service_id')->references('id')->on('services')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('service_transactions');
    }
}
