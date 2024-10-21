<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOutSourceJobOrderLinensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('out_source_job_order_linens', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('out_source_job_order_id');
            $table->uuid('out_source_linen_id');

            $table->string('category')->default('other');
            $table->string('name');
            $table->string('degree_of_soil')->remarks('regular_price,with_stain_light,with_stain_medium,with_stain_heavy');
            $table->double('unit_price')->default(0);
            $table->double('quantity')->default(0);

            $table->timestamp('synched');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('out_source_job_order_id')->references('id')->on('out_source_job_orders')->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->foreign('out_source_linen_id')->references('id')->on('out_source_linens')->onDelete('CASCADE')->onUpdate('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('out_source_job_order_linens');
    }
}
