<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServiceDisposalLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_disposal_logs', function (Blueprint $table) {
            $table->uuid('id');
            $table->uuid('customer_wash_id')->nullable();
            $table->uuid('customer_dry_id')->nullable();
            $table->text('remarks');
            $table->uuid('user_id');
            $table->timestamps();

            $table->foreign('customer_wash_id')->references('id')->on('customer_washes')->onDelete('SET NULL')->onUpdate('CASCADE');
            $table->foreign('customer_dry_id')->references('id')->on('customer_dries')->onDelete('SET NULL')->onUpdate('CASCADE');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('service_disposal_logs');
    }
}
