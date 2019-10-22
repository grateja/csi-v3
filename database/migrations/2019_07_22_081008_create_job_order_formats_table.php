<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJobOrderFormatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('job_order_formats', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->integer('char_count')->default(5);
            $table->string('prefix')->nullable()->default('#')->comment('Starts with #');
            $table->integer('start_number')->default(1)->comment('The next item will be inserted starts with 1');
            $table->string('format')->default('#%05d')->comment('Leading 5 zeros before the first digits');

            $table->timestamps();
            $table->softDeletes();
            $table->timestamp('synched')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('job_order_formats');
    }
}
