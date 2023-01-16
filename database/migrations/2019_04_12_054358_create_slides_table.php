<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSlidesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('slides')) {
            Schema::create('slides', function (Blueprint $table) {
                $table->uuid('id')->primary();

                $table->string('source');
                $table->integer('order');
                $table->string('caption')->nullable();
                $table->text('description')->nullable();
                $table->uuid('event_id');

                $table->foreign('event_id')->references('id')->on('events')->onDelete('CASCADE');
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
        Schema::dropIfExists('images');
    }
}
