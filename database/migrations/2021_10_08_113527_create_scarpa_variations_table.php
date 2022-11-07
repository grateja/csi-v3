<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateScarpaVariationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('scarpa_variations')) {
            Schema::create('scarpa_variations', function (Blueprint $table) {
                $table->uuid('id')->primary();

                $table->uuid('scarpa_category_id');

                $table->string('action')->default('ANY');
                $table->double('selling_price')->default(0);

                $table->softDeletes();
                $table->timestamps();
                $table->timestamp('synched')->nullable();

                $table->foreign('scarpa_category_id')->references('id')->on('scarpa_categories')->onUpdate('CASCADE')->onDelete('CASCADE');
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
        Schema::dropIfExists('scarpa_variations');
    }
}
