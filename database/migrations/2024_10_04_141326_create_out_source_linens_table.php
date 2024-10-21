<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOutSourceLinensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('out_source_linens', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('out_source_id');
            $table->string('category')->default('other');
            $table->string('name');
            $table->double('regular_price')->default(0);
            $table->double('with_stain_light')->default(0);
            $table->double('with_stain_medium')->default(0);
            $table->double('with_stain_heavy')->default(0);
            $table->double('dry_weight')->default(0);

            $table->timestamp('synched');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('out_source_id')->references('id')->on('out_sources')->onDelete('CASCADE')->onUpdate('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('out_source_linens');
    }
}
