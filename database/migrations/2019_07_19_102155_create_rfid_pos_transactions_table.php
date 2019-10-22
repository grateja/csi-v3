<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRfidPosTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rfid_pos_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('rfid_card_id')->nullable();
            $table->text('remarks')->nullable();
            $table->double('amount_deducted')->nullable();
            $table->uuid('user_id')->nullable();
            $table->uuid('transaction_id')->nullable();


            $table->timestamps();
            $table->softDeletes();
            $table->timestamp('synched')->nullable();

            $table->foreign('rfid_card_id')->references('id')->on('rfid_cards')->onDelete('CASCADE');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rfid_pos_transactions');
    }
}
