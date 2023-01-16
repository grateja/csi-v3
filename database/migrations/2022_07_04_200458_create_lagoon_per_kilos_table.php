<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLagoonPerKilosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('lagoon_per_kilos')) {
            Schema::create('lagoon_per_kilos', function (Blueprint $table) {
                $table->uuid('id')->primary();

                $table->string('name')->nullable();
                $table->double('price_per_kilo')->nullable()->default(0);

                $table->softDeletes();
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
        Schema::dropIfExists('lagoon_per_kilos');
    }
}
