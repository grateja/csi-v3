<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('videos')) {
            Schema::create('videos', function (Blueprint $table) {
                $table->uuid('id')->primary();

                $table->uuid('event_id');
                $table->text('source');
                $table->string('title')->nullable();
                $table->text('description')->nullable();
                $table->integer('order')->nullable();

                $table->timestamps();


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
        Schema::dropIfExists('videos');
    }
}
