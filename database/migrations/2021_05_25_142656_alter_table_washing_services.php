<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableWashingServices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('washing_services', function (Blueprint $table) {
            if(!Schema::hasColumn('washing_services', 'quick_minutes')) {
                $table->double('quick_minutes')->after('regular_minutes')->default(0);
            }

            if(!Schema::hasColumn('washing_services', 'more_rinse_minutes')) {
                $table->double('more_rinse_minutes')->after('quick_minutes')->default(0);
            }

            if(!Schema::hasColumn('washing_services', 'premium_minutes')) {
                $table->double('premium_minutes')->after('more_rinse_minutes')->default(0);
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
