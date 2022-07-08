<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->date('date');
            $table->string('title');
            $table->text('description')->nullable();
            $table->boolean('published')->default(false);
            $table->unsignedInteger('event_type_id')->nullable();

            $table->timestamps();

            $table->foreign('event_type_id')->references('id')->on('event_types')->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('events');
    }
}
