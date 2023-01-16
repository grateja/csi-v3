<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFullServiceItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('full_service_items')) {
            Schema::create('full_service_items', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('full_service_id');

                $table->string('category')->comment('washing, drying, other');

                $table->uuid('washing_service_id')->nullable();
                $table->uuid('drying_service_id')->nullable();
                $table->uuid('other_service_id')->nullable();

                $table->string('name');
                $table->integer('quantity')->default(1);
                $table->float('price')->default(0);
                $table->float('points')->default(0);

                $table->timestamps();
                $table->softDeletes();
                $table->timestamp('synched')->nullable();

                $table->foreign('full_service_id')->references('id')->on('full_services')->onDelete('CASCADE')->onUpdate('CASCADE');
                $table->foreign('washing_service_id')->references('id')->on('washing_services')->onDelete('CASCADE')->onUpdate('CASCADE');
                $table->foreign('drying_service_id')->references('id')->on('drying_services')->onDelete('CASCADE')->onUpdate('CASCADE');
                $table->foreign('other_service_id')->references('id')->on('other_services')->onDelete('CASCADE')->onUpdate('CASCADE');
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
        Schema::dropIfExists('full_service_items');
    }
}
