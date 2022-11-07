<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWasherTapCardConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('washer_tap_card_configs')) {
            Schema::create('washer_tap_card_configs', function (Blueprint $table) {
                $table->uuid('id')->primary();

                $table->integer('quick_minutes')->default(24);
                $table->integer('regular_minutes')->default(38);
                $table->integer('more_rinse_minutes')->default(40);
                $table->integer('premium_minutes')->default(46);
                $table->integer('additional_minutes')->default(52);

                $table->integer('quick_price')->default(60);
                $table->integer('regular_price')->default(65);
                $table->integer('more_rinse_price')->default(70);
                $table->integer('premium_price')->default(75);
                $table->integer('additional_price')->default(80);

                $table->timestamp('synched')->nullable();
                $table->timestamps();
                $table->softDeletes();

                // Testing
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
        Schema::dropIfExists('washer_tap_card_configs');
    }
}
