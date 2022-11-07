<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAnnouncementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('announcements')) {
            Schema::create('announcements', function (Blueprint $table) {
                $table->uuid('id')->primary();

                $table->text('content')->nullable();
                $table->date('date_from')->nullable();
                $table->date('date_until')->nullable();
                $table->boolean('marquee_on')->default(0);

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
        Schema::dropIfExists('announcements');
    }
}
