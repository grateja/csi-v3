<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionRemarksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('transaction_remarks')) {
            Schema::create('transaction_remarks', function (Blueprint $table) {
                $table->uuid('id')->primary();

                $table->uuid('transaction_id');
                $table->text('remarks');
                $table->string('added_by');

                $table->timestamps();
                $table->softDeletes();
                $table->timestamp('synched')->nullable();

                $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('CASCADE')->onUpdate('CASCADE');
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
        Schema::dropIfExists('transaction_remarks');
    }
}
