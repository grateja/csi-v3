<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExpensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('expenses')) {
            Schema::create('expenses', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->date('date')->nullable();
                $table->text('remarks')->nullable();
                $table->double('amount')->nullable();
                $table->string('expense_type')->nullable()->comment('Free text. Can add what ever type user may want to add');
                $table->uuid('staff_name')->nullable();
                $table->timestamps();
                $table->softDeletes();
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
        Schema::dropIfExists('expences');
    }
}
