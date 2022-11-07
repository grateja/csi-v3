<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReworksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('reworks')) {
            Schema::create('reworks', function (Blueprint $table) {
                $table->uuid('id')->primary();

                $table->text('remarks')->nullable();
                $table->string('customer_name')->nullable();
                $table->string('tag')->comment('transfer|reload');
                $table->uuid('machine_id')->nullable();
                $table->string('job_order')->nullable();
                $table->string('account_name')->nullable();

                $table->softDeletes();
                $table->timestamps();
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
        Schema::dropIfExists('reworks');
    }
}
