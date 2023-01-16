<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePerKiloWashDriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('per_kilo_wash_dries')) {
            Schema::create('per_kilo_wash_dries', function (Blueprint $table) {
                $table->uuid('id')->primary();

                $table->string('name')->nullable();
                $table->double('delicate_price')->nullable();
                $table->double('warm_price')->nullable();
                $table->double('hot_price')->nullable();
                $table->double('superwash_price')->nullable();

                $table->string('img_path')->nullable();

                $table->timestamps();
                $table->softDeletes();
                $table->timestamp('synched')->nullable();
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
        Schema::dropIfExists('per_kilo_wash_dries');
    }
}
