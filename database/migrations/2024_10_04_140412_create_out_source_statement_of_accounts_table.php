<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOutSourceStatementOfAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('out_source_statement_of_accounts', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('out_source_id');
            $table->string('soa_number');
            $table->text('remarks')->nullable();
            $table->double('penalty')->default(0);
            $table->double('vat')->default(0);
            $table->double('total_amount')->default(0);
            $table->integer('total_count')->default(0);

            $table->timestamp('synched')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('out_source_id')->references('id')->on('out_sources')->onUpdate('CASCADE')->onUpdate('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('out_source_statement_of_accounts');
    }
}
