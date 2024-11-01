<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMachineUsagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('machine_usages')) {
            Schema::create('machine_usages', function (Blueprint $table) {
                $table->uuid('id')->primary();

                $table->uuid('machine_id');
                $table->string('customer_name')->nullable();
                $table->double('minutes');
                $table->string('activation_type')->conmment('card, remote')->nullable();
                $table->float('price')->default(0);
                $table->text('remarks')->nullable();
                $table->timestamp('synched')->nullable();

                $table->timestamps();
                $table->foreign('machine_id')->references('id')->on('machines')->onDelete('CASCADE')->onUpdate('CASCADE');
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
        Schema::dropIfExists('machine_usages');
    }
}
