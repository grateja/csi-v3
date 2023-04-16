<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnCustomerWashDryTries extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_washes', function (Blueprint $table) {
            if (!Schema::hasColumn('customer_washes', 'tries')) {
                $table->integer('tries')->after('used')->default(0)->nullable();
            }
        });
        Schema::table('customer_dries', function (Blueprint $table) {
            if (!Schema::hasColumn('customer_dries', 'tries')) {
                $table->integer('tries')->after('used')->default(0)->nullable();
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
