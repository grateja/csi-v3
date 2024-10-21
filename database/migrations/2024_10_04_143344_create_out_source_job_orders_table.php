<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOutSourceJobOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('out_source_job_orders', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('out_source_id');
            $table->string('job_order_number');
            $table->uuid('user_id')->remarks('created_by');
            $table->uuid('out_source_statement_of_account_id')->nullable();
            $table->double('total_amount')->default(0);

            $table->timestamp('synched');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('out_source_id')->references('id')->on('out_sources')->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->foreign('out_source_statement_of_account_id', 'jo_foreign_soa_id')->references('id')->on('out_source_statement_of_accounts')->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('CASCADE')->onUpdate('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('out_source_job_orders');
    }
}
