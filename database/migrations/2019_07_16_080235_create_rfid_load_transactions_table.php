<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRfidLoadTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('rfid_load_transactions')) {
            Schema::create('rfid_load_transactions', function (Blueprint $table) {
                $table->uuid('id')->primary();

                $table->uuid('rfid_card_id')->nullable();
                $table->string('customer_name');
                $table->string('rfid');
                $table->double('amount')->nullable();
                $table->double('remaining_balance')->nullable()->comment('Remaining balance before loading.');
                $table->double('current_balance')->nullable()->comment('Balance after loading.');
                $table->double('cash')->nullable()->comment('Amount paid.');
                $table->double('change')->nullable();
                $table->string('staff_name')->nullable();
                $table->text('remarks')->nullable();

                $table->timestamps();
                $table->softDeletes();
                $table->timestamp('synched')->nullable();

                $table->foreign('rfid_card_id')->references('id')->on('rfid_cards')->onDelete('CASCADE')->onUpdate('CASCADE');
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
        Schema::dropIfExists('rfid_load_transactions');
    }
}
