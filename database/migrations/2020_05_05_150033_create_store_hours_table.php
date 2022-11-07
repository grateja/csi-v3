<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoreHoursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('store_hours')) {
            Schema::create('store_hours', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->integer('day_index')->comment('1 - Monday, 7 - sunday');
                $table->string('opens_at')->nullable();
                $table->string('closes_at')->nullable();
                $table->timestamp('synched')->nullable();
                $table->timestamps();
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
        Schema::dropIfExists('store_hours');
    }
}
