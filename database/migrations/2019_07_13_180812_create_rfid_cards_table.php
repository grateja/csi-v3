<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRfidCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('rfid_cards')) {
            Schema::create('rfid_cards', function (Blueprint $table) {
                $table->uuid('id')->primary();

                $table->string('rfid')->unique()->nullable();
                $table->double('balance')->default(0);
                $table->uuid('customer_id')->nullable()->comment('If issued to customer');
                $table->uuid('user_id')->nullable()->comment('For master card');
                $table->char('card_type')->default('c')->comment('c = customer, u = user');

                $table->timestamps();
                $table->softDeletes();
                $table->timestamp('synched')->nullable();

                $table->foreign('customer_id')->references('id')->on('customers')->onDelete('CASCADE');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('CASCADE')->onUpdate('CASCADE');
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
        Schema::dropIfExists('rfid_cards');
    }
}
