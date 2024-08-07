<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePartialPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('partial_payments')) {
            Schema::create('partial_payments', function (Blueprint $table) {
                $table->uuid('id')->primary()->unique();
                $table->uuid('transaction_id')->nullable();
                $table->uuid('customer_id')->nullable();
                $table->timestamp('date')->nullable()->default(DB::raw('CURRENT_TIMESTAMP'));
                $table->double('cash')->nullable()->default(0);
                $table->string('or_number')->nullable();
                $table->double('points')->nullable()->comment('Customer loyalty points used')->default(0);
                $table->double('points_in_peso')->nullable()->comment('Points in peso used during payment')->default(0);
                $table->double('card_load_used')->nullable()->comment('Amount of card load used')->default(0);
                $table->string('rfid')->nullable()->comment('Card used');
                $table->double('discount')->nullable()->comment('Percentage')->default(0);
                $table->double('total_amount')->nullable()->default(0);
                $table->double('balance')->nullable()->default(0);
                $table->double('change')->nullable()->default(0);
                $table->double('total_cash')->nullable()->default(0);
                $table->string('paid_to')->nullable()->comment('Static name of bantay');
                $table->uuid('user_id')->nullable()->comment('Bantay');

                $table->softDeletes();
                $table->timestamps();
                $table->timestamp('synched')->nullable();

                $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('CASCADE')->onUpdate('CASCADE');
                $table->foreign('customer_id')->references('id')->on('customers')->onDelete('SET NULL')->onUpdate('CASCADE');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('CASCADE');
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
        Schema::dropIfExists('partial_payments');
    }
}
