<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRfidCardTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rfid_card_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('rfid');
            $table->string('machine_name');
            $table->string('owner_name');
            $table->float('price')->default(0);
            $table->integer('minutes');

            $table->uuid('machine_id')->nullable();
            $table->uuid('rfid_card_id')->nullable();

            $table->timestamp('synched')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('machine_id')->references('id')->on('machines')->onDelete('SET NULL')->onUpdate('CASCADE');
            $table->foreign('rfid_card_id')->references('id')->on('rfid_cards')->onDelete('SET NULL')->onUpdate('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rfid_card_transactions');
    }
}
