<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsTransactionPayments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transaction_payments', function (Blueprint $table) {
            if (!Schema::hasColumn('transaction_payments', 'cash_less_provider')) {
                $table->string('cash_less_provider')->after('discount_name')->default(null)->nullable();
            }

            if (!Schema::hasColumn('transaction_payments', 'cash_less_reference_number')) {
                $table->string('cash_less_reference_number')->after('cash_less_provider')->default(null)->nullable();
            }

            if (!Schema::hasColumn('transaction_payments', 'cash_less_amount')) {
                $table->double('cash_less_amount')->after('cash_less_reference_number')->default(null)->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
