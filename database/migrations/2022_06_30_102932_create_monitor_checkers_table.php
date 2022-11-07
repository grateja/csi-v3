<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMonitorCheckersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('monitor_checkers')) {
            Schema::create('monitor_checkers', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('transaction_id')->nullable();
                $table->string('job_order')->nullable();
                $table->string('action')->nullable()->comment('iddle|hasQue');
                $table->string('token')->nullable();

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
        Schema::dropIfExists('monitor_checkers');
    }
}
